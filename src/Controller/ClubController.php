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
use App\Service\UserTools;

use DateTime;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/club", name="club_")
 *
 * @IsGranted("ROLE_CLUB")
 */
class ClubController extends AbstractController
{
    private $clubTools;

    /**
     * ClubController constructor.
     * @param ClubTools $clubTools
     */
    public function __construct(ClubTools $clubTools)
    {
        $clubTools->setClub($this->getUser()->getUserClub());

        $this->clubTools = $clubTools;
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
        return $this->render('Club/Dojo/index.html.twig', array('clubTools' => $this->clubTools));
    }

    /**
     * @Route("/ajouter_dojo", name="dojo_address_add")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function dojoAddressAdd(Request $request)
    {
        $form = $this->createForm(ClubType::class, new TrainingAddress(), array('form' => 'dojo_create', 'data_class' => TrainingAddress::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoAddress($form->getData(), 'Add');

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
            $this->clubTools->dojoAddress($form->getData());

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
            $this->clubTools->dojoAddress($form->getData(), 'Delete');

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
        $form = $this->createForm(ClubType::class, new Training(), array('form' => 'training_create', 'data_class' => Training::class, 'choices' => $this->clubTools->getClub()->getClubAddresses()));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoTraining($form->getData(), 'Add');

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
            $this->clubTools->dojoTraining($form->getData());

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
            $this->clubTools->dojoTraining($form->getData(), 'Delete');

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
        $form = $this->createForm(ClubType::class, new ClubTeacher(), array('form' => 'teacher_afa_create', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoTeacher($form->getData(), 'Add', $form->get('ClubTeacherMember')->getData());

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
        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_afa_update', 'data_class' => ClubTeacher::class));

        $form->get('ClubTeacherMember')->setData($teacher->getClubTeacherMember()->getMemberId());
        $form->get('ClubTeacherFirstname')->setData($teacher->getClubTeacherMember()->getMemberFirstname());
        $form->get('ClubTeacherName')->setData($teacher->getClubTeacherMember()->getMemberName());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoTeacher($form->getData());

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
        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_afa_delete', 'data_class' => ClubTeacher::class));

        $form->get('ClubTeacherMember')->setData($teacher->getClubTeacherMember()->getMemberId());
        $form->get('ClubTeacherFirstname')->setData($teacher->getClubTeacherMember()->getMemberFirstname());
        $form->get('ClubTeacherName')->setData($teacher->getClubTeacherMember()->getMemberName());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoTeacher($form->getData(), 'Delete');

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
        $form = $this->createForm(ClubType::class, new ClubTeacher(), array('form' => 'teacher_foreign_create', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoTeacher($form->getData(), 'Add', $form->get('ClubTeacherMember')->getData());

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
        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_foreign_update', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoTeacher($form->getData());

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
        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_foreign_delete', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoTeacher($form->getData(), 'Delete');

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
        $form = $this->createForm(ClubType::class, $this->clubTools->getClub(), array('form' => 'detail_association'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->associationDetails($form->getData());

            return $this->redirectToRoute('club_index');
        }

        return $this->render('Club/Association/details.html.twig', array('form' => $form->createView(), 'club' => $this->clubTools->getClub()));
    }

    /**
     * @Route("/liste_des_membres", name="members_list")
     * @return Response
     */
    public function membersList()
    {
        $today = new DateTime('today');

        $members = $this->getDoctrine()->getRepository(Member::class)->getClubActiveMembers($this->clubTools->getClub(), $today->format('Y-m-d'));

        return $this->render('Club/Member/list.html.twig', array('members' => $members, 'club' => $this->clubTools->getClub()));
    }

    /**
     * @Route("/donnees_personnelles/{member<\d+>}", name="member_personal_data")
     * @param Member $member
     * @param MemberTools $memberTools
     * @return Response
     */
    public function memberPersonalData(Member $member, MemberTools $memberTools)
    {
        if ($member->getMemberActualClub() !== $this->clubTools->getClub())
        {
            return $this->redirectToRoute('club_members_list');
        }

        $memberTools->setMember($member);

        return $this->render('Club/Member/personal_data.html.twig', array('memberTools' => $memberTools));
    }

