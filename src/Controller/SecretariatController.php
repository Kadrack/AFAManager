<?php
// src/Controller/SecretariatController.php
namespace App\Controller;

use App\Entity\Club;
use App\Entity\ClubHistory;
use App\Entity\ClubTeacher;
use App\Entity\Commission;
use App\Entity\CommissionMember;
use App\Entity\Grade;
use App\Entity\GradeSession;
use App\Entity\Member;
use App\Entity\MemberLicence;
use App\Entity\MemberModification;
use App\Entity\MemberPrintout;
use App\Entity\SecretariatSupporter;
use App\Entity\Training;
use App\Entity\TrainingAddress;
use App\Entity\TrainingSession;
use App\Entity\User;

use App\Form\ClubType;
use App\Form\GradeType;
use App\Form\MemberType;
use App\Form\SecretariatType;
use App\Form\TrainingType;
use App\Form\UserType;

use App\Service\ClubTools;
use App\Service\ListData;
use App\Service\PhotoUploader;
use App\Service\UserTools;

use DateTime;

use Exception;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/secretariat", name="secretariat_")
 *
 * @IsGranted("ROLE_SECRETARIAT")
 */
class SecretariatController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('Secretariat/index.html.twig');
    }

    /**
     * @Route("/liste_sympathisant", name="supporter_index")
     */
    public function supporterIndex()
    {
        $supporters = $this->getDoctrine()->getRepository(SecretariatSupporter::class)->findAll();

        return $this->render('Secretariat/Supporter/index.html.twig', array('supporters' => $supporters));
    }

    /**
     * @Route("/ajouter_sympathisant", name="supporter_create")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function supporterAdd(Request $request)
    {
        $form = $this->createForm(SecretariatType::class, new SecretariatSupporter(), array('form' => 'supporter_create', 'data_class' => SecretariatSupporter::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $address = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($address);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat_supporter_index');
        }

        return $this->render('Secretariat/Supporter/add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/modifier_sympathisant/{supporter<\d+>}", name="supporter_update")
     *
     * @param Request $request
     * @param SecretariatSupporter $supporter
     * @return RedirectResponse|Response
     */
    public function supporterUpdate(Request $request, SecretariatSupporter $supporter)
    {
        $form = $this->createForm(SecretariatType::class, $supporter, array('form' => 'supporter_update', 'data_class' => SecretariatSupporter::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat_supporter_index');
        }

        return $this->render('Secretariat/Supporter/update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/supprimer_sympathisant/{supporter<\d+>}", name="supporter_delete")
     *
     * @param Request $request
     * @param SecretariatSupporter $supporter
     * @return RedirectResponse|Response
     */
    public function supporterDelete(Request $request, SecretariatSupporter $supporter)
    {
        $form = $this->createForm(SecretariatType::class, $supporter, array('form' => 'supporter_delete', 'data_class' => SecretariatSupporter::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($supporter);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat_supporter_index');
        }

        return $this->render('Secretariat/Supporter/delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/liste_clubs", name="club_list")
     */
    public function clubList()
    {
        $provinces = new ListData();

        $active_clubs = array();

        $active_list = $this->getDoctrine()->getRepository(Club::class)->getActiveClubs();

        foreach ($active_list as $club)
        {
            $club['Province'] = $provinces->getProvince($club['Province']);

            $active_clubs[$club['Province']]['Clubs'][$club['Id']] = $club;

            if (!isset($active_clubs[$club['Province']]['name']))
            {
                $active_clubs[$club['Province']]['province'] = $club['Province'];
            }
        }

        $inactive_list = $this->getDoctrine()->getRepository(Club::class)->getInactiveClubs();

        return $this->render('Secretariat/Club/list.html.twig', array('active_clubs' => $active_clubs, 'inactive_clubs' => $inactive_list));
    }

    /**
     * @Route("/index_dojo/{club<\d+>}", name="dojo_index")
     * @param Club $club
     * @param ClubTools $clubTools
     * @return Response
     */
    public function dojoIndex(Club $club, ClubTools $clubTools)
    {
        $clubTools->setClub($club);

        return $this->render('Secretariat/Dojo/index.html.twig', array('clubTools' => $clubTools));
    }

    /**
     * @Route("/ajouter_dojo/{club<\d+>}", name="dojo_address_add")
     * @param Request $request
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    public function dojoAddressAdd(Request $request, Club $club, ClubTools $clubTools)
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, new TrainingAddress(), array('form' => 'dojo_create', 'data_class' => TrainingAddress::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoAddress($form->getData(), 'Add');

            return $this->redirectToRoute('secretariat_dojo_index', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/address_add.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @Route("/modifier_dojo/{address<\d+>}/{club<\d+>}", name="dojo_address_update")
     * @param Request $request
     * @param TrainingAddress $address
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    public function dojoAddressUpdate(Request $request, TrainingAddress $address, Club $club, ClubTools $clubTools)
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, $address, array('form' => 'dojo_update', 'data_class' => TrainingAddress::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoAddress($form->getData());

            return $this->redirectToRoute('secretariat_dojo_index', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/address_update.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @Route("/supprimer_dojo/{address<\d+>}/{club<\d+>}", name="dojo_address_delete")
     * @param Request $request
     * @param TrainingAddress $address
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    public function dojoAddressDelete(Request $request, TrainingAddress $address, Club $club, ClubTools $clubTools)
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, $address, array('form' => 'dojo_delete', 'data_class' => TrainingAddress::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoAddress($form->getData(), 'Delete');

            return $this->redirectToRoute('secretariat_dojo_index', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/address_delete.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @Route("/ajouter_horaire/{club<\d+>}", name="dojo_training_add")
     * @param Request $request
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    public function dojoTrainingAdd(Request $request, Club $club, ClubTools $clubTools)
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, new Training(), array('form' => 'training_create', 'data_class' => Training::class, 'choices' => $club->getClubAddresses()));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoTraining($form->getData(), 'Add');

            return $this->redirectToRoute('secretariat_dojo_index', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/training_add.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @Route("/modifier_horaire/{training<\d+>}/{club<\d+>}", name="dojo_training_update")
     * @param Request $request
     * @param Training $training
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    public function dojoTrainingUpdate(Request $request, Training $training, Club $club, ClubTools $clubTools)
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, $training, array('form' => 'training_update', 'data_class' => Training::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoTraining($form->getData());

            return $this->redirectToRoute('secretariat_dojo_index', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/training_update.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @Route("/supprimer_horaire/{training<\d+>}/{club<\d+>}", name="dojo_training_delete")
     * @param Request $request
     * @param Training $training
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    public function dojoTrainingDelete(Request $request, Training $training, Club $club, ClubTools $clubTools)
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, $training, array('form' => 'training_delete', 'data_class' => Training::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoTraining($form->getData(), 'Delete');

            return $this->redirectToRoute('secretariat_dojo_index', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/training_delete.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @Route("/ajouter_professeur_afa/{club<\d+>}", name="dojo_teacher_afa_add")
     * @param Request $request
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    public function dojoTeacherAFAAdd(Request $request, Club $club, ClubTools $clubTools)
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, new ClubTeacher(), array('form' => 'teacher_afa_create', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoTeacher($form->getData(), 'Add', $form->get('ClubTeacherMember')->getData());

            return $this->redirectToRoute('secretariat_dojo_index', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/teacher_afa_add.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @Route("/modifier_professeur_afa/{teacher<\d+>}/{club<\d+>}", name="dojo_teacher_afa_update")
     * @param Request $request
     * @param ClubTeacher $teacher
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    public function dojoTeacherAFAUpdate(Request $request, ClubTeacher $teacher, Club $club, ClubTools $clubTools)
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_afa_update', 'data_class' => ClubTeacher::class));

        $form->get('ClubTeacherMember')->setData($teacher->getClubTeacherMember()->getMemberId());
        $form->get('ClubTeacherFirstname')->setData($teacher->getClubTeacherMember()->getMemberFirstname());
        $form->get('ClubTeacherName')->setData($teacher->getClubTeacherMember()->getMemberName());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoTeacher($form->getData());

            return $this->redirectToRoute('secretariat_dojo_index', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/teacher_afa_update.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @Route("/supprimer_professeur_afa/{teacher<\d+>}/{club<\d+>}", name="dojo_teacher_afa_delete")
     * @param Request $request
     * @param ClubTeacher $teacher
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    public function dojoTeacherAFADelete(Request $request, ClubTeacher $teacher, Club $club, ClubTools $clubTools)
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_afa_delete', 'data_class' => ClubTeacher::class));

        $form->get('ClubTeacherMember')->setData($teacher->getClubTeacherMember()->getMemberId());
        $form->get('ClubTeacherFirstname')->setData($teacher->getClubTeacherMember()->getMemberFirstname());
        $form->get('ClubTeacherName')->setData($teacher->getClubTeacherMember()->getMemberName());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoTeacher($form->getData(), 'Delete');

            return $this->redirectToRoute('secretariat_dojo_index', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/teacher_afa_delete.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @Route("/ajouter_professeur_etranger/{club<\d+>}", name="dojo_teacher_foreign_add")
     * @param Request $request
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    public function dojoTeacherForeignAdd(Request $request, Club $club, ClubTools $clubTools)
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, new ClubTeacher(), array('form' => 'teacher_foreign_create', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoTeacher($form->getData(), 'Add', $form->get('ClubTeacherMember')->getData());

            return $this->redirectToRoute('secretariat_dojo_index', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/teacher_foreign_add.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @Route("/modifier_professeur_etranger/{teacher<\d+>}/{club<\d+>}", name="dojo_teacher_foreign_update")
     * @param Request $request
     * @param ClubTeacher $teacher
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    public function dojoTeacherForeignUpdate(Request $request, ClubTeacher $teacher, Club $club, ClubTools $clubTools)
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_foreign_update', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoTeacher($form->getData());

            return $this->redirectToRoute('secretariat_dojo_index', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/teacher_foreign_update.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @Route("/supprimer_professeur_etranger/{teacher<\d+>}/{club<\d+>}", name="dojo_teacher_foreign_delete")
     * @param Request $request
     * @param ClubTeacher $teacher
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    public function dojoTeacherForeignDelete(Request $request, ClubTeacher $teacher, Club $club, ClubTools $clubTools)
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_foreign_delete', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoTeacher($form->getData(), 'Delete');

            return $this->redirectToRoute('secretariat_dojo_index', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/teacher_foreign_delete.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @Route("/detail_association/{club<\d+>}", name="association_details")
     * @param Request $request
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    public function associationDetails(Request $request, Club $club, ClubTools $clubTools)
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, $club, array('form' => 'detail_association'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->associationDetails($form->getData());

            return $this->redirectToRoute('secretariat_club_list');
        }

        return $this->render('Secretariat/Club/Association/details.html.twig', array('form' => $form->createView(), 'club' => $club));
    }

    /**
     * @Route("/rechercher_membres", name="search_members")
     * @param Request $request
     * @return Response
     */
    public function searchMembers(Request $request)
    {
        $search = null; $results = null;

        $form = $this->createForm(SecretariatType::class, $search, array('form' => 'search_members', 'data_class' => null));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $results = $this->getDoctrine()->getRepository(Member::class)->getFullSearchMembers($form->get('Search')->getData());

            return $this->render('Secretariat/Member/search.html.twig', array('form' => $form->createView(), 'results' => $results));
        }

        return $this->render('Secretariat/Member/search.html.twig', array('form' => $form->createView(), 'results' => $results));
    }

    /**
     * @Route("/liste_membres/{club<\d+>}", name="members_active")
     * @param SessionInterface $session
     * @param Club $club
     * @return Response
     */
    public function membersActive(SessionInterface $session, Club $club)
    {
        $members = $this->getDoctrine()->getRepository(Member::class)->getClubActiveMembers($club);

        $limit = new DateTime('+3 month today');

        $session->set('origin', 'active');

        return $this->render('Secretariat/Club/Member/list_active.html.twig', array('members' => $members, 'club' => $club, 'limit' => $limit));
    }

    /**
     * @Route("/liste_anciens_membres/{club<\d+>}", name="members_ancient")
     * @param Club $club
     * @return Response
     */
    public function membersAncient(Club $club)
    {
        $members = $this->getDoctrine()->getRepository(Member::class)->getClubInactiveMembers($club);

        $limit = new DateTime('+3 month today');

        return $this->render('Secretariat/Club/Member/list_ancient.html.twig', array('members' => $members == null ? null : $members, 'club' => $club, 'limit' => $limit));
    }

    /**
     * @Route("/creer_club", name="club_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function clubCreate(Request $request)
    {
        $form = $this->createForm(ClubType::class, new Club());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $club = $form->getData();

            $history = new ClubHistory();

            $history->setClubHistoryStatus(1);
            $history->setClubHistoryUpdate($form->get('ClubMembership')->getData());

            $club->addClubHistories($history);
            $club->setClubLastHistory($history);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($club);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat_club_list');
        }

        return $this->render('Secretariat/Club/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/desaffilier_club/{club<\d+>}", name="club_disaffiliate")
     * @param Request $request
     * @param Club $club
     * @return RedirectResponse|Response
     */
    public function clubDisaffiliate(Request $request, Club $club)
    {
        $history = new ClubHistory();

        $form = $this->createForm(ClubType::class, $history, array('form' => 'history_entry', 'data_class' => ClubHistory::class));

        $form->get('ClubHistoryUpdate')->setData(new DateTime('today'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $history = $form->getData();

            $history->setClubHistoryStatus(2);

            $club->addClubHistories($history);
            $club->setClubLastHistory($history);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($history);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat_club_list');
        }

        return $this->render('Secretariat/Club/disaffiliate.html.twig', array('form' => $form->createView(), 'club' => $club));
    }

    /**
     * @Route("/reaffilier_club/{club<\d+>}", name="club_reassign")
     * @param Request $request
     * @param Club $club
     * @return RedirectResponse|Response
     */
    public function clubReassign(Request $request, Club $club)
    {
        $history = new ClubHistory();

        $form = $this->createForm(ClubType::class, $history, array('form' => 'history_entry', 'data_class' => ClubHistory::class));

        $form->get('ClubHistoryUpdate')->setData(new DateTime('today'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $history = $form->getData();

            $history->setClubHistoryStatus(1);

            $club->addClubHistories($history);
            $club->setClubLastHistory($history);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($history);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat_club_list');
        }

        return $this->render('Secretariat/Club/reassign.html.twig', array('form' => $form->createView(), 'club' => $club));
    }

    /**
     * @Route("/creer_membre/club/{club<\d+>}", name="member_create")
     * @param Request $request
     * @param PhotoUploader $photoUploader
     * @param Club $club
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function memberCreate(Request $request, PhotoUploader $photoUploader, Club $club)
    {
        $form = $this->createForm(MemberType::class, new Member());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $member = $form->getData();

            $licence = new MemberLicence();

            $licence->setMemberLicenceStatus(1);
            $licence->setMemberLicenceClub($club);
            $licence->setMemberLicenceUpdate(new DateTime('today'));
            $licence->setMemberLicenceMedicalCertificate($form->get('MemberLicenceMedicalCertificate')->getData());
            $licence->setMemberLicenceDeadline(new DateTime('+1 year '.$licence->getMemberLicenceMedicalCertificate()->format('Y-m-d')));

            $member->addMemberLicences($licence);

            $member->setMemberFirstLicence($licence);
            $member->setMemberLastLicence($licence);
            $member->setMemberActualClub($club);
            $member->setMemberPhoto($form['MemberPhoto']->getData() == null ? 'nophoto.png' : $photoUploader->upload($form['MemberPhoto']->getData()));

            $grade = new Grade();

            $rank = $form->get('GradeRank')->getData() == null ? 1 : $form->get('GradeRank')->getData();

            $grade->setGradeRank($rank);
            $grade->setGradeMember($member);
            $grade->setGradeDate($licence->getMemberLicenceUpdate());
            $grade->setGradeClub($club);

            if ($grade->getGradeRank() < 7)
            {
                $grade->setGradeStatus(4);
            }
            else
            {
                $grade->setGradeStatus(6);
            }

            $member->setMemberLastGrade($grade);
            $member->addMemberGrades($grade);

            $licence->setMemberLicenceGrade($grade);

            $stamp = new MemberPrintout();

            $stamp->setMemberPrintoutAction(1);
            $stamp->setMemberPrintoutLicence($licence);
            $stamp->setMemberPrintoutCreation(new DateTime('today'));

            $card = new MemberPrintout();

            $card->setMemberPrintoutAction(2);
            $card->setMemberPrintoutLicence($licence);
            $card->setMemberPrintoutCreation(new DateTime('today'));

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($member);
            $entityManager->persist($stamp);
            $entityManager->persist($card);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat_members_active', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Club/Member/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/detail_licence/{member<\d+>}/club/{club<\d+>}", name="member_licence_detail")
     *
     * @param Club $club
     * @param Member $member
     * @return Response
     */
    public function memberLicenceDetail(Club $club, Member $member)
    {
        $licence_history = $this->getDoctrine()->getRepository(MemberLicence::class)->findBy(['member_licence' => $member->getMemberId()], ['member_licence_id' => 'DESC']);

        $next_renew = $licence_history[0]->getMemberLicenceDeadline() < new DateTime('+3 month today');

        return $this->render('Secretariat/Club/Member/licence_detail.html.twig', array('member' => $member, 'club' => $club, 'licence_history' => $licence_history, 'next_renew' => $next_renew));
    }

    /**
     * @Route("/detail_personnel/{member<\d+>}/club/{club<\d+>}", name="member_personal_detail")
     *
     * @param Request $request
     * @param PhotoUploader $photoUploader
     * @param Club $club
     * @param Member $member
     * @return RedirectResponse|Response
     */
    public function memberPersonalDetail(Request $request, PhotoUploader $photoUploader, Club $club, Member $member)
    {
        $form = $this->createForm(SecretariatType::class, $member, array('form' => 'member_update', 'data_class' => Member::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($form['MemberPhoto']->getData() != null)
            {
                $member->setMemberPhoto($photoUploader->upload($form['MemberPhoto']->getData(), $member->getMemberPhoto()));
            }

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat_members_active', array('club' => $club->getClubId(), 'member' => $member->getMemberId()));
        }

        return $this->render('Secretariat/Club/Member/personal_detail.html.twig', array('form' => $form->createView(), 'member' => $member));
    }

    /**
     * @Route("/formulaire_renouvellement/{member<\d+>}/club/{club<\d+>}", name="member_form_renew")
     *
     * @param Club $club
     * @param Member $member
     * @return RedirectResponse|Response
     */
    public function memberFormRenew(Club $club, Member $member)
    {
        $listData = new ListData();

        $output_file = "./licence_out.rtf";

        $fh = fopen($output_file, 'a') or die("can't open file");

        $file = file_get_contents('../private/licence.rtf', true);

        $file = substr($file, 1, strlen($file)-2);

        fwrite($fh, '{');

        $old = array('\{\{TITRE\}\}', '\{\{SEXE\}\}', '\{\{NOM\}\}', '\{\{PRENOM\}\}', '\{\{DOJO_ID\}\}', '\{\{DOJO_NOM\}\}', '\{\{ADRESSE\}\}', '\{\{CODE_POSTALE\}\}', '\{\{LOCALITE\}\}', '\{\{DATE_DE_NAISSANCE\}\}', '\{\{GSM\}\}', '\{\{EMAIL\}\}', '\{\{LICENCE_ID\}\}', '\{\{DATE_ECHEANCE_FR\}\}', '\{\{ENFANT\}\}', '\{\{ADULTE\}\}', '\{\{PAYS\}\}');

        $children_limit = new DateTime('-14 year today');

        $newphrase = '';

        unset($new);

        if ($member->getMemberSex() == 1)
        {
            $title='Monsieur';
            $sex='Masculin';
        }
        else
        {
            $title='Madamme';
            $sex="Féminin";
        }

        if ($member->getMemberBirthday() > $children_limit)
        {
            $children='X';
            $adult='';
        }
        else
        {
            $children='';
            $adult='X';
        }

        $new = array($title, utf8_decode($sex), utf8_decode($member->getMemberName()), utf8_decode($member->getMemberFirstname()), utf8_decode($club->getClubId()), utf8_decode($club->getClubName()), utf8_decode($member->getMemberAddress()), utf8_decode($member->getMemberZip()), utf8_decode($member->getMemberCity()), utf8_decode($member->getMemberBirthday()->format('d/m/Y')), utf8_decode($member->getMemberPhone()), utf8_decode($member->getMemberEmail()), utf8_decode($member->getMemberId()), utf8_decode($member->getMemberLastLicence()->getMemberLicenceDeadline()->format('d/m/Y')), $children, $adult, utf8_decode($listData->getCountryName($member->getMemberCountry())));

        $newphrase .= str_replace($old, $new, $file);

        fwrite($fh, $newphrase);

        fwrite($fh, '}');
        fclose($fh);

        $response = new BinaryFileResponse($output_file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);

        return $response->deleteFileAfterSend();
    }

    /**
     * @Route("/renouvellement_licence/{member<\d+>}/club/{club<\d+>}", name="member_licence_renew")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Club $club
     * @param Member $member
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function memberLicenceRenew(SessionInterface $session, Request $request, Club $club, Member $member)
    {
        $licence_old = $member->getMemberLastLicence();

        $licence_old->setMemberLicenceStatus(0);

        $licence_new = new MemberLicence();

        $licence_new->setMemberLicence($member);
        $licence_new->setMemberLicenceClub($club);
        $licence_new->setMemberLicenceUpdate(new DateTime('today'));
        $licence_new->setMemberLicenceDeadline(new DateTime('+1 year '.$licence_old->getMemberLicenceDeadline()->format('Y-m-d')));
        $licence_new->setMemberLicenceStatus(1);

        if ($member->getMemberLastGrade() == null)
        {
            $kyu = true;
        }
        else if ($member->getMemberLastGrade()->getGradeRank() > 6)
        {
            $kyu = false;
        }
        else
        {
            $kyu = true;
        }

        if ($kyu)
        {
            $form = $this->createForm(MemberType::class, $licence_new, array('form' => 'licence_renew_kyu', 'data_class' => MemberLicence::class));

            $form->get('GradeKyuRank')->setData($licence_old->getMemberLicenceGrade() == null ? null : $licence_old->getMemberLicenceGrade()->getGradeRank());
        }
        else
        {
            $form = $this->createForm(MemberType::class, $licence_new, array('form' => 'licence_renew', 'data_class' => MemberLicence::class));
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $licence_new = $form->getData();

            $stamp = new MemberPrintout();

            $stamp->setMemberPrintoutAction(1);
            $stamp->setMemberPrintoutLicence($licence_new);
            $stamp->setMemberPrintoutCreation(new DateTime('today'));

            if ($licence_new->getMemberLicenceClub() != $licence_old->getMemberLicenceClub())
            {
                $card = new MemberPrintout();

                $card->setMemberPrintoutAction(2);
                $card->setMemberPrintoutLicence($licence_new);
                $card->setMemberPrintoutCreation(new DateTime('today'));

                $entityManager->persist($card);
            }

            if ($kyu)
            {
                if (($licence_old->getMemberLicenceGrade() == null) and ($form->get('GradeKyuRank')->getData() != null))
                {
                    $update = true;
                }
                else if ($licence_old->getMemberLicenceGrade() != null)
                {
                    $update = $licence_old->getMemberLicenceGrade()->getGradeRank() < $form->get('GradeKyuRank')->getData();
                }
                else
                {
                    $update = false;
                }

                if ($update)
                {
                    $grade = new Grade();

                    $grade->setGradeRank($form->get('GradeKyuRank')->getData());
                    $grade->setGradeMember($member);
                    $grade->setGradeStatus(4);

                    $member->setMemberLastGrade($grade);

                    $licence_new->setMemberLicenceGrade($grade);

                    $entityManager->persist($grade);
                }
            }

            $member->setMemberLastLicence($licence_new);
            $member->setMemberActualClub($licence_new->getMemberLicenceClub());

            $entityManager->persist($licence_new);
            $entityManager->persist($stamp);
            $entityManager->flush();

            if ($session->get('origin') == 'active')
            {
                return $this->redirectToRoute('secretariat_members_active', array('club' => $club->getClubId(), 'member' => $member->getMemberId()));
            }
            else
            {
                return $this->redirectToRoute('secretariat_members_ancient', array('club' => $club->getClubId()));
            }
        }

        return $this->render('Secretariat/Club/Member/licence_renew.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/modification_renouvellement/{renew<\d+>}/licence/{member<\d+>}/club/{club<\d+>}", name="member_licence_renew_update")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Club $club
     * @param Member $member
     * @param MemberLicence $renew
     * @return RedirectResponse|Response
     */
    public function memberLicenceRenewUpdate(SessionInterface $session, Request $request, Club $club, Member $member, MemberLicence $renew)
    {
        $grade = $renew->getMemberLicenceGrade() == null ? new Grade() : $renew->getMemberLicenceGrade();

        $kyus = $member->getMemberGrades();

        if ($member->getMemberLastGrade() == null)
        {
            $kyu = true;
        }
        else if ($member->getMemberLastGrade()->getGradeRank() > 6)
        {
            $kyu = false;
        }
        else
        {
            $kyu = true;
        }

        if ($kyu)
        {
            $form = $this->createForm(MemberType::class, $renew, array('form' => 'licence_renew_kyu', 'data_class' => MemberLicence::class));

            $form->get('GradeKyuRank')->setData($member->getMemberLastGrade() == null ? null : $member->getMemberLastGrade()->getGradeRank());
        }
        else
        {
            $form = $this->createForm(MemberType::class, $renew, array('form' => 'licence_renew', 'data_class' => MemberLicence::class));
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            if ($kyu)
            {
                if ($grade->getGradeRank() != $form->get('GradeKyuRank')->getData())
                {
                    $update = true;

                    foreach ($kyus as $kyu)
                    {
                        if ($kyu->getGradeRank() == $form->get('GradeKyuRank')->getData())
                        {
                            $update = false;
                        }
                    }

                    if ($update)
                    {
                        $grade->setGradeRank($form->get('GradeKyuRank')->getData());

                        $grade->setGradeMember($member);

                        if ($renew->getMemberLicenceGrade() == null)
                        {
                            $member->setMemberLastGrade($grade);

                            $renew->setMemberLicenceGrade($grade);
                        }
                    }
                }
            }

            $member->setMemberActualClub($this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_id' => $form->get('MemberLicenceClub')->getData()]));

            $renew->setMemberLicenceUpdate(new DateTime('today'));

            $entityManager->flush();

            if ($session->get('origin') == 'active')
            {
                return $this->redirectToRoute('secretariat_members_active', array('club' => $club->getClubId(), 'member' => $member->getMemberId()));
            }
            else
            {
                return $this->redirectToRoute('secretariat_members_ancient', array('club' => $club->getClubId()));
            }
        }

        return $this->render('Secretariat/Club/Member/licence_renew.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/sessions-examen", name="exam_index")
     */
    public function examIndex()
    {
        $sessions = $this->getDoctrine()->getRepository(GradeSession::class)->findBy(['grade_session_type' => 1], ['grade_session_date' => 'DESC', 'grade_session_type' => 'DESC']);

        return $this->render('Secretariat/Exam/index.html.twig', array('sessions' => $sessions));
    }

    /**
     * @Route("/session-examen/creer", name="exam_create")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function examCreate(Request $request)
    {
        $form = $this->createForm(GradeType::class, new GradeSession());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $session = $form->getData();

            $session->setGradeSessionType(1);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat_exam_index');
        }

        return $this->render('Secretariat/Exam/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/session-examen/{session<\d+>}/modifier", name="exam_update")
     *
     * @param Request $request
     * @param GradeSession $session
     * @return RedirectResponse|Response
     */
    public function examUpdate(Request $request, GradeSession $session)
    {
        $form = $this->createForm(GradeType::class, $session, array('form' => 'exam_update'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat_exam_index', array('session' => $session->getGradeSessionId()));
        }

        return $this->render('Secretariat/Exam/update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/liste_stages", name="training_index")
     */
    public function trainingIndex()
    {
        $trainings = $this->getDoctrine()->getRepository(Training::class)->getActiveTrainings(4);

        return $this->render('Secretariat/Training/index.html.twig', array('trainings' => count($trainings) == 0 ? null : $trainings));
    }

    /**
     * @Route("/stage_creer", name="training_create")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function trainingCreate(Request $request)
    {
        $form = $this->createForm(TrainingType::class, new Training());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $training = $form->getData();

            $session = $training->getSession();

            $duration = date_diff($session->getTrainingSessionEndingHour(), $session->getTrainingSessionStartingHour());

            $session->setTrainingSessionDuration($duration->format('%h')*60 + $duration->format('%i'));

            $training->addTrainingSessions($session);
            $training->setTrainingFirstSession($session);
            $training->setTrainingTotalSessions(1);
            $training->setTrainingType(4);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($training);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat_training_index');
        }

        return $this->render('Secretariat/Training/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/stage/{training<\d+>}/detail", name="training_detail")
     *
     * @param Training $training
     * @return Response
     */
    public function trainingDetail(Training $training)
    {
        $sessions = $this->getDoctrine()->getRepository(TrainingSession::class)->getTrainingSessions($training->getTrainingId());

        return $this->render('Secretariat/Training/detail.html.twig', array('training' => $training, 'sessions' => $sessions));
    }

    /**
     * @Route("/stage/{training<\d+>}/modifier", name="training_update")
     *
     * @param Request $request
     * @param Training $training
     * @return RedirectResponse|Response
     */
    public function trainingUpdate(Request $request, Training $training)
    {
        $form = $this->createForm(TrainingType::class, $training, array('form' => 'training_update'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat_training_index');
        }

        return $this->render('Secretariat/Training/update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/stage/{training<\d+>}/supprimer", name="training_delete")
     *
     * @param Request $request
     * @param Training $training
     * @return RedirectResponse|Response
     */
    public function traningDelete(Request $request, Training $training)
    {
        $form = $this->createForm(TrainingType::class, $training, array('form' => 'training_delete'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($training);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat_training_index');
        }

        return $this->render('Secretariat/Training/update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/stage/{training<\d+>}/ajouter-session", name="training_session_add")
     *
     * @param Request $request
     * @param Training $training
     * @return RedirectResponse|Response
     */
    public function trainingSessionAdd(Request $request, Training $training)
    {
        $form = $this->createForm(TrainingType::class, new TrainingSession(), array('form' => 'session_add', 'data_class' => TrainingSession::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $session = $form->getData();

            $duration = date_diff($session->getTrainingSessionEndingHour(), $session->getTrainingSessionStartingHour());

            $session->setTrainingSessionDuration($duration->format('%h')*60 + $duration->format('%i'));
            $session->setTraining($training);

            $training->setTrainingTotalSessions($training->getTrainingTotalSessions() + 1);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat_training_detail', array('training' => $training->getTrainingId()));
        }

        return $this->render('Secretariat/Training/session_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/stage/{training<\d+>}/modifier-session/{session<\d+>}", name="training_session_update")
     *
     * @param Request $request
     * @param Training $training
     * @param TrainingSession $session
     * @return RedirectResponse|Response
     */
    public function trainingSessionUpdate(Request $request, Training $training, TrainingSession $session)
    {
        $form = $this->createForm(TrainingType::class, $session, array('form' => 'session_add', 'data_class' => TrainingSession::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $session = $form->getData();

            $duration = date_diff($session->getTrainingSessionEndingHour(), $session->getTrainingSessionStartingHour());

            $session->setTrainingSessionDuration($duration->format('%h')*60 + $duration->format('%i'));

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat_training_detail', array('training' => $training->getTrainingId()));
        }

        return $this->render('Secretariat/Training/session_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/stage/{training<\d+>}/supprimer-session/{session<\d+>}", name="training_session_delete")
     *
     * @param Request $request
     * @param Training $training
     * @param TrainingSession $session
     * @return RedirectResponse|Response
     */
    public function trainingSessionDelete(Request $request, Training $training, TrainingSession $session)
    {
        $form = $this->createForm(TrainingType::class, $session, array('form' => 'session_delete', 'data_class' => TrainingSession::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            if (count($training->getTrainingSessions()) == 1)
            {
                $entityManager->remove($training);

                $index = true;
            }
            else
            {
                $entityManager->remove($session);

                $training->setTrainingTotalSessions($training->getTrainingTotalSessions() - 1);

                $index = false;
            }

            $entityManager->flush();

            if ($index)
            {
                return $this->redirectToRoute('secretariat_training_index');
            }
            else
            {
                return $this->redirectToRoute('secretariat_training_detail', array('training' => $training->getTrainingId()));
            }

        }

        return $this->render('Secretariat/Training/session_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/liste_validations_modifications_membres", name="member_modification_validation_index")
     */
    public function memberModificationValidationIndex()
    {
        $modifications = $this->getDoctrine()->getRepository(Member::class)->getMemberModification();

        return $this->render('Secretariat/Member/Modification/index.html.twig', array('modifications' => $modifications));
    }

    /**
     * @Route("/validation_modifications_membre/{member<\d+>}", name="member_modification_validation_validate")
     *
     * @param Request $request
     * @param Member $member
     * @return RedirectResponse|Response
     */
    public function memberModificationValidationValidate(Request $request, Member $member)
    {
        $modification = $member->getMemberModification();

        $form = $this->createForm(SecretariatType::class, $modification, array('form' => 'modification_validate', 'data_class' => MemberModification::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $modification = $form->getData();

            $modification->getMemberModificationFirstname() != null ? $member->setMemberFirstname($modification->getMemberModificationFirstname()) : null;
            $modification->getMemberModificationName()      != null ? $member->setMemberName($modification->getMemberModificationName()) : null;
            $modification->getMemberModificationBirthday()  != null ? $member->setMemberBirthday($modification->getMemberModificationBirthday()) : null;
            $modification->getMemberModificationAddress()   != null ? $member->setMemberAddress($modification->getMemberModificationAddress()) : null;
            $modification->getMemberModificationZip()       != null ? $member->setMemberZip($modification->getMemberModificationZip()) : null;
            $modification->getMemberModificationCity()      != null ? $member->setMemberCity($modification->getMemberModificationCity()) : null;
            $modification->getMemberModificationCountry()   != null ? $member->setMemberCountry($modification->getMemberModificationCountry()) : null;
            $modification->getMemberModificationEmail()     != null ? $member->setMemberEmail($modification->getMemberModificationEmail()) : null;
            $modification->getMemberModificationPhone()     != null ? $member->setMemberPhone($modification->getMemberModificationPhone()) : null;

            $member->setMemberModification(null);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat_member_modification_validation_index');
        }

        return $this->render('Secretariat/Member/Modification/validate.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/annulation_modifications_membre/{member<\d+>}", name="member_modification_validation_cancel")
     *
     * @param Request $request
     * @param Member $member
     * @return RedirectResponse|Response
     */
    public function memberModificationValidationCancel(Request $request, Member $member)
    {
        $modification = $member->getMemberModification();

        $form = $this->createForm(SecretariatType::class, $modification, array('form' => 'modification_validate', 'data_class' => MemberModification::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $member->setMemberModification(null);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat_member_modification_validation_index');
        }

        return $this->render('Secretariat/Member/Modification/cancel.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/liste_commission", name="commission_index")
     */
    public function commissionIndex()
    {
        $commissions = $this->getDoctrine()->getRepository(Commission::class)->findAll();

        return $this->render('Secretariat/Commission/index.html.twig', array('commissions' => $commissions));
    }

    /**
     * @Route("/ajouter_commission", name="commission_create")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function commissionCreate(Request $request)
    {
        $form = $this->createForm(SecretariatType::class, new Commission(), array('form' => 'commission_create', 'data_class' => Commission::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $commission = $form->getData();

            switch($commission->getCommissionRole())
            {
                case 1 :
                    $commission->setCommissionRole(null);
                    break;
                case 2 :
                    $commission->setCommissionRole('ROLE_CFG');
                    break;
                case 3 :
                    $commission->setCommissionRole('ROLE_STAGES');
                    break;
                case 4 :
                    $commission->setCommissionRole('ROLE_ADMINISTRATION');
                    break;
                default :
                    $commission->setCommissionRole(null);
            }

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($commission);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat_commission_index');
        }

        return $this->render('Secretariat/Commission/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/detail_commission/{commission<\d+>}", name="commission_detail")
     *
     * @param Commission $commission
     * @return Response
     */
    public function commissionDetail(Commission $commission)
    {
        $members = $this->getDoctrine()->getRepository(CommissionMember::class)->getCommissionMembers($commission->getCommissionId());

        return $this->render('Secretariat/Commission/detail.html.twig', array('members' => $members, 'commission' => $commission));
    }

    /**
     * @Route("/commission/{commission<\d+>}/ajouter_membre", name="commission_member_add")
     *
     * @param Request $request
     * @param Commission $commission
     * @return RedirectResponse|Response
     */
    public function commissionMemberAdd(Request $request, Commission $commission)
    {
        $commission_member = new CommissionMember();

        $form = $this->createForm(SecretariatType::class, $commission_member, array('form' => 'commission_member_add', 'data_class' => CommissionMember::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $today = new DateTime('today');

            $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $form->get('MemberLicence')->getData()]);

            if (is_null($member))
            {
                return $this->redirectToRoute('secretariat_commission_detail', array('commission' => $commission->getCommissionId()));
            }
            elseif (!is_null($this->getDoctrine()->getRepository(CommissionMember::class)->findOneBy(['commission_member' => $form->get('MemberLicence')->getData(), 'commission' => $commission])))
            {
                return $this->redirectToRoute('secretariat_commission_detail', array('commission' => $commission->getCommissionId()));
            }

            $commission_member->setCommission($commission);
            $commission_member->setCommissionMember($member);
            $commission_member->setCommissionMemberDateIn($today);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($commission_member);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat_commission_detail', array('commission' => $commission->getCommissionId()));
        }

        return $this->render('Secretariat/Commission/add_member.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/commission/{commission<\d+>}/supprimer_membre/{member<\d+>}", name="commission_member_delete")
     *
     * @param Request $request
     * @param Commission $commission
     * @param Member $member
     * @return RedirectResponse|Response
     */
    public function commissionMemberDelete(Request $request, Commission $commission, Member $member)
    {
        $commission_member = $this->getDoctrine()->getRepository(CommissionMember::class)->findOneBy(['commission_member' => $member, 'commission' => $commission]);

        $form = $this->createForm(SecretariatType::class, $member, array('form' => 'commission_member_delete', 'data_class' => Member::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $today = new DateTime('today');

            $commission_member->setCommissionMemberDateOut($today);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat_commission_detail', array('commission' => $commission->getCommissionId()));
        }

        return $this->render('Secretariat/Commission/delete_member.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/liste_gestionnaire_club/{club<\d+>}", name="club_manager_index")
     * @param Club $club
     * @param ClubTools $clubTools
     * @return Response
     */
    public function clubManagerIndex(Club $club, ClubTools $clubTools)
    {
        $clubTools->setClub($club);

        return $this->render('Secretariat/Club/Manager/index.html.twig', array('clubTools' => $clubTools));
    }

    /**
     * @Route("/creer_gestionnaire_club/{club<\d+>}", name="club_manager_add")
     * @param SessionInterface $session
     * @param Request $request
     * @param Club $club
     * @param UserTools $userTools
     * @return Response
     */
    public function clubManagerAdd(SessionInterface $session, Request $request, Club $club, UserTools $userTools)
    {
        $session->set('duplicate', false);

        $user = new User();

        $form = $this->createForm(UserType::class, $user, array('form' => 'club_manager_add', 'data_class' => User::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $userTools->clubManagerAdd($form->getData(), $club, $this->getUser(), $form['Password']->getData(), $form->get('UserMember')->getData());

            if ($userTools->isDuplicate())
            {
                $session->set('duplicate', true);

                return $this->render('Secretariat/Club/Manager/add.html.twig', array('form' => $form->createView()));
            }

            return $this->redirectToRoute('secretariat_club_manager_index', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Club/Manager/add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/supprimer_gestionnaire_club/{club<\d+>}/{user<\d+>}", name="club_manager_delete")
     * @param Request $request
     * @param Club $club
     * @param User $user
     * @param UserTools $userTools
     * @return Response
     */
    public function clubManagerDelete(Request $request, Club $club, User $user, UserTools $userTools)
    {
        $form = $this->createForm(UserType::class, $user, array('form' => 'club_manager_delete', 'data_class' => User::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $userTools->clubManagerDelete($user, $club, $this->getUser());

            return $this->redirectToRoute('secretariat_club_manager_index', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Club/Manager/delete.html.twig', array('form' => $form->createView(), 'club' => $club, 'user' => $user));
    }

    /**
     * @Route("/liste_acces_interface", name="access_list_index")
     * @return Response
     */
    public function accessListIndex()
    {
        $access_list = $this->getDoctrine()->getManager()->getRepository(User::class)->getAccessList();

        return $this->render('Secretariat/Interface/index.html.twig', array('access_list' => $access_list));
    }

    /**
     * @Route("/reactivation_acces/{user<\d+>}", name="access_reactivate")
     * @param User $user
     * @param UserTools $userTools
     * @return Response
     */
    public function accessReactivate(User $user, UserTools $userTools)
    {
        $userTools->reactivate($user, $this->getUser());

        return $this->redirectToRoute('secretariat_access_list_index');
    }

    /**
     * @Route("/modification_mot_de_passe_acces/{user<\d+>}", name="access_password_modify")
     * @param SessionInterface $session
     * @param Request $request
     * @param User $user
     * @param UserTools $userTools
     * @return Response
     */
    public function accessPasswordModify(SessionInterface $session, Request $request, User $user, UserTools $userTools)
    {
        $session->set('passwordError', false);

        $form = $this->createForm(UserType::class, $user, array('form' => 'change_password', 'data_class' => User::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($userTools->changePassword($form->getData(), $form['Password1']->getData(), $form['Password2']->getData()))
            {
                return $this->redirectToRoute('secretariat_access_list_index');
            }
            else
            {
                $session->set('passwordError', true);

                return $this->render('Secretariat/Interface/modify_password.html.twig', array('form' => $form->createView()));
            }
        }

        return $this->render('Secretariat/Interface/modify_password.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/imprimer_timbres", name="print_stamp")
     * @param Request $request
     * @return Response
     */
    public function printStamp(Request $request)
    {
        $stamps = null;

        $form = $this->createForm(SecretariatType::class, $stamps, array('form' => 'print_stamp', 'data_class' => null));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $stamps = explode(",", $form->get('MemberList')->getData());

            $members = $this->getDoctrine()->getRepository(Member::class)->findBy(['member_id' => $stamps]);

            return $this->render('Secretariat/stamps.html.twig', array('members' => $members));
        }

        return $this->render('Secretariat/stamp_form.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/imprimer_cartes", name="print_card")
     * @param Request $request
     * @return Response
     */
    public function printCard(Request $request)
    {
        $cards = null;

        $form = $this->createForm(SecretariatType::class, $cards, array('form' => 'print_card', 'data_class' => null));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $cards = explode(",", $form->get('MemberId')->getData());

            $members = $this->getDoctrine()->getRepository(Member::class)->findBy(['member_id' => $cards]);

            return $this->render('Secretariat/cards.html.twig', array('members' => $members));
        }

        return $this->render('Secretariat/cards_form.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/generer_formulaires_renouvellement/{club<\d+>}", name="form_renew_create")
     * @param Request $request
     * @param Club $club
     * @return Response
     */
    public function formRenewCreate(Request $request, Club $club)
    {
        $period = null;

        $form = $this->createForm(SecretariatType::class, $period, array('form' => 'form_renew_create', 'data_class' => null));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $listData = new ListData();

            $output_file = "./licence_out.rtf";

            $fh = fopen($output_file, 'a') or die("can't open file");

            $file = file_get_contents('../private/licence.rtf', true);

            $file = substr($file, 1, strlen($file)-2);

            fwrite($fh, '{');

            $old = array('\{\{TITRE\}\}', '\{\{SEXE\}\}', '\{\{NOM\}\}', '\{\{PRENOM\}\}', '\{\{DOJO_ID\}\}', '\{\{DOJO_NOM\}\}', '\{\{ADRESSE\}\}', '\{\{CODE_POSTALE\}\}', '\{\{LOCALITE\}\}', '\{\{DATE_DE_NAISSANCE\}\}', '\{\{GSM\}\}', '\{\{EMAIL\}\}', '\{\{LICENCE_ID\}\}', '\{\{DATE_ECHEANCE_FR\}\}', '\{\{ENFANT\}\}', '\{\{ADULTE\}\}', '\{\{PAYS\}\}');

            $children_limit = new DateTime('-14 year today');

            $members = $this->getDoctrine()->getRepository(Member::class)->getClubRenewForms($club, $form->get('Start')->getData()->format('Y-m-d'), $form->get('End')->getData()->format('Y-m-d'));

            $i = 0;

            foreach ($members as $member)
            {
                if ($i != 0)
                {
                    fwrite($fh, '{\page}');
                }

                $newphrase = '';

                unset($new);

                if ($member['Sex'] == 1)
                {
                    $title='Monsieur';
                    $sex='Masculin';
                }
                else
                {
                    $title='Madamme';
                    $sex="Féminin";
                }

                if ($member['Birthday'] > $children_limit)
                {
                    $children='X';
                    $adult='';
                }
                else
                {
                    $children='';
                    $adult='X';
                }

                $new = array($title, utf8_decode($sex), utf8_decode($member['Name']), utf8_decode($member['FirstName']), utf8_decode($club->getClubId()), utf8_decode($club->getClubName()), utf8_decode($member['Address']), utf8_decode($member['Zip']), utf8_decode($member['City']), utf8_decode($member['Birthday']->format('d/m/Y')), utf8_decode($member['Phone']), utf8_decode($member['Email']), utf8_decode($member['Id']), utf8_decode($member['Deadline']->format('d/m/Y')), $children, $adult, utf8_decode($listData->getCountryName($member['Country'])));

                $newphrase .= str_replace($old, $new, $file);

                fwrite($fh, $newphrase);

                $i++;
            }

            fwrite($fh, '}');
            fclose($fh);

            $response = new BinaryFileResponse($output_file);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);

            return $response->deleteFileAfterSend();
        }

        return $this->render('Secretariat/renew_form.html.twig', array('form' => $form->createView()));
    }
}
