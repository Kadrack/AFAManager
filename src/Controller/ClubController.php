<?php
// src/Controller/ClubController.php
namespace App\Controller;

use App\Entity\Club;
use App\Entity\ClubDojo;
use App\Entity\ClubLesson;
use App\Entity\ClubTeacher;
use App\Entity\Grade;
use App\Entity\Member;
use App\Entity\User;

use App\Form\ClubType;
use App\Form\GradeType;
use App\Form\UserType;

use App\Service\ClubTools;
use App\Service\MemberTools;
use App\Service\UserTools;

use Doctrine\ORM\EntityManagerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Security;

/**
 * Class ClubController
 * @package App\Controller
 *
 * @IsGranted("ROLE_CLUB")
 */
#[Route('/club', name:'club-')]
class ClubController extends AbstractController
{
    /**
     * @var ClubTools
     */
    private ClubTools $clubTools;

    /**
     * ClubController constructor.
     * @param ClubTools $clubTools
     * @param UserTools $userTools
     * @param SessionInterface $session
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ClubTools $clubTools, UserTools $userTools, SessionInterface $session, Security $security, EntityManagerInterface $entityManager)
    {
        $this->clubTools = $clubTools;

        $managedClubs = $userTools->listManagedClub($security->getUser());

        if (!isset($managedClubs[$session->get('actual_club')]))
        {
            $session->set('actual_club', 0);
        }

        $this->clubTools->setClub($entityManager->getRepository(Club::class)->findOneBy(['club_id' => $managedClubs[$session->get('actual_club')]]));
    }

    /**
     * @param Request $request
     * @param SessionInterface $session
     * @param UserTools $userTools
     * @return Response
     */
    #[Route('/index-dojo', name:'dojoIndex')]
    public function dojoIndex(Request $request, SessionInterface $session, UserTools $userTools): Response
    {
        $managedClubs = $userTools->listManagedClub($this->getUser());

        if ($request->query->has('change_actual'))
        {
            if (isset($managedClubs[$request->query->getInt('change_actual')]))
            {
                $session->set('actual_club', $request->query->get('change_actual'));
            }
            else
            {
                $session->set('actual_club', 0);
            }
        }

        $this->clubTools->setClub($this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_id' => $managedClubs[$session->get('actual_club')]]));

        return $this->render('Club/Dojo/index.html.twig', array('clubTools' => $this->clubTools, 'nextManagedClub' => sizeof($managedClubs) == 1 ? 0 : $session->get('actual_club')+1));
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route('/ajouter-dojo', name:'dojoAdd')]
    public function dojoAdd(Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(ClubType::class, new ClubDojo(), array('form' => 'dojoCreate', 'data_class' => ClubDojo::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoAddress($form->getData(), 'Add');

            return $this->redirectToRoute('club-dojoIndex');
        }

        return $this->render('Club/Dojo/address_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param ClubDojo $clubDojo
     * @return RedirectResponse|Response
     */
    #[Route('/modifier-dojo/{clubDojo<\d+>}', name:'dojoUpdate')]
    public function dojoUpdate(Request $request, ClubDojo $clubDojo): RedirectResponse|Response
    {
        $form = $this->createForm(ClubType::class, $clubDojo, array('form' => 'dojoUpdate', 'data_class' => ClubDojo::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoAddress($form->getData());

            return $this->redirectToRoute('club-dojoIndex');
        }

        return $this->render('Club/Dojo/address_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param ClubDojo $clubDojo
     * @return RedirectResponse|Response
     */
    #[Route('/supprimer-dojo/{clubDojo<\d+>}', name:'dojoDelete')]
    public function dojoDelete(Request $request, ClubDojo $clubDojo): RedirectResponse|Response
    {
        $form = $this->createForm(ClubType::class, $clubDojo, array('form' => 'dojoDelete', 'data_class' => ClubDojo::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoAddress($form->getData(), 'Delete');

            return $this->redirectToRoute('club-dojoIndex');
        }

        return $this->render('Club/Dojo/address_delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route('/ajouter-horaire', name:'lessonAdd')]
    public function lessonAdd(Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(ClubType::class, new ClubLesson(), array('form' => 'trainingCreate', 'data_class' => ClubLesson::class, 'choices' => $this->clubTools->getClub()->getClubDojos()));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoTraining($form->getData(), 'Add');

            return $this->redirectToRoute('club-dojoIndex');
        }

        return $this->render('Club/Dojo/training_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param ClubLesson $clubLesson
     * @return RedirectResponse|Response
     */
    #[Route('/modifier-horaire/{clubLesson<\d+>}', name:'lessonUpdate')]
    public function lessonUpdate(Request $request, ClubLesson $clubLesson): RedirectResponse|Response
    {
        $form = $this->createForm(ClubType::class, $clubLesson, array('form' => 'trainingUpdate', 'data_class' => ClubLesson::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoTraining($form->getData());

            return $this->redirectToRoute('club-dojoIndex');
        }

        return $this->render('Club/Dojo/training_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param ClubLesson $clubLesson
     * @return RedirectResponse|Response
     */
    #[Route('/supprimer-horaire/{clubLesson<\d+>}', name:'lessonDelete')]
    public function lessonDelete(Request $request, ClubLesson $clubLesson): RedirectResponse|Response
    {
        $form = $this->createForm(ClubType::class, $clubLesson, array('form' => 'trainingDelete', 'data_class' => ClubLesson::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoTraining($form->getData(), 'Delete');

            return $this->redirectToRoute('club-dojoIndex');
        }

        return $this->render('Club/Dojo/training_delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route('/ajouter-professeur-afa', name:'teacherAFAAdd')]
    public function teacherAFAAdd(Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(ClubType::class, new ClubTeacher(), array('form' => 'teacherAFACreate', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoTeacher($form->getData(), 'Add', $form->get('ClubTeacherMember')->getData());

            return $this->redirectToRoute('club-dojoIndex');
        }

        return $this->render('Club/Dojo/teacher_afa_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param ClubTeacher $teacher
     * @return RedirectResponse|Response
     */
    #[Route('/modifier-professeur-afa/{teacher<\d+>}', name:'teacherAFAUpdate')]
    public function teacherAFAUpdate(Request $request, ClubTeacher $teacher): RedirectResponse|Response
    {
        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacherAFAUpdate', 'data_class' => ClubTeacher::class));

        $form->get('ClubTeacherMember')->setData($teacher->getClubTeacherMember()->getMemberId());
        $form->get('ClubTeacherFirstname')->setData($teacher->getClubTeacherMember()->getMemberFirstname());
        $form->get('ClubTeacherName')->setData($teacher->getClubTeacherMember()->getMemberName());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoTeacher($form->getData());

            return $this->redirectToRoute('club-dojoIndex');
        }

        return $this->render('Club/Dojo/teacher_afa_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param ClubTeacher $teacher
     * @return RedirectResponse|Response
     */
    #[Route('/supprimer-professeur-afa/{teacher<\d+>}', name:'teacherAFADelete')]
    public function teacherAFADelete(Request $request, ClubTeacher $teacher): RedirectResponse|Response
    {
        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacherAFADelete', 'data_class' => ClubTeacher::class));

        $form->get('ClubTeacherMember')->setData($teacher->getClubTeacherMember()->getMemberId());
        $form->get('ClubTeacherFirstname')->setData($teacher->getClubTeacherMember()->getMemberFirstname());
        $form->get('ClubTeacherName')->setData($teacher->getClubTeacherMember()->getMemberName());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoTeacher($form->getData(), 'Delete');

            return $this->redirectToRoute('club-dojoIndex');
        }

        return $this->render('Club/Dojo/teacher_afa_delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route('/ajouter-professeur-etranger', name:'teacherForeignAdd')]
    public function teacherForeignAdd(Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(ClubType::class, new ClubTeacher(), array('form' => 'teacherForeignCreate', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoTeacher($form->getData(), 'Add');

            return $this->redirectToRoute('club-dojoIndex');
        }

        return $this->render('Club/Dojo/teacher_foreign_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param ClubTeacher $teacher
     * @return RedirectResponse|Response
     */
    #[Route('/modifier-professeur-etranger/{teacher<\d+>}', name:'teacherForeignUpdate')]
    public function teacherForeignUpdate(Request $request, ClubTeacher $teacher): RedirectResponse|Response
    {
        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacherForeignUpdate', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoTeacher($form->getData());

            return $this->redirectToRoute('club-dojoIndex');
        }

        return $this->render('Club/Dojo/teacher_foreign_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param ClubTeacher $teacher
     * @return RedirectResponse|Response
     */
    #[Route('/supprimer-professeur-etranger/{teacher<\d+>}', name:'teacherForeignDelete')]
    public function teacherForeignDelete(Request $request, ClubTeacher $teacher): RedirectResponse|Response
    {
        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacherForeignDelete', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->dojoTeacher($form->getData(), 'Delete');

            return $this->redirectToRoute('club-dojoIndex');
        }

        return $this->render('Club/Dojo/teacher_foreign_delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param SessionInterface $session
     * @param UserTools $userTools
     * @return RedirectResponse|Response
     */
    #[Route('/detail-association', name:'associationDetails')]
    public function associationDetails(Request $request, SessionInterface $session, UserTools $userTools): RedirectResponse|Response
    {
        $managedClubs = $userTools->listManagedClub($this->getUser());

        if ($request->query->has('change_actual'))
        {
            if (isset($managedClubs[$request->query->getInt('change_actual')]))
            {
                $session->set('actual_club', $request->query->get('change_actual'));
            }
            else
            {
                $session->set('actual_club', 0);
            }
        }

        $this->clubTools->setClub($this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_id' => $managedClubs[$session->get('actual_club')]]));

        $form = $this->createForm(ClubType::class, $this->clubTools->getClub(), array('form' => 'detailAssociation'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->clubTools->associationDetails($form->getData());

            return $this->redirectToRoute('common-index');
        }

        return $this->render('Club/Association/details.html.twig', array('form' => $form->createView(), 'club' => $this->clubTools->getClub(), 'nextManagedClub' => sizeof($managedClubs) == 1 ? 0 : $session->get('actual_club')+1));
    }

    /**
     * @param Request $request
     * @param SessionInterface $session
     * @param UserTools $userTools
     * @return Response
     */
    #[Route('/liste-des-membres', name:'membersList')]
    public function membersList(Request $request, SessionInterface $session, UserTools $userTools): Response
    {
        $managedClubs = $userTools->listManagedClub($this->getUser());

        if ($request->query->has('change_actual'))
        {
            if (isset($managedClubs[$request->query->getInt('change_actual')]))
            {
                $session->set('actual_club', $request->query->get('change_actual'));
            }
            else
            {
                $session->set('actual_club', 0);
            }
        }

        $this->clubTools->setClub($this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_id' => $managedClubs[$session->get('actual_club')]]));

        $members = $this->getDoctrine()->getRepository(Member::class)->getClubActiveMembers($this->clubTools->getClub());

        $old_members = $this->getDoctrine()->getRepository(Member::class)->getClubRecentInactiveMembers($this->clubTools->getClub());

        return $this->render('Club/Member/list.html.twig', array('members' => $members, 'old_members' => $old_members, 'club' => $this->clubTools->getClub(), 'nextManagedClub' => sizeof($managedClubs) == 1 ? 0 : $session->get('actual_club')+1));
    }

    /**
     * @param Member $member
     * @param MemberTools $memberTools
     * @return RedirectResponse|Response
     */
    #[Route('/donnees-personnelles/{member<\d+>}', name:'memberPersonalData')]
    public function memberPersonalData(Member $member, MemberTools $memberTools): RedirectResponse|Response
    {
        if ($member->getMemberActualClub() !== $this->clubTools->getClub())
        {
            return $this->redirectToRoute('club-membersList');
        }

        $memberTools->setMember($member);

        return $this->render('Club/Member/personal_data.html.twig', array('memberTools' => $memberTools));
    }

    /**
     * @param UserTools $userTools
     * @param SessionInterface $session
     * @param Request $request
     * @param Member $member
     * @return RedirectResponse|Response
     */
    #[Route('/creer-login/{member<\d+>}', name:'memberLoginCreate')]
    public function memberLoginCreate(UserTools $userTools, SessionInterface $session, Request $request, Member $member): RedirectResponse|Response
    {
        if ($member->getMemberActualClub() !== $this->clubTools->getClub())
        {
            return $this->redirectToRoute('club-membersList');
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

            return $this->redirectToRoute('club-membersList');
        }

        return $this->render('Club/Member/login_create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Member $member
     * @param MemberTools $memberTools
     * @return RedirectResponse|Response
     */
    #[Route('/detail-licence/{member<\d+>}', name:'memberLicenceDetail')]
    public function memberLicenceDetail(Member $member, MemberTools $memberTools): RedirectResponse|Response
    {
        if ($member->getMemberActualClub() !== $this->clubTools->getClub())
        {
            return $this->redirectToRoute('club-membersList');
        }

        $memberTools->setMember($member);

        return $this->render('Club/Member/licence_detail.html.twig', array('memberTools' => $memberTools));
    }

    /**
     * @param Member $member
     * @param MemberTools $memberTools
     * @return RedirectResponse|Response
     */
    #[Route('/detail-grades/{member<\d+>}', name:'memberGradesDetail')]
    public function memberGradesDetail(Member $member, MemberTools $memberTools): RedirectResponse|Response
    {
        if ($member->getMemberActualClub() !== $this->clubTools->getClub())
        {
            return $this->redirectToRoute('club-membersList');
        }

        $memberTools->setMember($member);

        return $this->render('Club/Member/grade_detail.html.twig', array('memberTools' => $memberTools));
    }

    /**
     * @param Member $member
     * @param MemberTools $memberTools
     * @return RedirectResponse|Response
     */
    #[Route('/detail-stages/{member<\d+>}', name:'memberStagesDetail')]
    public function memberStagesDetail(Member $member, MemberTools $memberTools): RedirectResponse|Response
    {
        if ($member->getMemberActualClub() !== $this->clubTools->getClub())
        {
            return $this->redirectToRoute('club-membersList');
        }

        $memberTools->setMember($member);

        return $this->render('Club/Member/stages_detail.html.twig', array('memberTools' => $memberTools));
    }

    /**
     * @param Member $member
     * @param MemberTools $memberTools
     * @return RedirectResponse|Response
     */
    #[Route('/detail-titres/{member<\d+>}', name:'memberTitlesDetail')]
    public function memberTitlesDetail(Member $member, MemberTools $memberTools): RedirectResponse|Response
    {
        if ($member->getMemberActualClub() !== $this->clubTools->getClub())
        {
            return $this->redirectToRoute('club-membersList');
        }

        $memberTools->setMember($member);

        return $this->render('Club/Member/titles_detail.html.twig', array('memberTools' => $memberTools));
    }

    /**
     * @param Request $request
     * @param Member $member
     * @param MemberTools $memberTools
     * @return RedirectResponse|Response
     */
    #[Route('/membre/{member<\d+>}/candidature', name:'memberApplication')]
    public function memberApplication(Request $request, Member $member, MemberTools $memberTools): RedirectResponse|Response
    {
        if ($member->getMemberActualClub() !== $this->clubTools->getClub())
        {
            return $this->redirectToRoute('club-membersList');
        }

        $memberTools->setMember($member);

        $form = $this->createForm(GradeType::class, $memberTools->getGrades()['exam']['grade'], array('form' => 'examApplication', 'data_class' => Grade::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $memberTools->application($form->getData());

            return $this->redirectToRoute('club-membersList');
        }

        return $this->render('Club/Member/exam_application.html.twig', array('form' => $form->createView(), 'exam' => $form->getData()->getGradeExam()));
    }

    /**
     * @param Request $request
     * @param Member $member
     * @param MemberTools $memberTools
     * @return RedirectResponse|Response
     */
    #[Route('/membre/{member<\d+>}/ajouter-kyu', name:'memberKyuAdd')]
    public function memberKyuAdd(Request $request, Member $member, MemberTools $memberTools): RedirectResponse|Response
    {
        $memberTools->setMember($member);

        if ($member->getMemberActualClub() !== $this->clubTools->getClub())
        {
            return $this->redirectToRoute('club-membersList');
        }

        $form = $this->createForm(GradeType::class, $memberTools->newKyu(), array('form' => 'kyuAdd', 'data_class' => Grade::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $memberTools->addKyu($form->getData());

            return $this->redirectToRoute('club-memberGradesDetail', array('member' => $member->getMemberId()));
        }

        return $this->render('Club/Member/kyu_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param Member $member
     * @param MemberTools $memberTools
     * @param Grade $grade
     * @return RedirectResponse|Response
     */
    #[Route('/membre/{member<\d+>}/modifier-kyu/{grade<\d+>}', name:'memberKyuModify')]
    public function memberKyuModify(Request $request, Member $member, MemberTools $memberTools, Grade $grade): RedirectResponse|Response
    {
        $memberTools->setMember($member);

        if ($member->getMemberActualClub() !== $this->clubTools->getClub())
        {
            return $this->redirectToRoute('club-membersList');
        }

        $form = $this->createForm(GradeType::class, $grade, array('form' => 'kyuModify', 'data_class' => Grade::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $memberTools->modifyKyu($form->getData());

            return $this->redirectToRoute('club-memberGradesDetail', array('member' => $member->getMemberId()));
        }

        return $this->render('Club/Member/kyu_modify.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param Member $member
     * @param MemberTools $memberTools
     * @return RedirectResponse|Response
     */
    #[Route('/membre/{member<\d+>}/supprimer-kyu/{grade<\d+>}', name:'memberKyuDelete')]
    public function memberKyuDelete(Request $request, Member $member, MemberTools $memberTools): RedirectResponse|Response
    {
        $memberTools->setMember($member);

        if ($member->getMemberActualClub() !== $this->clubTools->getClub())
        {
            return $this->redirectToRoute('club-membersList');
        }

        $form = $this->createForm(GradeType::class, $memberTools->newKyu(), array('form' => 'addKyu', 'data_class' => Grade::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $memberTools->addKyu($form->getData());

            return $this->redirectToRoute('club-memberGradesDetail', array('member' => $member->getMemberId()));
        }

        return $this->render('Club/Member/kyu_modify.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param SessionInterface $session
     * @param UserTools $userTools
     * @return Response
     */
    #[Route('/liste-gestionnaire', name:'managerIndex')]
    public function managerIndex(Request $request, SessionInterface $session, UserTools $userTools): Response
    {
        $managedClubs = $userTools->listManagedClub($this->getUser());

        if ($request->query->has('change_actual'))
        {
            if (isset($managedClubs[$request->query->getInt('change_actual')]))
            {
                $session->set('actual_club', $request->query->get('change_actual'));
            }
            else
            {
                $session->set('actual_club', 0);
            }
        }

        $this->clubTools->setClub($this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_id' => $managedClubs[$session->get('actual_club')]]));

        return $this->render('Club/Manager/index.html.twig', array('clubTools' => $this->clubTools, 'nextManagedClub' => sizeof($managedClubs) == 1 ? 0 : $session->get('actual_club')+1));
    }

    /**
     * @param Request $request
     * @param SessionInterface $session
     * @param UserTools $userTools
     * @return Response
     */
    #[Route('/rechercher-membres', name:'searchMembers')]
    public function searchMembers(Request $request, SessionInterface $session, UserTools $userTools): Response
    {
        $managedClubs = $userTools->listManagedClub($this->getUser());

        if ($request->query->has('change_actual'))
        {
            if (isset($managedClubs[$request->query->getInt('change_actual')]))
            {
                $session->set('actual_club', $request->query->get('change_actual'));
            }
            else
            {
                $session->set('actual_club', 0);
            }
        }

        $this->clubTools->setClub($this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_id' => $managedClubs[$session->get('actual_club')]]));

        $search = null; $results = null;

        $form = $this->createForm(ClubType::class, $search, array('form' => 'searchMembers', 'data_class' => null));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $results = $this->getDoctrine()->getRepository(Member::class)->getFullSearchClubMembers($form->get('Search')->getData(), $managedClubs[$session->get('actual_club')]);

            return $this->render('Club/Member/search.html.twig', array('form' => $form->createView(), 'results' => $results, 'clubTools' => $this->clubTools, 'nextManagedClub' => sizeof($managedClubs) == 1 ? 0 : $session->get('actual_club')+1));
        }

        return $this->render('Club/Member/search.html.twig', array('form' => $form->createView(), 'results' => $results, 'clubTools' => $this->clubTools, 'nextManagedClub' => sizeof($managedClubs) == 1 ? 0 : $session->get('actual_club')+1));
    }
}
