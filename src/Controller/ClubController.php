<?php
// src/Controller/ClubController.php
namespace App\Controller;

use App\Entity\ClubTeacher;
use App\Entity\Grade;
use App\Entity\GradeSession;
use App\Entity\Member;
use App\Entity\Training;
use App\Entity\TrainingAddress;

use App\Form\ClubType;
use App\Form\GradeType;

use DateTime;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/club", name="club_")
 *
 * @IsGranted("ROLE_CLUB")
 */
class ClubController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('Club/index.html.twig');
    }

    /**
     * @Route("/mon_administration", name="my_admin")
     * @return Response
     */
    public function myAdmin()
    {
        $club = $this->getUser()->getUserClub();

        $addresses = $this->getDoctrine()->getRepository(TrainingAddress::class)->findBy(['training_address_club' => $club->getClubId()]);

        $trainings = $this->getDoctrine()->getRepository(Training::class)->findBy(['training_club' => $club->getClubId()], ['training_day' => 'ASC', 'training_starting_hour' => 'ASC']);

        $afa_teachers = $this->getDoctrine()->getRepository(ClubTeacher::class)->getAFATeachers($club);

        $foreign_teachers = $this->getDoctrine()->getRepository(ClubTeacher::class)->getForeignTeachers($club);

        return $this->render('Club/my_admin.html.twig', array('club' => $club, 'addresses' => $addresses, 'trainings' => $trainings, 'afa_teachers' => $afa_teachers, 'foreign_teachers' => $foreign_teachers));
    }

    /**
     * @Route("/ajouter_dojo", name="dojo_add")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addressAdd(Request $request)
    {
        $club = $this->getUser()->getUserClub();

        $form = $this->createForm(ClubType::class, new TrainingAddress(), array('form' => 'dojo_create', 'data_class' => TrainingAddress::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $address = $form->getData();

            if ($address->getTrainingAddressDEA() == false)
            {
                $address->setTrainingAddressDEAFormation(null);
            }

            $address->setTrainingAddressClub($club);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($address);
            $entityManager->flush();

            return $this->redirectToRoute('club_my_admin');
        }

        return $this->render('Club/dojo_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/modifier_dojo/{address<\d+>}", name="dojo_update")
     * @param Request $request
     * @param TrainingAddress $address
     * @return RedirectResponse|Response
     */
    public function addressUpdate(Request $request, TrainingAddress $address)
    {
        $form = $this->createForm(ClubType::class, $address, array('form' => 'dojo_update', 'data_class' => TrainingAddress::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club_my_admin');
        }

        return $this->render('Club/dojo_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/supprimer_dojo/{address<\d+>}", name="dojo_delete")
     * @param Request $request
     * @param TrainingAddress $address
     * @return RedirectResponse|Response
     */
    public function addressDelete(Request $request, TrainingAddress $address)
    {
        $form = $this->createForm(ClubType::class, $address, array('form' => 'dojo_delete', 'data_class' => TrainingAddress::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($address);
            $entityManager->flush();

            return $this->redirectToRoute('club_my_admin');
        }

        return $this->render('Club/dojo_delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/ajouter_horaire", name="training_add")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function trainingAdd(Request $request)
    {
        $club = $this->getUser()->getUserClub();

        $form = $this->createForm(ClubType::class, new Training(), array('form' => 'training_create', 'data_class' => Training::class, 'choices' => $club->getClubAddresses()));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $training = $form->getData();

            $training->setTrainingClub($club);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($training);
            $entityManager->flush();

            return $this->redirectToRoute('club_my_admin');
        }

        return $this->render('Club/training_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/modifier_horaire/{training<\d+>}", name="training_update")
     * @param Request $request
     * @param Training $training
     * @return RedirectResponse|Response
     */
    public function trainingUpdate(Request $request, Training $training)
    {
        $form = $this->createForm(ClubType::class, $training, array('form' => 'training_update', 'data_class' => Training::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club_my_admin');
        }

        return $this->render('Club/training_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/supprimer_horaire/{training<\d+>}", name="training_delete")
     * @param Request $request
     * @param Training $training
     * @return RedirectResponse|Response
     */
    public function trainingDelete(Request $request, Training $training)
    {
        $form = $this->createForm(ClubType::class, $training, array('form' => 'training_delete', 'data_class' => Training::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($training);
            $entityManager->flush();

            return $this->redirectToRoute('club_my_admin');
        }

        return $this->render('Club/training_delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/ajouter_professeur_afa", name="teacher_afa_add")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function teacherAFAAdd(Request $request)
    {
        $club = $this->getUser()->getUserClub();

        $form = $this->createForm(ClubType::class, new ClubTeacher(), array('form' => 'teacher_afa_create', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $teacher = $form->getData();

            $teacher->setClubTeacher($club);

            $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $form->get('ClubTeacherMember')->getData()]);

            if ($member != null)
            {
                $teacher->setClubTeacherFirstname($member->getMemberFirstname());
                $teacher->setClubTeacherMember($member);
                $teacher->setClubTeacherName($member->getMemberName());

                if ($form->get('ClubTeacherTitle')->getData() == 1) {
                    $club->setClubMainTeacher($teacher);
                }

                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->persist($teacher);
                $entityManager->flush();
            }

            return $this->redirectToRoute('club_my_admin');
        }

        return $this->render('Club/teacher_afa_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/modifier_professeur_afa/{teacher<\d+>}", name="teacher_afa_update")
     * @param Request $request
     * @param ClubTeacher $teacher
     * @return RedirectResponse|Response
     */
    public function teacherAFAUpdate(Request $request, ClubTeacher $teacher)
    {
        $club = $this->getUser()->getUserClub();

        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_afa_update', 'data_class' => ClubTeacher::class));

        $form->get('ClubTeacherMember')->setData($teacher->getClubTeacherMember()->getMemberId());
        $form->get('ClubTeacherFirstname')->setData($teacher->getClubTeacherMember()->getMemberFirstname());
        $form->get('ClubTeacherName')->setData($teacher->getClubTeacherMember()->getMemberName());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($form->get('ClubTeacherTitle')->getData() == 1)
            {
                $club->setClubMainTeacher($teacher);
            }

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club_my_admin');
        }

        return $this->render('Club/teacher_afa_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/supprimer_professeur_afa/{teacher<\d+>}", name="teacher_afa_delete")
     * @param Request $request
     * @param ClubTeacher $teacher
     * @return RedirectResponse|Response
     */
    public function teacherAFADelete(Request $request, ClubTeacher $teacher)
    {
        $club = $this->getUser()->getUserClub();

        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_afa_delete', 'data_class' => ClubTeacher::class));

        $form->get('ClubTeacherMember')->setData($teacher->getClubTeacherMember()->getMemberId());
        $form->get('ClubTeacherFirstname')->setData($teacher->getClubTeacherMember()->getMemberFirstname());
        $form->get('ClubTeacherName')->setData($teacher->getClubTeacherMember()->getMemberName());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            if ($form->get('ClubTeacherTitle')->getData() == 1)
            {
                $main_teacher = $this->getDoctrine()->getRepository(ClubTeacher::class)->findOneBy(['club_teacher' => $club->getClubId(), 'club_teacher_title' => 1]);

                /** @var ClubTeacher $main_teacher */
                $club->setClubMainTeacher($main_teacher->getClubTeacherId() == $teacher->getClubTeacherId() ? null : $main_teacher);
            }

            $entityManager->remove($teacher);
            $entityManager->flush();

            return $this->redirectToRoute('club_my_admin');
        }

        return $this->render('Club/teacher_afa_delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/ajouter_professeur_etranger", name="teacher_foreign_add")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function teacherForeignAdd(Request $request)
    {
        $club = $this->getUser()->getUserClub();

        $form = $this->createForm(ClubType::class, new ClubTeacher(), array('form' => 'teacher_foreign_create', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $teacher = $form->getData();

            $teacher->setClubTeacher($club);

            if ($form->get('ClubTeacherTitle')->getData() == 1)
            {
                $club->setClubMainTeacher($teacher);
            }

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($teacher);
            $entityManager->flush();

            return $this->redirectToRoute('club_my_admin');
        }

        return $this->render('Club/teacher_afa_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/modifier_professeur_etranger/{teacher<\d+>}", name="teacher_foreign_update")
     * @param Request $request
     * @param ClubTeacher $teacher
     * @return RedirectResponse|Response
     */
    public function teacherForeignUpdate(Request $request, ClubTeacher $teacher)
    {
        $club = $this->getUser()->getUserClub();

        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_foreign_update', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($form->get('ClubTeacherTitle')->getData() == 1)
            {
                $club->setClubMainTeacher($teacher);
            }

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club_my_admin');
        }

        return $this->render('Club/teacher_afa_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/supprimer_professeur_etranger/{teacher<\d+>}", name="teacher_foreign_delete")
     * @param Request $request
     * @param ClubTeacher $teacher
     * @return RedirectResponse|Response
     */
    public function teacherForeignDelete(Request $request, ClubTeacher $teacher)
    {
        $club = $this->getUser()->getUserClub();

        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_foreign_delete', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            if ($form->get('ClubTeacherTitle')->getData() == 1)
            {
                $main_teacher = $this->getDoctrine()->getRepository(ClubTeacher::class)->findOneBy(['club_teacher' => $club->getClubId(), 'club_teacher_title' => 1]);

                /** @var ClubTeacher $main_teacher */
                $club->setClubMainTeacher($main_teacher->getClubTeacherId() == $teacher->getClubTeacherId() ? null : $main_teacher);
            }

            $entityManager->remove($teacher);
            $entityManager->flush();

            return $this->redirectToRoute('club_my_admin');
        }

        return $this->render('Club/teacher_afa_delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/detail_association", name="association_details")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function associationDetails(Request $request)
    {
        $club = $this->getUser()->getUserClub();

        $form = $this->createForm(ClubType::class, $club, array('form' => 'detail_association'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club_index');
        }

        return $this->render('Club/association_details.html.twig', array('form' => $form->createView(), 'club' => $club));
    }

    /**
     * @Route("/liste_des_membres", name="members_list")
     * @return Response
     */
    public function activeMembers()
    {
        $club = $this->getUser()->getUserClub();

        $today = new DateTime('today');

        $members = $this->getDoctrine()->getRepository(Member::class)->getClubActiveMembers($club, $today->format('Y-m-d'));

        return $this->render('Club/members_list.html.twig', array('members' => $members, 'club' => $club));
    }

    /**
     * @Route("/detail_grades/{member<\d+>}", name="grades_detail")
     * @param Member $member
     * @return Response
     */
    public function gradesDetail(Member $member)
    {
        $club   = $this->getUser()->getUserClub();

        if ($member->getMemberActualClub() != $club)
        {
            return $this->redirectToRoute('club_members_list');
        }

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

        $count_kyus = 0;

        for ($i = 0; $i < sizeof($grade_history); $i++)
        {
            if ($grade_history[$i]['Type'] == null)
            {
                $grade_history[$i]['Type'] = 1;
            }

            if ($grade_history[$i]['Rank'] < 7)
            {
                $count_kyus++;
            }
        }

        $count_kyus >= 6 ? $kyu_candidate = false : $kyu_candidate = true;

        return $this->render('Club/grade_detail.html.twig', array('member' => $member, 'club' => $club, 'grade_history' => $grade_history, 'exam_candidate' => $exam_candidate, 'kagami_candidate' => $kagami_candidate, 'kyu_candidate' => $kyu_candidate));
    }

    /**
     * @Route("/membre/{member<\d+>}/candidature/{type<\d+>}", name="member_application")
     * @param Request $request
     * @param Member $member
     * @param int $type
     * @return RedirectResponse|Response
     */
    public function memberApplication(Request $request, Member $member, int $type)
    {
        $today  = new DateTime('today');

        $club   = $this->getUser()->getUserClub();

        if ($member->getMemberActualClub() != $club)
        {
            return $this->redirectToRoute('club_members_list');
        }

        $exam   = $this->getDoctrine()->getRepository(GradeSession::class)->getOpenSession($today->format('Y-m-d'), $type);

        if (!is_object($member->getMemberLastGrade()))
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
            return $this->redirectToRoute('club_members_list');
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

            return $this->redirectToRoute('club_members_list');
        }

        return $this->render('Club/member_application.html.twig', array('form' => $form->createView(), 'exam' => $exam[0], 'type' => $type));
    }

    /**
     * @Route("/membre/{member<\d+>}/ajouter_kyu", name="member_add_kyu")
     * @param Request $request
     * @param Member $member
     * @return RedirectResponse|Response
     */
    public function memberAddKyu(Request $request, Member $member)
    {
        $club   = $this->getUser()->getUserClub();

        if ($member->getMemberActualClub() != $club)
        {
            return $this->redirectToRoute('club_members_list');
        }

        if ($member->getMemberLastGrade()->getGradeRank() < 6)
        {
            $rank = $member->getMemberLastGrade()->getGradeRank() + 1;
        }
        else
        {
            $rank = 2;
        }

        $grade = new Grade();

        $grade->setGradeClub($club);
        $grade->setGradeDate(new DateTime('today'));
        $grade->setGradeMember($member);
        $grade->setGradeRank($rank);
        $grade->setGradeStatus(4);

        $form = $this->createForm(GradeType::class, $grade, array('form' => 'add_kyu', 'data_class' => Grade::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($member->getMemberLastGrade()->getGradeDate() <= $grade->getGradeDate())
            {
                $member->setMemberLastGrade($grade);
            }

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($grade);
            $entityManager->flush();

            return $this->redirectToRoute('club_grades_detail', array('member' => $member->getMemberId()));
        }

        return $this->render('Club/member_add_kyu.html.twig', array('form' => $form->createView()));
    }
}
