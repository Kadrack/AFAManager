<?php
// src/Controller/MemberController.php
namespace App\Controller;

use App\Entity\Club;
use App\Entity\GradeDan;
use App\Entity\GradeKyu;
use App\Entity\GradeSession;
use App\Entity\GradeTitle;
use App\Entity\Member;
use App\Entity\MemberLicence;
use App\Entity\MemberPrintout;

use App\Form\GradeType;
use App\Form\MemberType;

use App\Service\ListData;

use App\Service\PhotoUploader;

use DateTime;

use Exception;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/club", name="member_")
 */
class MemberController extends AbstractController
{
    /**
     * @Route("/{club<\d+>}/creer_membre", name="create")
     * @param Request $request
     * @param PhotoUploader $photoUploader
     * @param Club $club
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function create(Request $request, PhotoUploader $photoUploader, Club $club)
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

            return $this->redirectToRoute('club_active_members', array('club' => $club->getClubId()));
        }
        
        return $this->render('Member/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{club<\d+>}/detail_personnel/{member<\d+>}", name="detail_personal")
     * @param Club $club
     * @param Member $member
     * @return Response
     */
    public function detailPersonal(Club $club, Member $member)
    {
        return $this->render('Member/detail_personal.html.twig', array('member' => $member, 'club' => $club));
    }

