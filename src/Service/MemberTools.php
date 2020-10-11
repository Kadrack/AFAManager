<?php
// src/Service/MemberTools.php
namespace App\Service;

use App\Entity\Grade;
use App\Entity\GradeSession;
use App\Entity\Member;
use App\Entity\MemberLicence;

use DateTime;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class Tools
 * @package App\Service
 */
class MemberTools
{
    private $em;

    private $grades;

    private $licences;

    private $member;

    private $stages;

    /**
     * MemberTools constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;

        $this->grades   = null;
        $this->licences = null;
        $this->stages   = null;
    }

    public function getMember()
    {
        return $this->member;
    }

    public function setMember(Member $member)
    {
        $this->member = $member;

        return $this->member;
    }

    public function getGrades(): ?array
    {
        if ($this->grades != null)
        {
            return $this->grades;
        }

        $grade_history = $this->em->getRepository(Grade::class)->getGradeHistory($this->member->getMemberId());

        $this->grades['history'] = $grade_history;

        for ($i = 0; $i < sizeof($this->grades['history']); $i++)
        {
            if ($this->grades['history'][$i]['Type'] == null)
            {
                $this->grades['history'][$i]['Type'] = 1;
            }
        }

        $this->grades['exam'] = $this->isCandidateDan();
        $this->grades['kyu']  = $this->isCandidateKyu();

        return $this->grades;
    }

    private function isCandidateKyu(): bool
    {
        $count_kyus = 0;

        for ($i = 0; $i < sizeof($this->grades['history']); $i++)
        {
            if ($this->grades['history'][$i]['Rank'] < 7)
            {
                $count_kyus++;
            }
        }

        $count_kyus >= 6 ? $kyu_candidate = false : $kyu_candidate = true;

        return $kyu_candidate;
    }

    private function isCandidateDan(): array
    {
        $today = new DateTime('today');

        $open_session = $this->em->getRepository(GradeSession::class)->getOpenSession($today->format('Y-m-d'));

        if ($open_session == null)
        {
            $is_candidate = false;
        }
        elseif ($this->grades['history'][0] != null)
        {
            if (($this->grades['history'][0]['Result'] == 2) || ($this->grades['history'][0]['Result'] == 6) || ($this->grades['history'][0]['Rank'] >= 14))
            {
                $is_candidate = false;
            }
            elseif ($this->grades['history'][0]['Session'] == $open_session[0]->getGradeSessionId())
            {
                $is_candidate = false;
            }
            else
            {
                $is_candidate = true;
            }
        }
        else
        {
            $is_candidate = true;
        }

        if ($is_candidate)
        {
            $next_grade = $this->nextGrade($open_session[0]);
        }
        else
        {
            $next_grade = 0;
        }

        return array('candidate' => $is_candidate, 'grade' => $next_grade);
    }

    private function nextGrade(GradeSession $session): ?Grade
    {
        $rank = 0;

        if ($this->member->getMemberLastGrade()->getGradeRank() <= 6)
        {
            $rank = 7;
        }
        elseif ($this->member->getMemberLastGrade()->getGradeStatus() == 3)
        {
            $rank = $this->member->getMemberLastGrade()->getGradeRank();
        }
        elseif ($this->member->getMemberLastGrade()->getGradeStatus() == 5)
        {
            $rank = $this->member->getMemberLastGrade()->getGradeRank() + 1;
        }

        if ($rank != 0)
        {
            $grade = new Grade();

            $grade->setGradeClub($this->member->getMemberActualClub());
            $grade->setGradeDate(new DateTime('today'));
            $grade->setGradeExam($session);
            $grade->setGradeMember($this->member);
            $grade->setGradeRank($rank);
            $grade->setGradeStatus(1);

            return $grade;
        }
        else
        {
            return null;
        }

    }

    public function getLicences(): ?array
    {
        if ($this->licences != null)
        {
            return $this->licences;
        }

        $licence_history = $this->em->getRepository(MemberLicence::class)->findBy(['member_licence' => $this->member->getMemberId()], ['member_licence_id' => 'DESC']);

        $today = new DateTime('Today');

        if ($licence_history[0]->getMemberLicenceDeadline()->diff($today)->m < 3)
        {
            $expired = true;
        }
        else
        {
            $expired = false;
        }

        $this->licences = array('history' => $licence_history, 'expired' => $expired);

        return $this->licences;
    }

    public function getStages(): ?array
    {
        if ($this->stages != null)
        {
            return $this->stages;
        }

        $stage_history = $this->em->getRepository(Member::class)->getMemberAttendances($this->member->getMemberId());

        $stage_count = 0; $total = 0; $history = array(); $history['Total'] = 0;

        while (isset($stage_history[$stage_count]))
        {
            $stage_history[$stage_count]['Duration'] = $stage_history[$stage_count]['Duration'] / 60;

            $history['Stage'][] = $stage_history[$stage_count];

            $total = $total + $stage_history[$stage_count]['Duration'];

            $stage_count++;
        }

        $this->stages = array('history' => $history, 'total' => $total);

        return $this->stages;
    }
}
