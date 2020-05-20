<?php
// src/Controller/MemberController.php
namespace App\Controller;

use App\Entity\Club;
use App\Entity\ClubTeacher;
use App\Entity\Grade;
use App\Entity\GradeSession;
use App\Entity\GradeTitle;
use App\Entity\Member;
use App\Entity\MemberLicence;
use App\Entity\MemberModification;
use App\Entity\Training;
use App\Entity\TrainingAddress;

use App\Form\GradeType;
use App\Form\MemberType;

use App\Service\PhotoUploader;

use DateTime;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/membre", name="member_")
 *
 * @IsGranted("ROLE_MEMBER")
 */
class MemberController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('Member/index.html.twig');
    }

    /**
     * @Route("/mes_donnees", name="my_data")
     * @return Response
     */
    public function myData()
    {
        $club   = $this->getUser()->getUserClub();

        $member = $this->getUser()->getUserMember();

        return $this->render('Member/my_data.html.twig', array('member' => $member, 'club' => $club));
    }

    /**
     * @Route("/mes_donnees/modifier", name="my_data_update")
     * @param Request $request
     * @param PhotoUploader $photoUploader
     * @return Response
     */
    public function myDataUpdate(Request $request, PhotoUploader $photoUploader)
    {
        $club   = $this->getUser()->getUserClub();

        $member = $this->getUser()->getUserMember();

        if ($member->getMemberModification() == null)
        {
            $member_modification = new MemberModification();

            $member_modification->setMemberModificationId($member->getMemberId());
        }
        else
        {
            $member_modification = $member->getMemberModification();
        }

        $form = $this->createForm(MemberType::class, $member_modification, array('form' => 'update', 'data_class' => MemberModification::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($form['MemberModificationPhoto']->getData() != null)
            {
                $member_modification->setMemberModificationPhoto($photoUploader->upload($form['MemberModificationPhoto']->getData(), $member_modification->getMemberModificationPhoto()));
            }

            $entityManager = $this->getDoctrine()->getManager();

            if ($member->getMemberModification() == null)
            {
                $member->setMemberModification($member_modification);

                $entityManager->persist($member_modification);
            }

            if ($form['MemberModificationCountry']->getData() == $member->getMemberCountry())
            {
                $member_modification->setMemberModificationCountry();
            }

            $entityManager->flush();

            return $this->render('Member/my_data.html.twig', array('member' => $member, 'club' => $club));
        }

        return $this->render('Member/my_data_update.html.twig', array('form' => $form->createView(), 'member' => $member, 'club' => $club));
    }

    /**
     * @Route("/mes_grades", name="my_grades")
     * @return Response
     */
    public function myGrades()
    {
        $club   = $this->getUser()->getUserClub();

        $member = $this->getUser()->getUserMember();

        $today = new DateTime('today');

        $grade_history = $this->getDoctrine()->getRepository(Grade::class)->getGradeHistory($member->getMemberId());

        $open_session = $this->getDoctrine()->getRepository(GradeSession::class)->getOpenSession($today->format('Y-m-d'), 1);

        if (($open_session == null) || ($grade_history[0]['Type'] == 2) || ($grade_history[0]['Rank'] >= 14))
        {
            $exam_candidate = false;
        }
        elseif (isset($grade_history[0]['Session']))
        {
            if ($grade_history[0]['Session'] == $open_session[0]->getGradeSessionId())
            {
                $exam_candidate = false;
            }
            else
            {
                $exam_candidate = true;
            }
        }
        else
        {
            $exam_candidate = true;
        }

        $open_session = $this->getDoctrine()->getRepository(GradeSession::class)->getOpenSession($today->format('Y-m-d'), 2);

        if ($open_session == null)
        {
            $kagami_candidate = false;
        }
        elseif (isset($grade_history[0]['Session']))
        {
            if ($grade_history[0]['Session'] == $open_session[0]->getGradeSessionId())
            {
                $kagami_candidate = false;
            }
            else
            {
                $kagami_candidate = true;
            }
        }
        else
        {
            $kagami_candidate = true;
        }

        for ($i = 0; $i < sizeof($grade_history); $i++)
        {
            if ($grade_history[$i]['Type'] == null)
            {
                $grade_history[$i]['Type'] = 1;
            }
        }

        return $this->render('Member/my_grades.html.twig', array('member' => $member, 'club' => $club, 'grade_history' => $grade_history, 'exam_candidate' => $exam_candidate, 'kagami_candidate' => $kagami_candidate));
    }

    /**
     * @Route("/ma_licence", name="my_licence")
     * @return Response
     */
    public function myLicence()
    {
        $club   = $this->getUser()->getUserClub();

        $member = $this->getUser()->getUserMember();

        $licence_history = $this->getDoctrine()->getRepository(MemberLicence::class)->findBy(['member_licence' => $member->getMemberId()], ['member_licence_id' => 'DESC']);

        return $this->render('Member/my_licence.html.twig', array('member' => $member, 'club' => $club, 'licence_history' => $licence_history));
    }

    /**
     * @Route("/mes_stages", name="my_stages")
     * @return Response
     */
    public function myStages()
    {
        $club   = $this->getUser()->getUserClub();

        $member = $this->getUser()->getUserMember();

        $stage_history = $this->getDoctrine()->getRepository(Member::class)->getMemberAttendances($member->getMemberId());

        $grade_history = $this->getDoctrine()->getRepository(Grade::class)->getGradeHistory($member->getMemberId());

        $stage_count = 0; $grade_count = 0; $total = 0; $history = array();

        while (isset($grade_history[$grade_count]))
        {
            $history[$grade_count]['Rank'] = $grade_history[$grade_count]['Rank'];

            $history[$grade_count]['Total'] = 0;

            while (isset($stage_history[$stage_count]))
            {
                if ($grade_history[$grade_count]['Date'] < $stage_history[$stage_count]['Date'])
                {
                    $stage_history[$stage_count]['Duration'] = $stage_history[$stage_count]['Duration'] / 60;

                    $history[$grade_count]['Stage'][] = $stage_history[$stage_count];

                    $history[$grade_count]['Total'] = $history[$grade_count]['Total'] + $stage_history[$stage_count]['Duration'];

                    $stage_count++;
                }
                else
                {
                    break;
                }
            }

            $total = $total + $history[$grade_count]['Total'];

            $grade_count++;
        }

        return $this->render('Member/my_stages.html.twig', array('member' => $member, 'club' => $club, 'history' => $history, 'total' => $total));
    }

    /**
     * @Route("/candidature/{type<\d+>}", name="application")
     * @param Request $request
     * @param int $type
     * @return RedirectResponse|Response
     */
    public function application(Request $request, int $type)
    {
        $today  = new DateTime('today');

        $club   = $this->getUser()->getUserClub();

        $member = $this->getUser()->getUserMember();

        $exam   = $this->getDoctrine()->getRepository(GradeSession::class)->getOpenSession($today->format('Y-m-d'), $type);

        if (!is_object($member->getMemberLastGradeDan()))
        {
            $rank = 7;
        }
        elseif ($member->getMemberLastGrade()->getGradeStatus() == 3)
        {
            $rank = $member->getMemberLastGrade()->getGradeRank();
        }
        elseif ($member->getMemberLastGrade()->getGradeStatus() == 5)
        {
            $rank = $member->getMemberLastGrade()->getGradeRank() + 1;
        }
        elseif (($member->getMemberLastGrade()->getGradeStatus() == 4) && ($type == 2))
        {
            $rank = $member->getMemberLastGrade()->getGradeRank() + 1;
        }
        else
        {
            return $this->redirectToRoute('member_my_data');
        }

        $grade = new Grade();

        $grade->setGradeClub($club);
        $grade->setGradeDate(new DateTime('today'));
        $grade->setGradeExam($exam[0]);
        $grade->setGradeMember($member);
        $grade->setGradeRank($rank);
        $grade->setGradeStatus(1);

        $form = $this->createForm(GradeType::class, $grade, array('form' => 'exam_application', 'data_class' => Grade::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($grade);
            $entityManager->flush();

            return $this->redirectToRoute('member_my_data');
        }

        return $this->render('Member/application.html.twig', array('form' => $form->createView(), 'exam' => $exam[0], 'type' => $type));
    }

    /**
     * @Route("/mon_club", name="my_club")
     * @return Response
     */
    public function myClub()
    {
        $club   = $this->getUser()->getUserClub();

        $addresses = $this->getDoctrine()->getRepository(TrainingAddress::class)->findBy(['training_address_club' => $club->getClubId()]);

        $trainings = $this->getDoctrine()->getRepository(Training::class)->findBy(['training_club' => $club->getClubId()], ['training_day' => 'ASC', 'training_starting_hour' => 'ASC']);

        $afa_teachers = $this->getDoctrine()->getRepository(ClubTeacher::class)->getAFATeachers($club);

        $foreign_teachers = $this->getDoctrine()->getRepository(ClubTeacher::class)->getForeignTeachers($club);

        return $this->render('Member/my_club.html.twig', array('club' => $club, 'addresses' => $addresses, 'trainings' => $trainings, 'afa_teachers' => $afa_teachers, 'foreign_teachers' => $foreign_teachers));
    }

    /**
     * @Route("/{club<\d+>}/detail_titre/{member<\d+>}", name="detail_title")
     * @param Club $club
     * @param Member $member
     * @return Response
     */
    public function detailTitle(Club $club, Member $member)
    {
        $title_history = $this->getDoctrine()->getRepository(GradeTitle::class)->findBy(['grade_title_member' => $member->getMemberId()], ['grade_title_rank' => 'ASC']);

        $aikikai   = array(); $i = 0;
        $old_adeps = array(); $j = 0;
        $adeps     = array(); $k = 0;

        foreach ($title_history as $title)
        {
            if (($title->getGradeTitleRank() >= 1) and ($title->getGradeTitleRank() <= 3))
            {
                $aikikai[$i++] = $title;
            }
            else if (($title->getGradeTitleRank() >= 4) and ($title->getGradeTitleRank() <= 6))
            {
                $old_adeps[$j++] = $title;
            }
            else if (($title->getGradeTitleRank() >= 7) and ($title->getGradeTitleRank() <= 9))
            {
                $adeps[$k++] = $title;
            }
        }

        return $this->render('Member/detail_title.html.twig', array('member' => $member, 'club' => $club, 'aikikai' => $i == 0 ? null : $aikikai, 'old_adeps' => $j == 0 ? null : $old_adeps, 'adeps' => $k == 0 ? null : $adeps));
    }
}
