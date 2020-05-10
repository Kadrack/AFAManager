<?php
// src/Controller/SecretariatController.php
namespace App\Controller;

use App\Entity\Club;
use App\Entity\ClubHistory;
use App\Entity\GradeKyu;
use App\Entity\GradeSession;
use App\Entity\Member;
use App\Entity\MemberLicence;
use App\Entity\MemberPrintout;
use App\Entity\SecretariatSupporter;

use App\Entity\Training;
use App\Entity\TrainingSession;
use App\Form\ClubType;
use App\Form\GradeType;
use App\Form\MemberType;
use App\Form\SecretariatType;

use App\Form\TrainingType;
use App\Service\ListData;
use App\Service\PhotoUploader;

use DateTime;

use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("/sympathisant_liste", name="supporter_index")
     */
    public function supporterIndex()
    {
        $supporters = $this->getDoctrine()->getRepository(SecretariatSupporter::class)->findAll();

        return $this->render('Secretariat/supporter_index.html.twig', array('supporters' => $supporters));
    }

    /**
     * @Route("/sympathisant_ajouter", name="supporter_create")
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

        return $this->render('Secretariat/supporter_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/sympathisant_modifier/{supporter<\d+>}", name="supporter_update")
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

        return $this->render('Secretariat/supporter_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/sympathisant_supprimer/{supporter<\d+>}", name="supporter_delete")
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

        return $this->render('Secretariat/supporter_delete.html.twig', array('form' => $form->createView()));
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
            $club['Members'] = 0;

            $club['Province'] = $provinces->getProvince($club['Province']);

            $active_clubs[$club['Province']]['Clubs'][$club['Name']] = $club;

            if (!isset($active_clubs[$club['Province']]['name']))
            {
                $active_clubs[$club['Province']]['province'] = $club['Province'];
            }
        }

        $inactive_list = $this->getDoctrine()->getRepository(Club::class)->getInactiveClubs();

        return $this->render('Secretariat/club_list.html.twig', array('active_clubs' => $active_clubs, 'inactive_clubs' => $inactive_list));
    }

    /**
     * @Route("/detail_association/{club<\d+>}", name="association_details")
     * @param Request $request
     * @param Club $club
     * @return RedirectResponse|Response
     */
    public function associationDetails(Request $request, Club $club)
    {
        $form = $this->createForm(ClubType::class, $club, array('form' => 'detail_association'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat_club_list');
        }

        return $this->render('Secretariat/club_association_details.html.twig', array('form' => $form->createView(), 'club' => $club));
    }

    /**
     * @Route("/liste_membres/{club<\d+>}", name="members_active")
     * @param Club $club
     * @return Response
     */
    public function membersActive(Club $club)
    {
        $today = new DateTime('today');

        $members = $this->getDoctrine()->getRepository(Member::class)->getClubActiveMembers($club, $today->format('Y-m-d'));

        return $this->render('Secretariat/members_list.html.twig', array('members' => $members, 'club' => $club));
    }

    /**
     * @Route("/liste_anciens_membres/{club<\d+>}", name="members_ancient")
     * @param Club $club
     * @return Response
     */
    public function membersAncient(Club $club)
    {
        $today = new DateTime('today');

        $members = $this->getDoctrine()->getRepository(Member::class)->getClubInactiveMembers($club, $today->format('Y-m-d'));

        return $this->render('Secretariat/members_ancient.html.twig', array('members' => $members == null ? null : $members, 'club' => $club));
    }

    /**
     * @Route("/club_creer", name="club_create")
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

        return $this->render('Secretariat/club_create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/club_desaffilier/{club<\d+>}", name="club_disaffiliate")
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

        return $this->render('Secretariat/club_disaffiliate.html.twig', array('form' => $form->createView(), 'club' => $club));
    }

    /**
     * @Route("/club_reaffilier/{club<\d+>}", name="club_reassign")
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

            return $this->redirectToRoute('club_index');
        }

        return $this->render('Secretariat/club_reassign.html.twig', array('form' => $form->createView(), 'club' => $club));
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
            $member->setMemberPhoto($form['MemberPhoto']->getData() == null ? 'nophoto.png' : $photoUploader->upload($form['MemberPhoto']->getData()));

            if ($form->get('GradeKyuRank')->getData() != null)
            {
                $grade = new GradeKyu();

                $grade->setGradeKyuRank($form->get('GradeKyuRank')->getData());
                $grade->setGradeKyuMember($member);

                $member->setMemberLastGradeKyu($grade);
                $member->addMemberGradesKyu($grade);

                $licence->setMemberLicenceGradeKyu($grade);
            }

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

        return $this->render('Secretariat/member_create.html.twig', array('form' => $form->createView()));
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

        $next_renew = $licence_history[0]->getMemberLicenceDeadline() < new DateTime('-3 month today');

        return $this->render('Secretariat/member_licence_detail.html.twig', array('member' => $member, 'club' => $club, 'licence_history' => $licence_history, 'next_renew' => $next_renew));
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
        $form = $this->createForm(MemberType::class, $member, array('form' => 'update'));

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

        return $this->render('Secretariat/personal_detail.html.twig', array('form' => $form->createView()));
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

        if ($member->getMemberLastGradeDan() != null)
        {
            $kyu = false;
        }
        else if ($member->getMemberLastGradeKyu() == null)
        {
            $kyu = true;
        }
        else if ($member->getMemberLastGradeKyu()->getGradeKyuRank() > 6)
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

            $form->get('GradeKyuRank')->setData($licence_old->getMemberLicenceGradeKyu() == null ? null : $licence_old->getMemberLicenceGradeKyu()->getGradeKyuRank());
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
                if (($licence_old->getMemberLicenceGradeKyu() == null) and ($form->get('GradeKyuRank')->getData() != null))
                {
                    $update = true;
                }
                else if ($licence_old->getMemberLicenceGradeKyu() != null)
                {
                    $update = $licence_old->getMemberLicenceGradeKyu()->getGradeKyuRank() < $form->get('GradeKyuRank')->getData();
                }
                else
                {
                    $update = false;
                }

                if ($update)
                {
                    $grade = new GradeKyu();

                    $grade->setGradeKyuRank($form->get('GradeKyuRank')->getData());
                    $grade->setGradeKyuMember($member);

                    $member->setMemberLastGradeKyu($grade);

                    $licence_new->setMemberLicenceGradeKyu($grade);

                    $entityManager->persist($grade);
                }
            }

            $member->setMemberLastLicence($licence_new);

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

        return $this->render('Secretariat/member_licence_renew.html.twig', array('form' => $form->createView()));
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
        $grade = $renew->getMemberLicenceGradeKyu() == null ? new GradeKyu() : $renew->getMemberLicenceGradeKyu();

        $kyus = $member->getMemberGradesKyu();

        if ($member->getMemberLastGradeDan() != null)
        {
            $kyu = false;
        }
        else if ($member->getMemberLastGradeKyu() == null)
        {
            $kyu = true;
        }
        else if ($member->getMemberLastGradeKyu()->getGradeKyuRank() > 6)
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

            $form->get('GradeKyuRank')->setData($member->getMemberLastGradeKyu() == null ? null : $member->getMemberLastGradeKyu()->getGradeKyuRank());
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
                if ($grade->getGradeKyuRank() != $form->get('GradeKyuRank')->getData())
                {
                    $update = true;

                    foreach ($kyus as $kyu)
                    {
                        if ($kyu->getGradeKyuRank() == $form->get('GradeKyuRank')->getData())
                        {
                            $update = false;
                        }
                    }

                    if ($update)
                    {
                        $grade->setGradeKyuRank($form->get('GradeKyuRank')->getData());

                        $grade->setGradeKyuMember($member);

                        if ($renew->getMemberLicenceGradeKyu() == null)
                        {
                            $member->setMemberLastGradeKyu($grade);

                            $renew->setMemberLicenceGradeKyu($grade);

                            $entityManager->persist($grade);
                        }
                    }
                }
            }

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

        return $this->render('Secretariat/member_licence_renew.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/sessions-examen", name="exam_index")
     */
    public function examIndex()
    {
        $sessions = $this->getDoctrine()->getRepository(GradeSession::class)->findBy(['grade_session_type' => 1], ['grade_session_date' => 'DESC', 'grade_session_type' => 'DESC']);

        return $this->render('Secretariat/exam_index.html.twig', array('sessions' => $sessions));
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

        return $this->render('Grade/Exam/create.html.twig', array('form' => $form->createView()));
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

        return $this->render('Secretariat/exam_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/kagami", name="kagami_index")
     */
    public function kagamiIndex()
    {
        $sessions = $this->getDoctrine()->getRepository(GradeSession::class)->findBy(['grade_session_type' => 2], ['grade_session_date' => 'DESC', 'grade_session_type' => 'DESC']);

        return $this->render('Secretariat/kagami_index.html.twig', array('sessions' => $sessions));
    }

    /**
     * @Route("/kagami/creer", name="kagami_create")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function kagamiCreate(Request $request)
    {
        $form = $this->createForm(GradeType::class, new GradeSession(), array('form' => 'kagami_create'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $session = $form->getData();

            $session->setGradeSessionType(2);
            $session->setGradeSessionCandidateClose($form->get('GradeSessionDate')->getData());

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat_kagami_index');
        }

        return $this->render('Secretariat/kagami_create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/kagami/{session<\d+>}/modifier", name="kagami_update")
     *
     * @param Request $request
     * @param GradeSession $session
     * @return RedirectResponse|Response
     */
    public function kagamiUpdate(Request $request, GradeSession $session)
    {
        $form = $this->createForm(GradeType::class, $session, array('form' => 'kagami_update'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat_kagami_index', array('session' => $session->getGradeSessionId()));
        }

        return $this->render('Secretariat/kagami_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/liste_stages", name="training_index")
     */
    public function trainingIndex()
    {
        $trainings = $this->getDoctrine()->getRepository(Training::class)->getActiveTrainings();

        return $this->render('Secretariat/training_index.html.twig', array('trainings' => count($trainings) == 0 ? null : $trainings));
    }

    /**
     * @Route("/stage/creer", name="training_create")
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

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($training);
            $entityManager->flush();

            return $this->redirectToRoute('training_index');
        }

        return $this->render('Secretariat/training_create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{training<\d+>}/detail", name="training_detail")
     *
     * @param Training $training
     * @return Response
     */
    public function trainingDetail(Training $training)
    {
        $sessions = $this->getDoctrine()->getRepository(TrainingSession::class)->getTrainingSessions($training->getTrainingId());

        return $this->render('Secretariat/training_detail.html.twig', array('training' => $training, 'sessions' => $sessions));
    }

    /**
     * @Route("/{training<\d+>}/modifier", name="training_update")
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

        return $this->render('Secretariat/training_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{training<\d+>}/supprimer", name="training_delete")
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

        return $this->render('Secretariat/training_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{training<\d+>}/ajouter-session", name="training_session_add")
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

        return $this->render('Secretariat/training_session_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{training<\d+>}/modifier-session/{session<\d+>}", name="training_session_update")
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

        return $this->render('Secretariat/training_session_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{training<\d+>}/supprimer-session/{session<\d+>}", name="training_session_delete")
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
                return $this->redirectToRoute('training_index');
            }
            else
            {
                return $this->redirectToRoute('secretariat_training_detail', array('training' => $training->getTrainingId()));
            }

        }

        return $this->render('Secretariat/training_session_update.html.twig', array('form' => $form->createView()));
    }
}