    /**
     * @Route("/creer_login/{member<\d+>}", name="member_login_create")
     * @param UserTools $userTools
     * @param SessionInterface $session
     * @param Request $request
     * @param Member $member
     * @return Response
     */
    public function memberLoginCreate(UserTools $userTools, SessionInterface $session, Request $request, Member $member)
    {
        if ($member->getMemberActualClub() !== $this->clubTools->getClub())
        {
            return $this->redirectToRoute('club_members_list');
        }

        $form = $this->createForm(UserType::class, new User(), array('form' => 'create', 'data_class' => User::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $userTools->newUser($form->getData(), $this->getUser(), $form['Password']->getData(), $member);

            $session->set('duplicate', $userTools->isDuplicate());

            if ($session->get('duplicate'))
            {
                return $this->render('Club/Member/login_create.html.twig', array('form' => $form->createView()));
            }

            return $this->redirectToRoute('club_members_list');
        }

        return $this->render('Club/Member/login_create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/detail_licence/{member<\d+>}", name="member_licence_detail")
     * @param Member $member
     * @param MemberTools $memberTools
     * @return Response
     */
    public function memberLicenceDetail(Member $member, MemberTools $memberTools)
    {
        if ($member->getMemberActualClub() !== $this->clubTools->getClub())
        {
            return $this->redirectToRoute('club_members_list');
        }

        $memberTools->setMember($member);

        return $this->render('Club/Member/licence_detail.html.twig', array('memberTools' => $memberTools));
    }

    /**
     * @Route("/detail_grades/{member<\d+>}", name="member_grades_detail")
     * @param Member $member
     * @param MemberTools $memberTools
     * @return Response
     */
    public function memberGradesDetail(Member $member, MemberTools $memberTools)
    {
        if ($member->getMemberActualClub() !== $this->clubTools->getClub())
        {
            return $this->redirectToRoute('club_members_list');
        }

        $memberTools->setMember($member);

        return $this->render('Club/Member/grade_detail.html.twig', array('memberTools' => $memberTools));
    }

    /**
     * @Route("/detail_stages/{member<\d+>}", name="member_stages_detail")
     * @param Member $member
     * @param MemberTools $memberTools
     * @return Response
     */
    public function memberStagesDetail(Member $member, MemberTools $memberTools)
    {
        if ($member->getMemberActualClub() !== $this->clubTools->getClub())
        {
            return $this->redirectToRoute('club_members_list');
        }

        $memberTools->setMember($member);

        return $this->render('Member/my_stages.html.twig', array('memberTools' => $memberTools));
    }

    /**
     * @Route("/membre/{member<\d+>}/candidature", name="member_application")
     * @param Request $request
     * @param Member $member
     * @param MemberTools $memberTools
     * @return RedirectResponse|Response
     */
    public function memberApplication(Request $request, Member $member, MemberTools $memberTools)
    {
        if ($member->getMemberActualClub() !== $this->clubTools->getClub())
        {
            return $this->redirectToRoute('club_members_list');
        }

        $memberTools->setMember($member);

        $form = $this->createForm(GradeType::class, $memberTools->getGrades()['exam']['grade'], array('form' => 'exam_application', 'data_class' => Grade::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $memberTools->application($form->getData());

            return $this->redirectToRoute('club_members_list');
        }

        return $this->render('Club/Member/exam_application.html.twig', array('form' => $form->createView(), 'exam' => $form->getData()->getGradeExam()));
    }

    /**
     * @Route("/membre/{member<\d+>}/ajouter_kyu", name="member_add_kyu")
     * @param Request $request
     * @param Member $member
     * @return RedirectResponse|Response
     */
    public function memberAddKyu(Request $request, Member $member)
    {
        //need to be added to memberTools

        if ($member->getMemberActualClub() !== $this->clubTools->getClub())
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

        $grade->setGradeClub($this->clubTools->getClub());
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
     * @param ClubTools $clubTools
     * @return Response
     */
    public function managerIndex(ClubTools $clubTools)
    {
        $clubTools->setClub($this->getUser()->getUserClub());

        return $this->render('Club/Manager/index.html.twig', array('clubTools' => $clubTools));
    }

    /**
     * @Route("/rechercher_membres", name="search_members")
     * @param Request $request
     * @return Response
     */
    public function searchMembers(Request $request)
    {
        $club = $this->getUser()->getUserClub();

        $search = null; $results = null;

        $form = $this->createForm(ClubType::class, $search, array('form' => 'search_members', 'data_class' => null));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $results = $this->getDoctrine()->getRepository(Member::class)->getFullSearchClubMembers($form->get('Search')->getData(), $club->getClubId());

            return $this->render('Club/Member/search.html.twig', array('form' => $form->createView(), 'results' => $results));
        }

        return $this->render('Club/Member/search.html.twig', array('form' => $form->createView(), 'results' => $results));
    }

}
