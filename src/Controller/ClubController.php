<?php
// src/Controller/ClubController.php
namespace App\Controller;

use App\Entity\ClubTeacher;
use App\Entity\Grade;
use App\Entity\Member;
use App\Entity\Training;
use App\Entity\TrainingAddress;
use App\Entity\User;

use App\Form\ClubType;
use App\Form\GradeType;
use App\Form\UserType;

use App\Service\ClubTools;
use App\Service\MemberTools;

use DateTime;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/club", name="club_")
 *
 * @IsGranted("ROLE_CLUB")
 */
class ClubController extends AbstractController
{
    private $passwordEncoder;

    /**
     * ClubController constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('Club/index.html.twig');
    }

    /**
     * @Route("/index_dojo", name="dojo_index")
     * @return Response
     */
    public function dojoIndex()
    {
        $club = $this->getUser()->getUserClub();

        $club_tools = new ClubTools($this->getDoctrine()->getManager(), $club);

        return $this->render('Club/Dojo/index.html.twig', array('club' => $club, 'club_tools' => $club_tools));
    }

    /**
     * @Route("/ajouter_dojo", name="dojo_address_add")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function dojoAddressAdd(Request $request)
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

            return $this->redirectToRoute('club_dojo_index');
        }

        return $this->render('Club/Dojo/address_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/modifier_dojo/{address<\d+>}", name="dojo_address_update")
     * @param Request $request
     * @param TrainingAddress $address
     * @return RedirectResponse|Response
     */
    public function dojoAddressUpdate(Request $request, TrainingAddress $address)
    {
        $form = $this->createForm(ClubType::class, $address, array('form' => 'dojo_update', 'data_class' => TrainingAddress::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club_dojo_index');
        }

        return $this->render('Club/Dojo/address_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/supprimer_dojo/{address<\d+>}", name="dojo_address_delete")
     * @param Request $request
     * @param TrainingAddress $address
     * @return RedirectResponse|Response
     */
    public function dojoAddressDelete(Request $request, TrainingAddress $address)
    {
        $form = $this->createForm(ClubType::class, $address, array('form' => 'dojo_delete', 'data_class' => TrainingAddress::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($address);
            $entityManager->flush();

            return $this->redirectToRoute('club_dojo_index');
        }

        return $this->render('Club/Dojo/address_delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/ajouter_horaire", name="dojo_training_add")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function dojoTrainingAdd(Request $request)
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

            return $this->redirectToRoute('club_dojo_index');
        }

        return $this->render('Club/Dojo/training_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/modifier_horaire/{training<\d+>}", name="dojo_training_update")
     * @param Request $request
     * @param Training $training
     * @return RedirectResponse|Response
     */
    public function dojoTrainingUpdate(Request $request, Training $training)
    {
        $form = $this->createForm(ClubType::class, $training, array('form' => 'training_update', 'data_class' => Training::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club_dojo_index');
        }

        return $this->render('Club/Dojo/training_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/supprimer_horaire/{training<\d+>}", name="dojo_training_delete")
     * @param Request $request
     * @param Training $training
     * @return RedirectResponse|Response
     */
    public function dojoTrainingDelete(Request $request, Training $training)
    {
        $form = $this->createForm(ClubType::class, $training, array('form' => 'training_delete', 'data_class' => Training::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($training);
            $entityManager->flush();

            return $this->redirectToRoute('club_dojo_index');
        }

        return $this->render('Club/Dojo/training_delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/ajouter_professeur_afa", name="dojo_teacher_afa_add")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function dojoTeacherAFAAdd(Request $request)
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
                $teacher->setClubTeacherMember($member);

                if ($form->get('ClubTeacherTitle')->getData() == 1)
                {
                    $club->setClubMainTeacher($teacher);
                }

                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->persist($teacher);
                $entityManager->flush();
            }

            return $this->redirectToRoute('club_dojo_index');
        }

        return $this->render('Club/Dojo/teacher_afa_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/modifier_professeur_afa/{teacher<\d+>}", name="dojo_teacher_afa_update")
     * @param Request $request
     * @param ClubTeacher $teacher
     * @return RedirectResponse|Response
     */
    public function dojoTeacherAFAUpdate(Request $request, ClubTeacher $teacher)
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

            return $this->redirectToRoute('club_dojo_index');
        }

        return $this->render('Club/Dojo/teacher_afa_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/supprimer_professeur_afa/{teacher<\d+>}", name="dojo_teacher_afa_delete")
     * @param Request $request
     * @param ClubTeacher $teacher
     * @return RedirectResponse|Response
     */
    public function dojoTeacherAFADelete(Request $request, ClubTeacher $teacher)
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

            return $this->redirectToRoute('club_dojo_index');
        }

        return $this->render('Club/Dojo/teacher_afa_delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/ajouter_professeur_etranger", name="dojo_teacher_foreign_add")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function dojoTeacherForeignAdd(Request $request)
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

            return $this->redirectToRoute('club_dojo_index');
        }

        return $this->render('Club/Dojo/teacher_foreign_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/modifier_professeur_etranger/{teacher<\d+>}", name="dojo_teacher_foreign_update")
     * @param Request $request
     * @param ClubTeacher $teacher
     * @return RedirectResponse|Response
     */
    public function dojoTeacherForeignUpdate(Request $request, ClubTeacher $teacher)
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

            return $this->redirectToRoute('club_dojo_index');
        }

        return $this->render('Club/Dojo/teacher_foreign_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/supprimer_professeur_etranger/{teacher<\d+>}", name="dojo_teacher_foreign_delete")
     * @param Request $request
     * @param ClubTeacher $teacher
     * @return RedirectResponse|Response
     */
    public function dojoTeacherForeignDelete(Request $request, ClubTeacher $teacher)
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

            return $this->redirectToRoute('club_dojo_index');
        }

        return $this->render('Club/Dojo/teacher_foreign_delete.html.twig', array('form' => $form->createView()));
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

        return $this->render('Club/Association/details.html.twig', array('form' => $form->createView(), 'club' => $club));
    }

    /**
     * @Route("/liste_des_membres", name="members_list")
     * @return Response
     */
    public function membersList()
    {
        $club = $this->getUser()->getUserClub();

        $today = new DateTime('today');

        $members = $this->getDoctrine()->getRepository(Member::class)->getClubActiveMembers($club, $today->format('Y-m-d'));

        return $this->render('Club/Member/list.html.twig', array('members' => $members, 'club' => $club));
    }

    /**
     * @Route("/donnees_personnelles/{member<\d+>}", name="member_personal_data")
     * @param Member $member
     * @return Response
     */
    public function memberPersonalData(Member $member)
    {
        $club = $this->getUser()->getUserClub();

        if ($member->getMemberActualClub() != $club)
        {
            return $this->redirectToRoute('club_members_list');
        }

        $member_tools = new MemberTools($this->getDoctrine()->getManager(), $member);

        return $this->render('Club/Member/personal_data.html.twig', array('member' => $member, 'member_tools' => $member_tools));
    }

    /**
     * @Route("/creer_login/{member<\d+>}", name="member_login_create")
     * @param SessionInterface $session
     * @param Request $request
     * @param Member $member
     * @return Response
     */
    public function memberLoginCreate(SessionInterface $session, Request $request, Member $member)
    {
        $session->set('duplicate', false);

        $club = $this->getUser()->getUserClub();

        if ($member->getMemberActualClub() != $club)
        {
            return $this->redirectToRoute('club_members_list');
        }

        $user = new User();

        $form = $this->createForm(UserType::class, $user, array('form' => 'create', 'data_class' => User::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if (!is_null($this->getDoctrine()->getRepository(User::class)->findOneBy(['login' => $form->get('Login')->getData()])))
            {
                $session->set('duplicate', true);

                return $this->render('Club/Member/login_create.html.twig', array('form' => $form->createView()));
            }

            $user->setPassword($this->passwordEncoder->encodePassword($user, $form['Password']->getData()));
            $user->setUserMember($member);
            $user->setUserStatus(1);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('club_members_list');
        }

        return $this->render('Club/Member/login_create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/detail_licence/{member<\d+>}", name="member_licence_detail")
     * @param Member $member
     * @return Response
     */
    public function memberLicenceDetail(Member $member)
    {
        $club = $this->getUser()->getUserClub();

        if ($member->getMemberActualClub() != $club)
        {
            return $this->redirectToRoute('club_members_list');
        }

        $member_tools = new MemberTools($this->getDoctrine()->getManager(), $member);

        return $this->render('Club/Member/licence_detail.html.twig', array('member' => $member, 'member_tools' => $member_tools));
    }

    /**
     * @Route("/detail_grades/{member<\d+>}", name="member_grades_detail")
     * @param Member $member
     * @return Response
     */
    public function memberGradesDetail(Member $member)
    {
        $club = $this->getUser()->getUserClub();

        if ($member->getMemberActualClub() != $club)
        {
            return $this->redirectToRoute('club_members_list');
        }

        $member_tools = new MemberTools($this->getDoctrine()->getManager(), $member);

        return $this->render('Club/Member/grade_detail.html.twig', array('member' => $member, 'member_tools' => $member_tools));
    }

    /**
     * @Route("/detail_stages/{member<\d+>}", name="member_stages_detail")
     * @param Member $member
     * @return Response
     */
    public function memberStagesDetail(Member $member)
    {
        $member_tools = new MemberTools($this->getDoctrine()->getManager(), $member);

        return $this->render('Member/my_stages.html.twig', array('member' => $member, 'member_tools' => $member_tools));
    }

    /**
     * @Route("/membre/{member<\d+>}/candidature", name="member_application")
     * @param Request $request
     * @param Member $member
     * @return RedirectResponse|Response
     */
    public function memberApplication(Request $request, Member $member)
    {
        $club = $this->getUser()->getUserClub();

        if ($member->getMemberActualClub() != $club)
        {
            return $this->redirectToRoute('club_members_list');
        }

        $member_tools = new MemberTools($this->getDoctrine()->getManager(), $member);

        $grade = $member_tools->getGrades()['exam']['grade'];

        $form = $this->createForm(GradeType::class, $grade, array('form' => 'exam_application', 'data_class' => Grade::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($grade);
            $entityManager->flush();

            return $this->redirectToRoute('club_members_list');
        }

        return $this->render('Club/Member/exam_application.html.twig', array('form' => $form->createView(), 'exam' => $grade->getGradeExam()));
    }

    /**
     * @Route("/membre/{member<\d+>}/ajouter_kyu", name="member_add_kyu")
     * @param Request $request
     * @param Member $member
     * @return RedirectResponse|Response
     */
    public function memberAddKyu(Request $request, Member $member)
    {
        $club = $this->getUser()->getUserClub();

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

            return $this->redirectToRoute('club_member_grades_detail', array('member' => $member->getMemberId()));
        }

        return $this->render('Club/Member/add_kyu.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/liste_gestionnaire", name="manager_index")
     * @return Response
     */
    public function managerIndex()
    {
        $club = $this->getUser()->getUserClub();

        $club_tools = new ClubTools($this->getDoctrine()->getManager(), $club);

        return $this->render('Club/Manager/index.html.twig', array('club' => $club, 'club_tools' => $club_tools));
    }
}
