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

use App\Form\ExamType;
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

class MemberController extends AbstractController
{
    /**
     * @Route("/club/{club_number<\d+>}/creer_membre", name="member_create")
     * @param Request $request
     * @param PhotoUploader $photoUploader
     * @param int $club_number
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function create(Request $request, PhotoUploader $photoUploader, int $club_number)
    {   
        $form = $this->createForm(MemberType::class, new Member());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

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

            return $this->redirectToRoute('club_active_members', array('club_number' => $club->getClubNumber()));
        }
        
        return $this->render('Member/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/detail_personnel/{member_id<\d+>}", name="member_detail_personnal")
     * @param int $club_number
     * @param int $member_id
     * @return Response
     */
    public function detailPersonnal(int $club_number, int $member_id)
    {
        $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

        return $this->render('Member/detail_personnal.html.twig', array('member' => $member, 'club' => $club));
    }

    /**
     * @Route("/club/{club_number<\d+>}/detail_grade/{member_id<\d+>}", name="member_detail_grade")
     * @param int $club_number
     * @param int $member_id
     * @return Response
     */
    public function detailGrade(int $club_number, int $member_id)
    {
        $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

        $today = new DateTime('today');

        $grade_dan_history = $this->getDoctrine()->getRepository(GradeDan::class)->getGradeDanHistory($member->getMemberId());

        $grade_kyu_history = $this->getDoctrine()->getRepository(GradeKyu::class)->findBy(['grade_kyu_member' => $member->getMemberId()], ['grade_kyu_rank' => 'ASC']);

        $open_session = $this->getDoctrine()->getRepository(GradeSession::class)->getOpenSession($today->format('Y-m-d'));

        if ($open_session == null)
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

        return $this->render('Member/detail_grade.html.twig', array('member' => $member, 'club' => $club, 'grade_dan_history' => sizeof($grade_dan_history) == 0 ? null : $grade_dan_history, 'grade_kyu_history' => sizeof($grade_kyu_history) == 0 ? null : $grade_kyu_history, 'exam_candidate' => $exam_candidate, 'list' => new ListData()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/detail_titre/{member_id<\d+>}", name="member_detail_title")
     * @param int $club_number
     * @param int $member_id
     * @return Response
     */
    public function detailTitle(int $club_number, int $member_id)
    {
        $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

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
     * @Route("/club/{club_number<\d+>}/detail_licence/{member_id<\d+>}", name="member_detail_licence")
     * @param int $club_number
     * @param int $member_id
     * @return Response
     */
    public function detailLicence(int $club_number, int $member_id)
    {
        $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

        $licence_history = $this->getDoctrine()->getRepository(MemberLicence::class)->findBy(['member_licence' => $member->getMemberId()], ['member_licence_id' => 'DESC']);

        $next_renew = $licence_history[0]->getMemberLicenceDeadline() < new DateTime('-3 month today');

        return $this->render('Member/detail_licence.html.twig', array('member' => $member, 'club' => $club, 'licence_history' => $licence_history, 'next_renew' => $next_renew, 'list' => new ListData()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/modifier/{member_id<\d+>}", name="member_update")
     * @param Request $request
     * @param PhotoUploader $photoUploader
     * @param int $club_number
     * @param int $member_id
     * @return RedirectResponse|Response
     */
    public function updatePersonal(Request $request, PhotoUploader $photoUploader, int $club_number, int $member_id)
    {
        $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

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

            return $this->redirectToRoute('club_active_members', array('club_number' => $club->getClubNumber(), 'member_id' => $member->getMemberId()));
        }

        return $this->render('Member/personnal_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/modifier/{member_id<\d+>}/candidature", name="member_exam_application")
     * @param Request $request
     * @param int $club_number
     * @param int $member_id
     * @return RedirectResponse|Response
     */
    public function exam_application(Request $request, int $club_number, int $member_id)
    {
        $today = new DateTime('today');

        $club   = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);
        $exam   = $this->getDoctrine()->getRepository(GradeSession::class)->getOpenSession($today->format('Y-m-d'));
        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

        if (!is_object($member->getMemberLastGradeDan()))
        {
            $rank = 7;
        }
        elseif ($member->getMemberLastGradeDan()->getGradeDanStatus() == 3)
        {
            $rank = $member->getMemberLastGradeDan()->getGradeDanRank() + 1;
        }
        else
        {
            $rank = $member->getMemberLastGradeDan()->getGradeDanRank();
        }

        $grade = new GradeDan();

        $grade->setGradeDanClub($club);
        $grade->setGradeDanExam($exam[0]);
        $grade->setGradeDanMember($member);
        $grade->setGradeDanRank($rank);
        $grade->setGradeDanStatus(1);

        $form = $this->createForm(ExamType::class, $grade, array('form' => 'exam_application', 'data_class' => GradeDan::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($grade);
            $entityManager->flush();

            return $this->redirectToRoute('member_detail_grade', array('club_number' => $club->getClubNumber(), 'member_id' => $member->getMemberId()));
        }

        return $this->render('Member/exam_application.html.twig', array('form' => $form->createView(), 'exam' => $exam[0]));
    }

    /**
     * @Route("/club/{club_number<\d+>}/modifier/{member_id<\d+>}/renouveller", name="member_licence_renew")
     * @param SessionInterface $session
     * @param Request $request
     * @param int $club_number
     * @param int $member_id
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function licence_renew(SessionInterface $session, Request $request, int $club_number, int $member_id)
    {
        $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

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
                return $this->redirectToRoute('member_detail_licence', array('club_number' => $club->getClubNumber(), 'member_id' => $member->getMemberId()));
            }
            else
            {
                return $this->redirectToRoute('club_inactive_members', array('club_number' => $club->getClubNumber()));
            }
        }

        return $this->render('Member/licence_renew.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/modifier/{member_id<\d+>}/renouveller_modifier/{renew_id<\d+>}", name="member_licence_renew_update")
     * @param SessionInterface $session
     * @param Request $request
     * @param int $club_number
     * @param int $member_id
     * @param int $renew_id
     * @return RedirectResponse|Response
     */
    public function licence_renew_update(SessionInterface $session, Request $request, int $club_number, int $member_id, int $renew_id)
    {
        $renew = $this->getDoctrine()->getRepository(MemberLicence::class)->findOneBy(['member_licence_id' => $renew_id]);

        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

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
                return $this->redirectToRoute('member_detail_licence', array('club_number' => $club_number, 'member_id' => $member->getMemberId()));
            }
            else
            {
                return $this->redirectToRoute('club_inactive_members', array('club_number' => $club_number));
            }
        }

        return $this->render('Member/licence_renew.html.twig', array('form' => $form->createView()));
    }
}
