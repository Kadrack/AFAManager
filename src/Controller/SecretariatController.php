<?php
// src/Controller/SecretariatController.php
namespace App\Controller;

use App\Entity\Club;
use App\Entity\ClubDojo;
use App\Entity\ClubHistory;
use App\Entity\ClubLesson;
use App\Entity\ClubModificationLog;
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
 * Class SecretariatController
 * @package App\Controller
 *
 * @IsGranted("ROLE_SECRETARIAT")
 */
#[Route('/secretariat', name:'secretariat-')]
class SecretariatController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('/', name:'index')]
    public function index(): Response
    {
        return $this->render('Secretariat/index.html.twig');
    }

    /**
     * @return Response
     */
    #[Route('/liste-sympathisant', name:'supporterIndex')]
    public function supporterIndex(): Response
    {
        $supporters = $this->getDoctrine()->getRepository(SecretariatSupporter::class)->findAll();

        return $this->render('Secretariat/Supporter/index.html.twig', array('supporters' => $supporters));
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route('/ajouter-sympathisant', name:'supporterCreate')]
    public function supporterAdd(Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(SecretariatType::class, new SecretariatSupporter(), array('form' => 'supporterCreate', 'data_class' => SecretariatSupporter::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $address = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($address);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat-supporterIndex');
        }

        return $this->render('Secretariat/Supporter/add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param SecretariatSupporter $supporter
     * @return RedirectResponse|Response
     */
    #[Route('/modifier-sympathisant/{supporter<\d+>}', name:'supporterUpdate')]
    public function supporterUpdate(Request $request, SecretariatSupporter $supporter): RedirectResponse|Response
    {
        $form = $this->createForm(SecretariatType::class, $supporter, array('form' => 'supporterUpdate', 'data_class' => SecretariatSupporter::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat-supporterIndex');
        }

        return $this->render('Secretariat/Supporter/update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param SecretariatSupporter $supporter
     * @return RedirectResponse|Response
     */
    #[Route('/supprimer-sympathisant/{supporter<\d+>}', name:'supporterDelete')]
    public function supporterDelete(Request $request, SecretariatSupporter $supporter): RedirectResponse|Response
    {
        $form = $this->createForm(SecretariatType::class, $supporter, array('form' => 'supporterDelete', 'data_class' => SecretariatSupporter::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($supporter);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat-supporterIndex');
        }

        return $this->render('Secretariat/Supporter/delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @return Response
     */
    #[Route('/liste-clubs', name:'clubList')]
    public function clubList(): Response
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
     * @return Response
     */
    #[Route('/liste-adresse-clubs', name:'clubAddressList')]
    public function clubAddressList(): Response
    {
        $provinces = new ListData();

        $active_clubs = array();

        $active_list = $this->getDoctrine()->getRepository(Club::class)->getActiveClubsInformations();

        foreach ($active_list as $club)
        {
            $club['Province'] = $provinces->getProvince($club['Province']);

            $active_clubs[$club['Province']]['Clubs'][$club['Id']] = $club;

            if (!isset($active_clubs[$club['Province']]['name']))
            {
                $active_clubs[$club['Province']]['province'] = $club['Province'];
            }
        }

        return $this->render('Secretariat/Club/address_list.html.twig', array('active_clubs' => $active_clubs));
    }

    /**
     * @param int|null $list
     * @return Response
     */
    #[Route('/liste-mails-clubs/{list<\d+>}', name:'clubMailsList', defaults: ['list' => null])]
    public function clubMailsList(?int $list): Response
    {
        if (is_null($list))
        {
            return $this->render('Secretariat/Club/mails_list.html.twig');
        }

        if ($list == 3)
        {
            $mailing_list = array_merge($this->getDoctrine()->getRepository(Club::class)->getClubsMailsList(1), $this->getDoctrine()->getRepository(Club::class)->getClubsMailsList(2));

        }
        else
        {
            $mailing_list = $this->getDoctrine()->getRepository(Club::class)->getClubsMailsList($list);
        }

        $list = array();

        foreach ($mailing_list as $mail)
        {
            $list[] = $mail['Mail'];
        }

        file_put_contents('./mails.csv', array_unique($list));

        $response = new BinaryFileResponse('./mails.csv');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response->deleteFileAfterSend();
    }

    /**
     * @param Club $club
     * @param ClubTools $clubTools
     * @return Response
     */
    #[Route('/index-dojo/{club<\d+>}', name:'dojoIndex')]
    public function dojoIndex(Club $club, ClubTools $clubTools): Response
    {
        $clubTools->setClub($club);

        return $this->render('Secretariat/Dojo/index.html.twig', array('clubTools' => $clubTools));
    }

    /**
     * @param Request $request
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    #[Route('/ajouter-dojo/{club<\d+>}', name:'dojoAdd')]
    public function dojoAdd(Request $request, Club $club, ClubTools $clubTools): RedirectResponse|Response
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, new ClubDojo(), array('form' => 'dojoCreate', 'data_class' => ClubDojo::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoAddress($form->getData(), 'Add');

            return $this->redirectToRoute('secretariat-dojoIndex', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/address_add.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @param Request $request
     * @param ClubDojo $clubDojo
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    #[Route('/modifier-dojo/{clubDojo<\d+>}/{club<\d+>}', name:'dojoUpdate')]
    public function dojoUpdate(Request $request, ClubDojo $clubDojo, Club $club, ClubTools $clubTools): RedirectResponse|Response
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, $clubDojo, array('form' => 'dojoUpdate', 'data_class' => ClubDojo::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoAddress($form->getData());

            return $this->redirectToRoute('secretariat-dojoIndex', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/address_update.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @param Request $request
     * @param ClubDojo $clubDojo
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    #[Route('/supprimer-dojo/{clubDojo<\d+>}/{club<\d+>}', name:'dojoDelete')]
    public function dojoDelete(Request $request, ClubDojo $clubDojo, Club $club, ClubTools $clubTools): RedirectResponse|Response
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, $clubDojo, array('form' => 'dojoDelete', 'data_class' => ClubDojo::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoAddress($form->getData(), 'Delete');

            return $this->redirectToRoute('secretariat-dojoIndex', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/address_delete.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @param Request $request
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    #[Route('/ajouter-horaire/{club<\d+>}', name:'lessonAdd')]
    public function lessonAdd(Request $request, Club $club, ClubTools $clubTools): RedirectResponse|Response
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, new ClubLesson(), array('form' => 'trainingCreate', 'data_class' => ClubLesson::class, 'choices' => $club->getClubDojos()));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoTraining($form->getData(), 'Add');

            return $this->redirectToRoute('secretariat-dojoIndex', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/training_add.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @param Request $request
     * @param ClubLesson $clubLesson
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    #[Route('/modifier-horaire/{clubLesson<\d+>}/{club<\d+>}', name:'lessonUpdate')]
    public function lessonUpdate(Request $request, ClubLesson $clubLesson, Club $club, ClubTools $clubTools): RedirectResponse|Response
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, $clubLesson, array('form' => 'trainingUpdate', 'data_class' => ClubLesson::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoTraining($form->getData());

            return $this->redirectToRoute('secretariat-dojoIndex', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/training_update.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @param Request $request
     * @param ClubLesson $clubLesson
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    #[Route('/supprimer-horaire/{clubLesson<\d+>}/{club<\d+>}', name:'lessonDelete')]
    public function lessonDelete(Request $request, ClubLesson $clubLesson, Club $club, ClubTools $clubTools): RedirectResponse|Response
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, $clubLesson, array('form' => 'trainingDelete', 'data_class' => ClubLesson::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoTraining($form->getData(), 'Delete');

            return $this->redirectToRoute('secretariat-dojoIndex', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/training_delete.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @param Request $request
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    #[Route('/ajouter-professeur-afa/{club<\d+>}', name:'teacherAFAAdd')]
    public function teacherAFAAdd(Request $request, Club $club, ClubTools $clubTools): RedirectResponse|Response
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, new ClubTeacher(), array('form' => 'teacherAFACreate', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoTeacher($form->getData(), 'Add', $form->get('ClubTeacherMember')->getData());

            return $this->redirectToRoute('secretariat-dojoIndex', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/teacher_afa_add.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @param Request $request
     * @param ClubTeacher $teacher
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    #[Route('/modifier-professeur-afa/{teacher<\d+>}/{club<\d+>}', name:'teacherAFAUpdate')]
    public function teacherAFAUpdate(Request $request, ClubTeacher $teacher, Club $club, ClubTools $clubTools): RedirectResponse|Response
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacherAFAUpdate', 'data_class' => ClubTeacher::class));

        $form->get('ClubTeacherMember')->setData($teacher->getClubTeacherMember()->getMemberId());
        $form->get('ClubTeacherFirstname')->setData($teacher->getClubTeacherMember()->getMemberFirstname());
        $form->get('ClubTeacherName')->setData($teacher->getClubTeacherMember()->getMemberName());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoTeacher($form->getData());

            return $this->redirectToRoute('secretariat-dojoIndex', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/teacher_afa_update.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @param Request $request
     * @param ClubTeacher $teacher
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    #[Route('/supprimer-professeur-afa/{teacher<\d+>}/{club<\d+>}', name:'teacherAFADelete')]
    public function teacherAFADelete(Request $request, ClubTeacher $teacher, Club $club, ClubTools $clubTools): RedirectResponse|Response
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacherAFADelete', 'data_class' => ClubTeacher::class));

        $form->get('ClubTeacherMember')->setData($teacher->getClubTeacherMember()->getMemberId());
        $form->get('ClubTeacherFirstname')->setData($teacher->getClubTeacherMember()->getMemberFirstname());
        $form->get('ClubTeacherName')->setData($teacher->getClubTeacherMember()->getMemberName());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoTeacher($form->getData(), 'Delete');

            return $this->redirectToRoute('secretariat-dojoIndex', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/teacher_afa_delete.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @param Request $request
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    #[Route('/ajouter-professeur-etranger/{club<\d+>}', name:'teacherForeignAdd')]
    public function teacherForeignAdd(Request $request, Club $club, ClubTools $clubTools): RedirectResponse|Response
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, new ClubTeacher(), array('form' => 'teacherForeignCreate', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoTeacher($form->getData(), 'Add', $form->get('ClubTeacherMember')->getData());

            return $this->redirectToRoute('secretariat-dojoIndex', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/teacher_foreign_add.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @param Request $request
     * @param ClubTeacher $teacher
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    #[Route('/modifier-professeur-etranger/{teacher<\d+>}/{club<\d+>}', name:'teacherForeignUpdate')]
    public function teacherForeignUpdate(Request $request, ClubTeacher $teacher, Club $club, ClubTools $clubTools): RedirectResponse|Response
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacherForeignUpdate', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoTeacher($form->getData());

            return $this->redirectToRoute('secretariat-dojoIndex', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/teacher_foreign_update.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @param Request $request
     * @param ClubTeacher $teacher
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    #[Route('/supprimer-professeur-etranger/{teacher<\d+>}/{club<\d+>}', name:'teacherForeignDelete')]
    public function teacherForeignDelete(Request $request, ClubTeacher $teacher, Club $club, ClubTools $clubTools): RedirectResponse|Response
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacherForeignDelete', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->dojoTeacher($form->getData(), 'Delete');

            return $this->redirectToRoute('secretariat-dojoIndex', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Dojo/teacher_foreign_delete.html.twig', array('form' => $form->createView(), 'clubTools' => $clubTools->getClub()));
    }

    /**
     * @param Request $request
     * @param Club $club
     * @param ClubTools $clubTools
     * @return RedirectResponse|Response
     */
    #[Route('/detail-association/{club<\d+>}', name:'associationDetails')]
    public function associationDetails(Request $request, Club $club, ClubTools $clubTools): RedirectResponse|Response
    {
        $clubTools->setClub($club);

        $form = $this->createForm(ClubType::class, $club, array('form' => 'detailAssociation'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $clubTools->associationDetails($form->getData());

            return $this->redirectToRoute('secretariat-clubList');
        }

        return $this->render('Secretariat/Club/Association/details.html.twig', array('form' => $form->createView(), 'club' => $club));
    }

    /**
     * @param SessionInterface $session
     * @param Club $club
     * @return Response
     */
    #[Route('/liste-membres/{club<\d+>}', name:'membersActive')]
    public function membersActive(SessionInterface $session, Club $club): Response
    {
        $members = $this->getDoctrine()->getRepository(Member::class)->getClubActiveMembers($club);

        $limit = new DateTime('+3 month today');

        $session->set('origin', 'active');

        return $this->render('Secretariat/Club/Member/list_active.html.twig', array('members' => $members, 'club' => $club, 'limit' => $limit));
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/rechercher-membres', name:'searchMembers')]
    public function searchMembers(Request $request): Response
    {
        $search = null; $results = null;

        $form = $this->createForm(SecretariatType::class, $search, array('form' => 'searchMembers', 'data_class' => null));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $results = $this->getDoctrine()->getRepository(Member::class)->getFullSearchMembers($form->get('Search')->getData());

            return $this->render('Secretariat/Member/search.html.twig', array('form' => $form->createView(), 'results' => $results));
        }

        return $this->render('Secretariat/Member/search.html.twig', array('form' => $form->createView(), 'results' => $results));
    }

    /**
     * @param Club $club
     * @return Response
     */
    #[Route('/liste-anciens-membres/{club<\d+>}', name:'membersAncient')]
    public function membersAncient(Club $club): Response
    {
        $members = $this->getDoctrine()->getRepository(Member::class)->getClubInactiveMembers($club);

        $limit = new DateTime('+3 month today');

        return $this->render('Secretariat/Club/Member/list_ancient.html.twig', array('members' => $members == null ? null : $members, 'club' => $club, 'limit' => $limit));
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route('/creer-club', name:'clubCreate')]
    public function clubCreate(Request $request): RedirectResponse|Response
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

            return $this->redirectToRoute('secretariat-clubList');
        }

        return $this->render('Secretariat/Club/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param Club $club
     * @return RedirectResponse|Response
     */
    #[Route('/desaffilier-club/{club<\d+>}', name:'clubDisaffiliate')]
    public function clubDisaffiliate(Request $request, Club $club): RedirectResponse|Response
    {
        $history = new ClubHistory();

        $form = $this->createForm(ClubType::class, $history, array('form' => 'historyEntry', 'data_class' => ClubHistory::class));

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

            return $this->redirectToRoute('secretariat-clubList');
        }

        return $this->render('Secretariat/Club/disaffiliate.html.twig', array('form' => $form->createView(), 'club' => $club));
    }

    /**
     * @param Request $request
     * @param Club $club
     * @return RedirectResponse|Response
     */
    #[Route('/reaffilier-club/{club<\d+>}', name:'clubReassign')]
    public function clubReassign(Request $request, Club $club): RedirectResponse|Response
    {
        $history = new ClubHistory();

        $form = $this->createForm(ClubType::class, $history, array('form' => 'historyEntry', 'data_class' => ClubHistory::class));

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

            return $this->redirectToRoute('secretariat-clubList');
        }

        return $this->render('Secretariat/Club/reassign.html.twig', array('form' => $form->createView(), 'club' => $club));
    }

    /**
     * @param Request $request
     * @param PhotoUploader $photoUploader
     * @param Club $club
     * @return RedirectResponse|Response
     * @throws Exception
     */
    #[Route('/creer-membre/club/{club<\d+>}', name:'memberCreate')]
    public function memberCreate(Request $request, PhotoUploader $photoUploader, Club $club): RedirectResponse|Response
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

            $member->setMemberActualClub($club);
            $member->setMemberLastLicence($licence);
            $member->setMemberStartPractice($form->get('MemberLicenceMedicalCertificate')->getData());
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

            return $this->redirectToRoute('secretariat-membersActive', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Club/Member/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Club $club
     * @param Member $member
     * @return Response
     */
    #[Route('/detail-licence/{member<\d+>}/club/{club<\d+>}', name:'memberLicenceDetail')]
    public function memberLicenceDetail(Club $club, Member $member): Response
    {
        $licence_history = $this->getDoctrine()->getRepository(MemberLicence::class)->findBy(['member_licence' => $member->getMemberId()], ['member_licence_id' => 'DESC']);

        $next_renew = $licence_history[0]->getMemberLicenceDeadline() < new DateTime('+3 month today');

        return $this->render('Secretariat/Club/Member/licence_detail.html.twig', array('member' => $member, 'club' => $club, 'licence_history' => $licence_history, 'next_renew' => $next_renew));
    }

    /**
     * @param Request $request
     * @param PhotoUploader $photoUploader
     * @param Club $club
     * @param Member $member
     * @return RedirectResponse|Response
     */
    #[Route('/detail-personnel/{member<\d+>}/club/{club<\d+>}', name:'memberPersonalDetail')]
    public function memberPersonalDetail(Request $request, PhotoUploader $photoUploader, Club $club, Member $member): RedirectResponse|Response
    {
        $form = $this->createForm(SecretariatType::class, $member, array('form' => 'memberUpdate', 'data_class' => Member::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($form['MemberPhoto']->getData() != null)
            {
                $member->setMemberPhoto($photoUploader->upload($form['MemberPhoto']->getData(), $member->getMemberPhoto()));
            }

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat-membersActive', array('club' => $club->getClubId(), 'member' => $member->getMemberId()));
        }

        return $this->render('Secretariat/Club/Member/personal_detail.html.twig', array('form' => $form->createView(), 'member' => $member));
    }

    /**
     * @param Club $club
     * @param Member $member
     * @return BinaryFileResponse
     */
    #[Route('/formulaire-renouvellement/{member<\d+>}/club/{club<\d+>}', name:'memberFormRenew')]
    public function memberFormRenew(Club $club, Member $member): BinaryFileResponse
    {
        $listData = new ListData();

        $output_file = "./licence_out.rtf";

        $fh = fopen($output_file, 'a') or die('can\'t open file');

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
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response->deleteFileAfterSend();
    }

    /**
     * @param SessionInterface $session
     * @param Request $request
     * @param Club $club
     * @param Member $member
     * @return RedirectResponse|Response
     * @throws Exception
     */
    #[Route('/renouvellement-licence/{member<\d+>}/club/{club<\d+>}', name:'memberLicenceRenew')]
    public function memberLicenceRenew(SessionInterface $session, Request $request, Club $club, Member $member): RedirectResponse|Response
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
            $form = $this->createForm(MemberType::class, $licence_new, array('form' => 'licenceRenewKyu', 'data_class' => MemberLicence::class));

            $form->get('GradeKyuRank')->setData($licence_old->getMemberLicenceGrade() == null ? null : $licence_old->getMemberLicenceGrade()->getGradeRank());
        }
        else
        {
            $form = $this->createForm(MemberType::class, $licence_new, array('form' => 'licenceRenew', 'data_class' => MemberLicence::class));
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

            if ($kyu && ($member->getMemberLastGrade()->getGradeRank() < $form->get('GradeKyuRank')->getData()))
            {
                $grade = new Grade();
    
                $grade->setGradeDate($licence_new->getMemberLicenceUpdate());
                $grade->setGradeRank($form->get('GradeKyuRank')->getData());
                $grade->setGradeMember($member);
                $grade->setGradeStatus(4);
                $grade->setGradeClub($club);
    
                $member->setMemberLastGrade($grade);
    
                $licence_new->setMemberLicenceGrade($grade);
    
                $entityManager->persist($grade);
            }

            $member->setMemberLastLicence($licence_new);
            $member->setMemberActualClub($licence_new->getMemberLicenceClub());

            $entityManager->persist($licence_new);
            $entityManager->persist($stamp);
            $entityManager->flush();

            if ($session->get('origin') == 'active')
            {
                return $this->redirectToRoute('secretariat-membersActive', array('club' => $club->getClubId(), 'member' => $member->getMemberId()));
            }
            else
            {
                return $this->redirectToRoute('secretariat-membersAncient', array('club' => $club->getClubId()));
            }
        }

        return $this->render('Secretariat/Club/Member/licence_renew.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param SessionInterface $session
     * @param Request $request
     * @param Club $club
     * @param Member $member
     * @param MemberLicence $renew
     * @return RedirectResponse|Response
     */
    #[Route('/modification-renouvellement/{renew<\d+>}/licence/{member<\d+>}/club/{club<\d+>}', name:'memberLicenceRenewUpdate')]
    public function memberLicenceRenewUpdate(SessionInterface $session, Request $request, Club $club, Member $member, MemberLicence $renew): RedirectResponse|Response
    {
        if ($renew->getMemberLicenceGrade() == null)
        {
            $grade = new Grade();
    
            $grade->setGradeClub($club);
            $grade->setGradeMember($member);
            $grade->setGradeStatus(4);
            $grade->setGradeRank(1);
        }
        else
        {
            $grade = $renew->getMemberLicenceGrade();
        }

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
            $form = $this->createForm(MemberType::class, $renew, array('form' => 'licenceRenewKyu', 'data_class' => MemberLicence::class));

            $form->get('GradeKyuRank')->setData($member->getMemberLastGrade() == null ? null : $member->getMemberLastGrade()->getGradeRank());
        }
        else
        {
            $form = $this->createForm(MemberType::class, $renew, array('form' => 'licenceRenew', 'data_class' => MemberLicence::class));
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
                        $grade->setGradeDate(new DateTime('today'));

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
                return $this->redirectToRoute('secretariat-membersActive', array('club' => $club->getClubId()));
            }
            else
            {
                return $this->redirectToRoute('secretariat-membersAncient', array('club' => $club->getClubId()));
            }
        }

        return $this->render('Secretariat/Club/Member/licence_renew.html.twig', array('form' => $form->createView()));
    }

    /**
     * @return Response
     */
    #[Route('/sessions-examen', name:'examIndex')]
    public function examIndex(): Response
    {
        $sessions = $this->getDoctrine()->getRepository(GradeSession::class)->findBy(['grade_session_type' => 1], ['grade_session_date' => 'DESC', 'grade_session_type' => 'DESC']);

        return $this->render('Secretariat/Exam/index.html.twig', array('sessions' => $sessions));
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route('/session-examen/creer', name:'examCreate')]
    public function examCreate(Request $request): RedirectResponse|Response
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

            return $this->redirectToRoute('secretariat-examIndex');
        }

        return $this->render('Secretariat/Exam/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param GradeSession $session
     * @return RedirectResponse|Response
     */
    #[Route('/session-examen/{session<\d+>}/modifier', name:'examUpdate')]
    public function examUpdate(Request $request, GradeSession $session): RedirectResponse|Response
    {
        $form = $this->createForm(GradeType::class, $session, array('form' => 'examUpdate'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat-examIndex', array('session' => $session->getGradeSessionId()));
        }

        return $this->render('Secretariat/Exam/update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @return Response
     */
    #[Route('/liste-stages', name:'trainingIndex')]
    public function trainingIndex(): Response
    {
        $trainings = $this->getDoctrine()->getRepository(Training::class)->getActiveTrainings(4);

        return $this->render('Secretariat/Training/index.html.twig', array('trainings' => count($trainings) == 0 ? null : $trainings));
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route('/stage-creer', name:'trainingCreate')]
    public function trainingCreate(Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(TrainingType::class, new Training());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $training = $form->getData();

            $session = $training->getSession();

            $duration = date_diff($session->getTrainingSessionEndingHour(), $session->getTrainingSessionStartingHour());

            $session->setTrainingSessionDuration($duration->format('%h')*60 . $duration->format('%i'));

            $training->addTrainingSessions($session);
            $training->setTrainingFirstSession($session);
            $training->setTrainingTotalSessions(1);
            $training->setTrainingType(4);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($training);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat-trainingIndex');
        }

        return $this->render('Secretariat/Training/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Training $training
     * @return Response
     */
    #[Route('/stage/{training<\d+>}/detail', name:'trainingDetail')]
    public function trainingDetail(Training $training): Response
    {
        $sessions = $this->getDoctrine()->getRepository(TrainingSession::class)->getTrainingSessions($training->getTrainingId());

        return $this->render('Secretariat/Training/detail.html.twig', array('training' => $training, 'sessions' => $sessions));
    }

    /**
     * @param Request $request
     * @param Training $training
     * @return RedirectResponse|Response
     */
    #[Route('/stage/{training<\d+>}/modifier', name:'trainingUpdate')]
    public function trainingUpdate(Request $request, Training $training): RedirectResponse|Response
    {
        $form = $this->createForm(TrainingType::class, $training, array('form' => 'trainingUpdate'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat-trainingIndex');
        }

        return $this->render('Secretariat/Training/update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param Training $training
     * @return RedirectResponse|Response
     */
    #[Route('/stage/{training<\d+>}/supprimer', name:'trainingDelete')]
    public function traningDelete(Request $request, Training $training): RedirectResponse|Response
    {
        $form = $this->createForm(TrainingType::class, $training, array('form' => 'trainingDelete'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($training);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat-trainingIndex');
        }

        return $this->render('Secretariat/Training/update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param Training $training
     * @return RedirectResponse|Response
     */
    #[Route('/stage/{training<\d+>}/ajouter-session', name:'trainingSessionAdd')]
    public function trainingSessionAdd(Request $request, Training $training): RedirectResponse|Response
    {
        $form = $this->createForm(TrainingType::class, new TrainingSession(), array('form' => 'sessionAdd', 'data_class' => TrainingSession::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $session = $form->getData();

            $duration = date_diff($session->getTrainingSessionEndingHour(), $session->getTrainingSessionStartingHour());

            $session->setTrainingSessionDuration($duration->format('%h')*60 . $duration->format('%i'));
            $session->setTraining($training);

            $training->setTrainingTotalSessions($training->getTrainingTotalSessions() + 1);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat-trainingDetail', array('training' => $training->getTrainingId()));
        }

        return $this->render('Secretariat/Training/session_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param Training $training
     * @param TrainingSession $session
     * @return RedirectResponse|Response
     */
    #[Route('/stage/{training<\d+>}/modifier-session/{session<\d+>}', name:'trainingSessionUpdate')]
    public function trainingSessionUpdate(Request $request, Training $training, TrainingSession $session): RedirectResponse|Response
    {
        $form = $this->createForm(TrainingType::class, $session, array('form' => 'sessionAdd', 'data_class' => TrainingSession::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $session = $form->getData();

            $duration = date_diff($session->getTrainingSessionEndingHour(), $session->getTrainingSessionStartingHour());

            $session->setTrainingSessionDuration($duration->format('%h')*60 . $duration->format('%i'));

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat-trainingDetail', array('training' => $training->getTrainingId()));
        }

        return $this->render('Secretariat/Training/session_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param Training $training
     * @param TrainingSession $session
     * @return RedirectResponse|Response
     */
    #[Route('/stage/{training<\d+>}/supprimer-session/{session<\d+>}', name:'trainingSessionDelete')]
    public function trainingSessionDelete(Request $request, Training $training, TrainingSession $session): RedirectResponse|Response
    {
        $form = $this->createForm(TrainingType::class, $session, array('form' => 'sessionDelete', 'data_class' => TrainingSession::class));

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
                return $this->redirectToRoute('secretariat-trainingIndex');
            }
            else
            {
                return $this->redirectToRoute('secretariat-trainingDetail', array('training' => $training->getTrainingId()));
            }

        }

        return $this->render('Secretariat/Training/session_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @return Response
     */
    #[Route('/liste-validations-modifications-membres', name:'memberModificationValidationIndex')]
    public function memberModificationValidationIndex(): Response
    {
        $modifications = $this->getDoctrine()->getRepository(Member::class)->getMemberModification();

        return $this->render('Secretariat/Member/Modification/index.html.twig', array('modifications' => $modifications));
    }

    /**
     * @param Request $request
     * @param Member $member
     * @param PhotoUploader $photoUploader
     * @return RedirectResponse|Response
     */
    #[Route('/validation-modifications-membre/{member<\d+>}', name:'memberModificationValidationValidate')]
    public function memberModificationValidationValidate(Request $request, Member $member, PhotoUploader $photoUploader): RedirectResponse|Response
    {
        $modification = $member->getMemberModification();

        $form = $this->createForm(SecretariatType::class, $modification, array('form' => 'modificationValidate', 'data_class' => MemberModification::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $modification = $form->getData();

            $photoUploader->delete($member->getMemberPhoto());

            $modification->getMemberModificationPhoto()     != null ? $member->setMemberPhoto($modification->getMemberModificationPhoto()) : null;
            $modification->getMemberModificationFirstname() != null ? $member->setMemberFirstname($modification->getMemberModificationFirstname()) : null;
            $modification->getMemberModificationName()      != null ? $member->setMemberName($modification->getMemberModificationName()) : null;
            $modification->getMemberModificationBirthday()  != null ? $member->setMemberBirthday($modification->getMemberModificationBirthday()) : null;
            $modification->getMemberModificationAddress()   != null ? $member->setMemberAddress($modification->getMemberModificationAddress()) : null;
            $modification->getMemberModificationZip()       != null ? $member->setMemberZip($modification->getMemberModificationZip()) : null;
            $modification->getMemberModificationCity()      != null ? $member->setMemberCity($modification->getMemberModificationCity()) : null;
            $modification->getMemberModificationCountry()   != null ? $member->setMemberCountry($modification->getMemberModificationCountry()) : null;
            $modification->getMemberModificationEmail()     != null ? $member->setMemberEmail($modification->getMemberModificationEmail()) : null;
            $modification->getMemberModificationPhone()     != null ? $member->setMemberPhone($modification->getMemberModificationPhone()) : null;
            $modification->getMemberModificationAikikaiId() != null ? $member->setMemberAikikaiId($modification->getMemberModificationAikikaiId()) : null;

            $member->setMemberModification(null);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat-memberModificationValidationIndex');
        }

        return $this->render('Secretariat/Member/Modification/validate.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param Member $member
     * @param PhotoUploader $photoUploader
     * @return RedirectResponse|Response
     */
    #[Route('/annulation-modifications-membre/{member<\d+>}', name:'memberModificationValidationCancel')]
    public function memberModificationValidationCancel(Request $request, Member $member, PhotoUploader $photoUploader): RedirectResponse|Response
    {
        $modification = $member->getMemberModification();

        $form = $this->createForm(SecretariatType::class, $modification, array('form' => 'modificationValidate', 'data_class' => MemberModification::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $photoUploader->delete($modification->getMemberModificationPhoto());

            $member->setMemberModification(null);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat-memberModificationValidationIndex');
        }

        return $this->render('Secretariat/Member/Modification/cancel.html.twig', array('form' => $form->createView()));
    }

    /**
     * @return Response
     */
    #[Route('/liste-commission', name:'commissionIndex')]
    public function commissionIndex(): Response
    {
        $commissions = $this->getDoctrine()->getRepository(Commission::class)->findAll();

        return $this->render('Secretariat/Commission/index.html.twig', array('commissions' => $commissions));
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route('/ajouter-commission', name:'commissionCreate')]
    public function commissionCreate(Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(SecretariatType::class, new Commission(), array('form' => 'commissionCreate', 'data_class' => Commission::class));

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
                    $commission->setCommissionRole('ROLE_CT');
                    break;
                case 3 :
                    $commission->setCommissionRole('ROLE_STAGES');
                    break;
                case 4 :
                    $commission->setCommissionRole('ROLE_CA');
                    break;
                case 5 :
                    $commission->setCommissionRole('ROLE_CP');
                    break;
                case 6 :
                    $commission->setCommissionRole('ROLE_BANK');
                    break;
                default :
                    $commission->setCommissionRole(null);
            }

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($commission);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat-commissionIndex');
        }

        return $this->render('Secretariat/Commission/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Commission $commission
     * @return Response
     */
    #[Route('/detail-commission/{commission<\d+>}', name:'commissionDetail')]
    public function commissionDetail(Commission $commission): Response
    {
        $members = $this->getDoctrine()->getRepository(CommissionMember::class)->getCommissionMembers($commission->getCommissionId());

        return $this->render('Secretariat/Commission/detail.html.twig', array('members' => $members, 'commission' => $commission));
    }

    /**
     * @param Request $request
     * @param Commission $commission
     * @return RedirectResponse|Response
     */
    #[Route('/commission/{commission<\d+>}/ajouter-membre', name:'commissionMemberAdd')]
    public function commissionMemberAdd(Request $request, Commission $commission): RedirectResponse|Response
    {
        $commission_member = new CommissionMember();

        $form = $this->createForm(SecretariatType::class, $commission_member, array('form' => 'commissionMemberAdd', 'data_class' => CommissionMember::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $today = new DateTime('today');

            $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $form->get('MemberLicence')->getData()]);

            if (is_null($member))
            {
                return $this->redirectToRoute('secretariat-commissionDetail', array('commission' => $commission->getCommissionId()));
            }
            elseif (!is_null($this->getDoctrine()->getRepository(CommissionMember::class)->findOneBy(['commission_member' => $form->get('MemberLicence')->getData(), 'commission' => $commission])))
            {
                return $this->redirectToRoute('secretariat-commissionDetail', array('commission' => $commission->getCommissionId()));
            }

            $commission_member->setCommission($commission);
            $commission_member->setCommissionMember($member);
            $commission_member->setCommissionMemberDateIn($today);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($commission_member);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat-commissionDetail', array('commission' => $commission->getCommissionId()));
        }

        return $this->render('Secretariat/Commission/add_member.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param Commission $commission
     * @param Member $member
     * @return RedirectResponse|Response
     */
    #[Route('/commission/{commission<\d+>}/supprimer-membre/{member<\d+>}', name:'commissionMemberDelete')]
    public function commissionMemberDelete(Request $request, Commission $commission, Member $member): RedirectResponse|Response
    {
        $commission_member = $this->getDoctrine()->getRepository(CommissionMember::class)->findOneBy(['commission_member' => $member, 'commission' => $commission]);

        $form = $this->createForm(SecretariatType::class, $member, array('form' => 'commissionMemberDelete', 'data_class' => Member::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $today = new DateTime('today');

            $commission_member->setCommissionMemberDateOut($today);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat-commissionDetail', array('commission' => $commission->getCommissionId()));
        }

        return $this->render('Secretariat/Commission/delete_member.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Club $club
     * @param ClubTools $clubTools
     * @return Response
     */
    #[Route('/liste-gestionnaire-club/{club<\d+>}', name:'clubManagerIndex')]
    public function clubManagerIndex(Club $club, ClubTools $clubTools): Response
    {
        $clubTools->setClub($club);

        return $this->render('Secretariat/Club/Manager/index.html.twig', array('clubTools' => $clubTools));
    }

    /**
     * @param SessionInterface $session
     * @param Request $request
     * @param Club $club
     * @param UserTools $userTools
     * @return RedirectResponse|Response
     */
    #[Route('/creer-gestionnaire-club/{club<\d+>}', name:'clubManagerAdd')]
    public function clubManagerAdd(SessionInterface $session, Request $request, Club $club, UserTools $userTools): RedirectResponse|Response
    {
        $session->set('duplicate', false);

        $form = $this->createForm(UserType::class, null, array('form' => 'clubManagerAdd', 'data_class' => null));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $isCreated = $userTools->clubManagerAdd($form->get('Login')->getData(), $club, $this->getUser());

            if (!$isCreated)
            {
                $session->set('duplicate', true);

                return $this->render('Secretariat/Club/Manager/add.html.twig', array('form' => $form->createView()));
            }

            return $this->redirectToRoute('secretariat-clubManagerIndex', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Club/Manager/add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @param Club $club
     * @param User $user
     * @param UserTools $userTools
     * @return RedirectResponse|Response
     */
    #[Route('/supprimer-gestionnaire-club/{club<\d+>}/{user<\d+>}', name:'clubManagerDelete')]
    public function clubManagerDelete(Request $request, Club $club, User $user, UserTools $userTools): RedirectResponse|Response
    {
        $form = $this->createForm(UserType::class, $user, array('form' => 'clubManagerDelete', 'data_class' => User::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $userTools->clubManagerDelete($user, $club, $this->getUser());

            return $this->redirectToRoute('secretariat-clubManagerIndex', array('club' => $club->getClubId()));
        }

        return $this->render('Secretariat/Club/Manager/delete.html.twig', array('form' => $form->createView(), 'club' => $club, 'user' => $user));
    }

    /**
     * @return Response
     */
    #[Route('/liste-acces-interface', name:'accessListIndex')]
    public function accessListIndex(): Response
    {
        $clubManagerList = $this->getDoctrine()->getManager()->getRepository(User::class)->getClubManagerList();

        $secretariatAccessList = $this->getDoctrine()->getManager()->getRepository(User::class)->getSecretariatAccessList();

        $lockedAccessList = $this->getDoctrine()->getManager()->getRepository(User::class)->getLockedAccessList();

        $countActiveAccess = $this->getDoctrine()->getManager()->getRepository(User::class)->getCountActiveAccess();

        return $this->render('Secretariat/Interface/index.html.twig', array('clubManagerList' => $clubManagerList, 'secretariatAccessList' => $secretariatAccessList, 'lockedAccessList' => $lockedAccessList, 'countActiveAccess' => $countActiveAccess[0][1]));
    }

    /**
     * @param UserTools $userTools
     * @param SessionInterface $session
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route('/creer-acces/', name:'accessCreate')]
    public function accessCreate(UserTools $userTools, SessionInterface $session, Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(UserType::class, new User(), array('form' => 'createAccess', 'data_class' => User::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $userTools->newUser($form->getData(), $this->getUser(), $form['Password']->getData(), $form->get('UserMember')->getData());

            $session->set('duplicate', $userTools->isDuplicate());

            if ($session->get('duplicate'))
            {
                return $this->render('Secretariat/Interface/access_create.html.twig', array('form' => $form->createView()));
            }

            return $this->redirectToRoute('secretariat-accessListIndex');
        }

        return $this->render('Secretariat/Interface/access_create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param User $user
     * @param UserTools $userTools
     * @return RedirectResponse
     */
    #[Route('/reactivation-acces/{user<\d+>}', name:'accessReactivate')]
    public function accessReactivate(User $user, UserTools $userTools): RedirectResponse
    {
        $userTools->reactivate($user, $this->getUser());

        return $this->redirectToRoute('secretariat-accessListIndex');
    }

    /**
     * @param SessionInterface $session
     * @param Request $request
     * @param User $user
     * @param UserTools $userTools
     * @return RedirectResponse|Response
     */
    #[Route('/modification-mot-de-passe-acces/{user<\d+>}', name:'accessPasswordModify')]
    public function accessPasswordModify(SessionInterface $session, Request $request, User $user, UserTools $userTools): RedirectResponse|Response
    {
        $session->set('passwordError', false);

        $form = $this->createForm(UserType::class, $user, array('form' => 'changePassword', 'data_class' => User::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($userTools->changePassword($form->getData(), $form['Password1']->getData(), $form['Password2']->getData()))
            {
                return $this->redirectToRoute('secretariat-accessListIndex');
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
     * @param Request $request
     * @return Response
     */
    #[Route('/imprimer-timbres', name:'printStamp')]
    public function printStamp(Request $request): Response
    {
        $stamps = null;

        $form = $this->createForm(SecretariatType::class, $stamps, array('form' => 'printStamp', 'data_class' => null));

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
     * @param Request $request
     * @return Response
     */
    #[Route('/imprimer-cartes', name:'printCard')]
    public function printCard(Request $request): Response
    {
        $cards = null;

        $form = $this->createForm(SecretariatType::class, $cards, array('form' => 'printCard', 'data_class' => null));

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
     * @param Request $request
     * @param Club $club
     * @return BinaryFileResponse|Response
     */
    #[Route('/generer-formulaires-renouvellement/{club<\d+>}', name:'formRenewCreate')]
    public function formRenewCreate(Request $request, Club $club): BinaryFileResponse|Response
    {
        $period = null;

        $form = $this->createForm(SecretariatType::class, $period, array('form' => 'formRenewCreate', 'data_class' => null));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $listData = new ListData();

            $output_file = "./licence_out.rtf";

            $fh = fopen($output_file, 'a') or die('can\'t open file');

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
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

            return $response->deleteFileAfterSend();
        }

        return $this->render('Secretariat/renew_form.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    #[Route('/nettoyage-liste-membres', name:'memberListCleanup')]
    public function memberListCleanup(Request $request): RedirectResponse|Response
    {
        $list = $this->getDoctrine()->getRepository(Member::class)->getMemberListCleanup();

        $form = $this->createForm(SecretariatType::class,null, array('form' => 'cleanupMember', 'data_class' => null));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $fridge = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_id' => 9999]);

            $entityManager = $this->getDoctrine()->getManager();

            for ($i = 0; $i <= 50; $i++)
            {
                if (!isset($list[$i]))
                {
                    break;
                }

                $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $list[$i]['Id']]);

                $licence_old = $member->getMemberLastLicence();

                $licence_old->setMemberLicenceStatus(0);

                $licence_new = new MemberLicence();

                $licence_new->setMemberLicence($member);
                $licence_new->setMemberLicenceClub($fridge);
                $licence_new->setMemberLicenceUpdate(new DateTime('today'));
                $licence_new->setMemberLicenceDeadline($licence_old->getMemberLicenceDeadline());
                $licence_new->setMemberLicenceStatus(1);
                $licence_new->setMemberLicenceMedicalCertificate($licence_old->getMemberLicenceMedicalCertificate());

                $member->setMemberActualClub($fridge);
                $member->setMemberLastLicence($licence_new);

                $entityManager->persist($licence_new);
                $entityManager->flush();
            }

            return $this->redirectToRoute('secretariat-memberListCleanup');
        }

        return $this->render('Secretariat/Member/cleanup.html.twig', array('form' => $form->createView(), 'list' => $list));
    }

    /**
     * @param ClubModificationLog|null $modification
     * @return Response
     */
    #[Route('/liste-modification-dojo/{modification<\d+>}', name:'dojoModificationList')]
    public function dojoModificationList(?ClubModificationLog $modification = null): Response
    {
        if (!is_null($modification))
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($modification);
            $entityManager->flush();
        }

        $modification_list = $this->getDoctrine()->getManager()->getRepository(ClubModificationLog::class)->findAll();

        return $this->render('Secretariat/Club/modification_list.html.twig', array('modification_list' => $modification_list));
    }
}