    /**
     * @Route("/{club<\d+>}/detail_grade/{member<\d+>}", name="detail_grade")
     * @param Club $club
     * @param Member $member
     * @return Response
     */
    public function detailGrade(Club $club, Member $member)
    {
        $today = new DateTime('today');

        $grade_dan_history = $this->getDoctrine()->getRepository(GradeDan::class)->getGradeDanHistory($member->getMemberId());

        $grade_kyu_history = $this->getDoctrine()->getRepository(GradeKyu::class)->findBy(['grade_kyu_member' => $member->getMemberId()], ['grade_kyu_rank' => 'ASC']);

        $open_session = $this->getDoctrine()->getRepository(GradeSession::class)->getOpenSession($today->format('Y-m-d'), 1);

        if (($open_session == null) || ($grade_dan_history[0]['Type'] == 2) || ($grade_dan_history[0]['Rank'] >= 14))
        {
            $exam_candidate = false;
        }
        elseif (isset($grade_dan_history[0]['Session']))
        {
            if ($grade_dan_history[0]['Session'] == $open_session[0]->getGradeSessionId())
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
        elseif (isset($grade_dan_history[0]['Session']))
        {
            if ($grade_dan_history[0]['Session'] == $open_session[0]->getGradeSessionId())
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

        return $this->render('Member/detail_grade.html.twig', array('member' => $member, 'club' => $club, 'grade_dan_history' => sizeof($grade_dan_history) == 0 ? null : $grade_dan_history, 'grade_kyu_history' => sizeof($grade_kyu_history) == 0 ? null : $grade_kyu_history, 'exam_candidate' => $exam_candidate, 'kagami_candidate' => $kagami_candidate, 'list' => new ListData()));
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

        return $this->render('Member/detail_title.html.twig', array('member' => $member, 'club' => $club, 'aikikai' => $i == 0 ? null : $aikikai, 'old_adeps' => $j == 0 ? null : $old_adeps, 'adeps' => $k == 0 ? null : $adeps, 'list' => new ListData()));
    }

    /**
     * @Route("/{club<\d+>}/detail_licence/{member<\d+>}", name="detail_licence")
     * @param Club $club
     * @param Member $member
     * @return Response
     */
    public function detailLicence(Club $club, Member $member)
    {
        $licence_history = $this->getDoctrine()->getRepository(MemberLicence::class)->findBy(['member_licence' => $member->getMemberId()], ['member_licence_id' => 'DESC']);

        $next_renew = $licence_history[0]->getMemberLicenceDeadline() < new DateTime('-3 month today');

        return $this->render('Member/detail_licence.html.twig', array('member' => $member, 'club' => $club, 'licence_history' => $licence_history, 'next_renew' => $next_renew, 'list' => new ListData()));
    }

    /**
     * @Route("/{club<\d+>}/modifier/{member<\d+>}", name="update")
     * @param Request $request
     * @param PhotoUploader $photoUploader
     * @param Club $club
     * @param Member $member
     * @return RedirectResponse|Response
     */
    public function updatePersonal(Request $request, PhotoUploader $photoUploader, Club $club, Member $member)
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

            return $this->redirectToRoute('club_active_members', array('club' => $club->getClubId(), 'member' => $member->getMemberId()));
        }

        return $this->render('Member/personal_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{club<\d+>}/modifier/{member<\d+>}/candidature_examen", name="exam_application")
     * @param Request $request
     * @param Club $club
     * @param Member $member
     * @return RedirectResponse|Response
     */
    public function exam_application(Request $request, Club $club, Member $member)
    {
        $today = new DateTime('today');

        $exam   = $this->getDoctrine()->getRepository(GradeSession::class)->getOpenSession($today->format('Y-m-d'), 1);

        if (!is_object($member->getMemberLastGradeDan()))
        {
            $rank = 7;
        }
        elseif ($member->getMemberLastGradeDan()->getGradeDanStatus() == 3)
        {
            $rank = $member->getMemberLastGradeDan()->getGradeDanRank();
        }
        elseif ($member->getMemberLastGradeDan()->getGradeDanStatus() == 5)
        {
            $rank = $member->getMemberLastGradeDan()->getGradeDanRank() + 1;
        }
        else
        {
            return $this->redirectToRoute('member_detail_grade', array('club' => $club->getClubId(), 'member' => $member->getMemberId()));
        }

        $grade = new GradeDan();

        $grade->setGradeDanClub($club);
        $grade->setGradeDanExam($exam[0]);
        $grade->setGradeDanMember($member);
        $grade->setGradeDanRank($rank);
        $grade->setGradeDanStatus(1);

        $form = $this->createForm(GradeType::class, $grade, array('form' => 'exam_application', 'data_class' => GradeDan::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($grade);
            $entityManager->flush();

            return $this->redirectToRoute('member_detail_grade', array('club' => $club->getClubId(), 'member' => $member->getMemberId()));
        }

        return $this->render('Member/exam_application.html.twig', array('form' => $form->createView(), 'exam' => $exam[0]));
    }

    /**
     * @Route("/{club<\d+>}/modifier/{member<\d+>}/candidature_kagami", name="kagami_application")
     * @param Request $request
     * @param Club $club
     * @param Member $member
     * @return RedirectResponse|Response
     */
    public function kagami_application(Request $request, Club $club, Member $member)
    {
        $today  = new DateTime('today');

        $kagami = $this->getDoctrine()->getRepository(GradeSession::class)->getOpenSession($today->format('Y-m-d'), 2);

        if (!is_object($member->getMemberLastGradeDan()))
        {
            $rank = 7;
        }
        elseif ($member->getMemberLastGradeDan()->getGradeDanStatus() == 3)
        {
            $rank = $member->getMemberLastGradeDan()->getGradeDanRank();
        }
        elseif (($member->getMemberLastGradeDan()->getGradeDanStatus() == 4) || ($member->getMemberLastGradeDan()->getGradeDanStatus() == 5))
        {
            $rank = $member->getMemberLastGradeDan()->getGradeDanRank() + 1;
        }
        else
        {
            return $this->redirectToRoute('member_detail_grade', array('club' => $club->getClubId(), 'member' => $member->getMemberId()));
        }

        $grade = new GradeDan();

        $grade->setGradeDanClub($club);
        $grade->setGradeDanExam($kagami[0]);
        $grade->setGradeDanMember($member);
        $grade->setGradeDanRank($rank);
        $grade->setGradeDanStatus(1);

        $form = $this->createForm(GradeType::class, $grade, array('form' => 'exam_application', 'data_class' => GradeDan::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($grade);
            $entityManager->flush();

            return $this->redirectToRoute('member_detail_grade', array('club' => $club->getClubId(), 'member' => $member->getMemberId()));
        }

        return $this->render('Member/exam_application.html.twig', array('form' => $form->createView(), 'exam' => $kagami[0]));
    }

    /**
     * @Route("/{club<\d+>}/modifier/{member<\d+>}/renouveller", name="licence_renew")
     * @param SessionInterface $session
     * @param Request $request
     * @param Club $club
     * @param Member $member
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function licence_renew(SessionInterface $session, Request $request, Club $club, Member $member)
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
                return $this->redirectToRoute('member_detail_licence', array('club' => $club->getClubId(), 'member' => $member->getMemberId()));
            }
            else
            {
                return $this->redirectToRoute('club_inactive_members', array('club' => $club->getClubId()));
            }
        }

        return $this->render('Member/licence_renew.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{club<\d+>}/modifier/{member<\d+>}/renouveller_modifier/{renew<\d+>}", name="licence_renew_update")
     * @param SessionInterface $session
     * @param Request $request
     * @param Club $club
     * @param Member $member
     * @param MemberLicence $renew
     * @return RedirectResponse|Response
     */
    public function licence_renew_update(SessionInterface $session, Request $request, Club $club, Member $member, MemberLicence $renew)
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
                return $this->redirectToRoute('member_detail_licence', array('club' => $club->getClubId(), 'member' => $member->getMemberId()));
            }
            else
            {
                return $this->redirectToRoute('club_inactive_members', array('club' => $club->getClubId()));
            }
        }

        return $this->render('Member/licence_renew.html.twig', array('form' => $form->createView()));
    }
}
