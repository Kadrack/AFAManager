<?php
// src/Repository/ClubRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\ClubHistory;
use App\Entity\ClubTeacher;
use App\Entity\Member;
use App\Entity\MemberLicence;

use DateInterval;
use DateTime;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Club|null find($id, $lockMode = null, $lockVersion = null)
 * @method Club|null findOneBy(array $criteria, array $orderBy = null)
 * @method Club[]    findAll()
 * @method Club[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClubRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Club::class);
    }

    public function getCreationDateList(): ?array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->select('c.club_id AS Id', 'c.club_name AS Name', 'c.club_creation AS Creation', 'h.club_history_update AS Affiliation')
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history_id', 'c.club_last_history'))
            ->where($qb->expr()->eq('h.club_history_status', 1))
            ->orderBy('c.club_name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function getActiveClubs(): ?array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->select('c.club_id AS Id', 'c.club_name AS Name','c.club_province AS Province')
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history_id', 'c.club_last_history'))
            ->where($qb->expr()->eq('h.club_history_status', 1))
            ->orderBy('c.club_province', 'ASC')
            ->addOrderBy('c.club_id', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function getInactiveClubs(): ?array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->select('c.club_id AS Id', 'c.club_name AS Name','c.club_province AS Province')
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history_id', 'c.club_last_history'))
            ->where($qb->expr()->neq('h.club_history_status', 1))
            ->orderBy('c.club_id', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function getProvinceTeachersTotal(DateTime $referenceDate): ?array
    {
        $deadline = $referenceDate->format('Y-m-d');

        $deadline_high = $referenceDate->add(new DateInterval('P1Y'))->format('Y-m-d');

        $qb = $this->createQueryBuilder('c');

        return $qb->select('c.club_province AS Province', 'count(m.member_id) AS Total')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_actual_club', 'c.club_id'))
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->leftJoin(ClubTeacher::class, 't', 'WITH', $qb->expr()->eq('t.club_teacher_member', 'm.member_id'))
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history', 'm.member_actual_club'))
            ->where($qb->expr()->gt('l.member_licence_deadline', "'".$deadline."'"))
            ->andWhere($qb->expr()->lte('l.member_licence_deadline', "'".$deadline_high."'"))
            ->andWhere($qb->expr()->eq('h.club_history_status', 1))
            ->andWhere($qb->expr()->isNotNull('t.club_teacher_id'))
            ->groupBy('c.club_province')
            ->orderBy('Province', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function getProvinceMembersTotal(DateTime $referenceDate): ?array
    {
        $deadline = $referenceDate->format('Y-m-d');

        $deadline_high = $referenceDate->add(new DateInterval('P1Y'))->format('Y-m-d');

        $qb = $this->createQueryBuilder('c');

        return $qb->select('c.club_province AS Province', 'm.member_sex AS Sex', 'count(m.member_id) AS Total')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_actual_club', 'c.club_id'))
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->leftJoin(ClubTeacher::class, 't', 'WITH', $qb->expr()->eq('t.club_teacher_member', 'm.member_id'))
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history', 'm.member_actual_club'))
            ->where($qb->expr()->gt('l.member_licence_deadline', "'".$deadline."'"))
            ->andWhere($qb->expr()->lte('l.member_licence_deadline', "'".$deadline_high."'"))
            ->andWhere($qb->expr()->eq('h.club_history_status', 1))
            ->andWhere($qb->expr()->isNull('t.club_teacher_id'))
            ->groupBy('c.club_province')
            ->addGroupBy('m.member_sex')
            ->orderBy('Province', 'ASC')
            ->addOrderBy('Sex', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function getProvinceMembersCount(?DateTime $referenceDate = null): ?array
    {
        $result = array();

        $deadline = $referenceDate->format('Y-m-d');

        $deadline_high = $referenceDate->add(new DateInterval('P1Y'))->format('Y-m-d');

        $limit[0] = $referenceDate->sub(new DateInterval('P0Y'))->format('Y-m-d');
        $limit[1] = $referenceDate->sub(new DateInterval('P6Y'))->format('Y-m-d');
        $limit[2] = $referenceDate->sub(new DateInterval('P6Y'))->format('Y-m-d');
        $limit[3] = $referenceDate->sub(new DateInterval('P6Y'))->format('Y-m-d');
        $limit[4] = $referenceDate->sub(new DateInterval('P7Y'))->format('Y-m-d');
        $limit[5] = $referenceDate->sub(new DateInterval('P10Y'))->format('Y-m-d');

        $qb = $this->createQueryBuilder('c');

        for ($i = 0; $i < count($limit)-1; $i++)
        {
            $result[] = $qb->select('c.club_province AS Province', 'm.member_sex AS Sex', 'count(m.member_id) AS Total')
                ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_actual_club', 'c.club_id'))
                ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
                ->leftJoin(ClubTeacher::class, 't', 'WITH', $qb->expr()->eq('t.club_teacher_member', 'm.member_id'))
                ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history', 'm.member_actual_club'))
                ->where($qb->expr()->gt('l.member_licence_deadline', "'".$deadline."'"))
                ->andWhere($qb->expr()->lte('l.member_licence_deadline', "'".$deadline_high."'"))
                ->andWhere($qb->expr()->eq('h.club_history_status', 1))
                ->andWhere($qb->expr()->between('m.member_birthday', "'".$limit[$i+1]."'", "'".$limit[$i]."'"))
                ->groupBy('c.club_province')
                ->addGroupBy('m.member_sex')
                ->orderBy('Province', 'ASC')
                ->addOrderBy('Sex', 'ASC')
                ->getQuery()
                ->getArrayResult();

            $qb = $this->createQueryBuilder('c');
        }

        $result[] = $qb->select('c.club_province AS Province', 'm.member_sex AS Sex', 'count(m.member_id) AS Total')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_actual_club', 'c.club_id'))
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->leftJoin(ClubTeacher::class, 't', 'WITH', $qb->expr()->eq('t.club_teacher_member', 'm.member_id'))
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history', 'm.member_actual_club'))
            ->where($qb->expr()->gt('l.member_licence_deadline', "'".$deadline."'"))
            ->andWhere($qb->expr()->lte('l.member_licence_deadline', "'".$deadline_high."'"))
            ->andWhere($qb->expr()->eq('h.club_history_status', 1))
            ->andWhere($qb->expr()->lte('m.member_birthday', "'".$limit[count($limit)-1]."'"))
            ->groupBy('c.club_province')
            ->addGroupBy('m.member_sex')
            ->orderBy('Province', 'ASC')
            ->addOrderBy('Sex', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return $result;
    }

    public function getClubMembersCount(int $province, DateTime $referenceDate): ?array
    {
        $result = array();

        $deadline = $referenceDate->format('Y-m-d');

        $deadline_high = $referenceDate->add(new DateInterval('P1Y'))->format('Y-m-d');

        $limit[0] = $referenceDate->sub(new DateInterval('P0Y'))->format('Y-m-d');
        $limit[1] = $referenceDate->sub(new DateInterval('P6Y'))->format('Y-m-d');
        $limit[2] = $referenceDate->sub(new DateInterval('P6Y'))->format('Y-m-d');
        $limit[3] = $referenceDate->sub(new DateInterval('P6Y'))->format('Y-m-d');
        $limit[4] = $referenceDate->sub(new DateInterval('P7Y'))->format('Y-m-d');
        $limit[5] = $referenceDate->sub(new DateInterval('P10Y'))->format('Y-m-d');

        $qb = $this->createQueryBuilder('c');

        $result['Clubs'] = $qb->select('c.club_id AS Id', 'c.club_name AS Name')
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history_id', 'c.club_last_history'))
            ->where($qb->expr()->eq('h.club_history_status', 1))
            ->andWhere($qb->expr()->eq('c.club_province', $province))
            ->orderBy('c.club_province', 'ASC')
            ->addOrderBy('c.club_id', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $qb = $this->createQueryBuilder('c');

        for ($i = 0; $i < count($limit)-1; $i++)
        {
            $result['Details'][] = $qb->select('c.club_id AS Id', 'c.club_name AS Name', 'm.member_sex AS Sex', 'count(m.member_id) AS Total')
                ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_actual_club', 'c.club_id'))
                ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
                ->leftJoin(ClubTeacher::class, 't', 'WITH', $qb->expr()->eq('t.club_teacher_member', 'm.member_id'))
                ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history', 'm.member_actual_club'))
                ->where($qb->expr()->gt('l.member_licence_deadline', "'".$deadline."'"))
                ->andWhere($qb->expr()->lte('l.member_licence_deadline', "'".$deadline_high."'"))
                ->andWhere($qb->expr()->eq('h.club_history_status', 1))
                ->andWhere($qb->expr()->between('m.member_birthday', "'".$limit[$i+1]."'", "'".$limit[$i]."'"))
                ->andWhere($qb->expr()->eq('c.club_province', $province))
                ->groupBy('c.club_id')
                ->addGroupBy('m.member_sex')
                ->orderBy('Id', 'ASC')
                ->getQuery()
                ->getArrayResult();

            $qb = $this->createQueryBuilder('c');
        }

        $result['Details'][] = $qb->select('c.club_id AS Id', 'c.club_name AS Name', 'm.member_sex AS Sex', 'count(m.member_id) AS Total')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_actual_club', 'c.club_id'))
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->leftJoin(ClubTeacher::class, 't', 'WITH', $qb->expr()->eq('t.club_teacher_member', 'm.member_id'))
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history', 'm.member_actual_club'))
            ->where($qb->expr()->gt('l.member_licence_deadline', "'".$deadline."'"))
            ->andWhere($qb->expr()->lte('l.member_licence_deadline', "'".$deadline_high."'"))
            ->andWhere($qb->expr()->eq('h.club_history_status', 1))
            ->andWhere($qb->expr()->lte('m.member_birthday', "'".$limit[count($limit)-1]."'"))
            ->andWhere($qb->expr()->eq('c.club_province', $province))
            ->groupBy('c.club_id')
            ->addGroupBy('m.member_sex')
            ->orderBy('Id', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return $result;
    }
}
