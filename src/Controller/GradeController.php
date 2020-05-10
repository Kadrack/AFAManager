<?php
// src/Controller/GradeController.php
namespace App\Controller;

use App\Entity\GradeDan;
use App\Entity\GradeSession;
use App\Entity\Member;

use App\Form\GradeType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/grade", name="grade_")
 *
 * @IsGranted("ROLE_CFG")
 */
class GradeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('Grade/index.html.twig');
    }

    /**
     * @Route("/session-examen", name="exam_index")
     */
    public function exam_index()
    {
        $sessions = $this->getDoctrine()->getRepository(GradeSession::class)->findBy(['grade_session_type' => 1], ['grade_session_date' => 'DESC', 'grade_session_type' => 'DESC']);

        return $this->render('Grade/Exam/index.html.twig', array('sessions' => $sessions));
    }

    /**
     * @Route("/session-examen/{session<\d+>}/detail", name="exam_detail")
     *
     * @param GradeSession $session
     * @return Response
     */
    public function exam_detail(GradeSession $session)
    {
        $applicants = $this->getDoctrine()->getRepository(GradeDan::class)->findBy(['grade_dan_status' => 1, 'grade_dan_exam' => $session->getGradeSessionId()], ['grade_dan_rank' => 'ASC']);

        $candidates = $this->getDoctrine()->getRepository(GradeDan::class)->findBy(['grade_dan_status' => 2, 'grade_dan_exam' => $session->getGradeSessionId()], ['grade_dan_rank' => 'ASC']);

        $refused    = $this->getDoctrine()->getRepository(GradeDan::class)->findBy(['grade_dan_status' => 3, 'grade_dan_exam' => $session->getGradeSessionId()], ['grade_dan_rank' => 'ASC']);

        $promoted   = $this->getDoctrine()->getRepository(GradeDan::class)->findBy(['grade_dan_status' => array(4,5), 'grade_dan_exam' => $session->getGradeSessionId()], ['grade_dan_rank' => 'ASC']);

        $promoted_filtered = array(); $i = 0;

        foreach ($promoted as $promote)
        {
            if ($promote->getGradeDanStatus() == 4)
            {
                $promoted_filtered[$i]['Grade_Id']  = $promote->getGradeDanId();

                $promoted_filtered[$i]['Id']        = $promote->getGradeDanMember()->getMemberId();
                $promoted_filtered[$i]['Name']      = $promote->getGradeDanMember()->getMemberName();
                $promoted_filtered[$i]['FirstName'] = $promote->getGradeDanMember()->getMemberFirstName();
                $promoted_filtered[$i]['Grade']     = $promote->getGradeDanMember()->getMemberLastGradeDan()->getGradeDanRank();

                $promoted_filtered[$i]['Aikikai_Certificate'] = null;
                $promoted_filtered[$i]['Federal_Certificate'] = $promote->getGradeDanCertificate();
            }
            else
            {
                $promoted_filtered[$i-1]['Grade_Aikikai_Id']    = $promote->getGradeDanId();
                $promoted_filtered[$i-1]['Aikikai_Certificate'] = $promote->getGradeDanCertificate();
            }

            $i++;
        }

        return $this->render('Grade/Exam/detail.html.twig', array('session' => $session, 'applicants' => $applicants, 'candidates' => $candidates, 'promoted' => $promoted_filtered, 'refused' => $refused));
    }

    /**
     * @Route("/session-examen/{session<\d+>}/postulant/{member<\d+>}/grade/{grade<\d+>}/detail", name="exam_applicant_detail")
     *
     * @param Request $request
     * @param GradeSession $session
     * @param Member $member
     * @param GradeDan $grade
     * @return RedirectResponse|Response
     */
    public function exam_applicant_detail(Request $request, GradeSession $session, Member $member, GradeDan $grade)
    {
        $form = $this->createForm(GradeType::class, $grade, array('form' => 'exam_applicant_validation', 'data_class' => GradeDan::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $grade->setGradeDanStatus(2);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('grade_exam_detail', array('session' => $session->getGradeSessionId()));
        }

        return $this->render('Grade/Exam/applicant_detail.html.twig', array('session' => $session, 'member' => $member, 'form' => $form->createView()));
    }

    /**
     * @Route("/session-examen/{session<\d+>}/candidat/{member<\d+>}/grade/{grade<\d+>}/detail", name="exam_candidate_detail")
     *
     * @param Request $request
     * @param GradeSession $session
     * @param Member $member
     * @param GradeDan $grade
     * @return RedirectResponse|Response
     */
    public function exam_candidate_detail(Request $request, GradeSession $session, Member $member, GradeDan $grade)
    {
        $form = $this->createForm(GradeType::class, $grade, array('form' => 'exam_candidate_result', 'data_class' => GradeDan::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $member->setMemberLastGradeDan($grade);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('grade_exam_detail', array('session' => $session->getGradeSessionId()));
        }

        return $this->render('Grade/Exam/candidate_detail.html.twig', array('session' => $session, 'member' => $member, 'form' => $form->createView()));
    }

    /**
     * @Route("/session-examen/{session<\d+>}/candidat/{member<\d+>}/grade/{grade<\d+>}/detail_update", name="exam_candidate_detail_update")
     *
     * @param Request $request
     * @param GradeSession $session
     * @param Member $member
     * @param GradeDan $grade
     * @return RedirectResponse|Response
     */
    public function exam_candidate_detail_update(Request $request, GradeSession $session, Member $member, GradeDan $grade)
    {
        $form = $this->createForm(GradeType::class, $grade, array('form' => 'exam_candidate_result', 'data_class' => GradeDan::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            if ($grade->getGradeDanStatus() == 3)
            {
                $member->setMemberLastGradeDan($grade);

                $grade_aikikai = $this->getDoctrine()->getRepository(GradeDan::class)->findOneBy(['grade_dan_member' => $member->getMemberId(), 'grade_dan_exam' => $session->getGradeSessionId(), 'grade_dan_status' => 5]);

                if ($grade_aikikai != null)
                {
                    $entityManager->remove($grade_aikikai);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('grade_exam_detail', array('session' => $session->getGradeSessionId()));
        }

        return $this->render('Grade/Exam/candidate_detail.html.twig', array('session' => $session, 'member' => $member, 'form' => $form->createView()));
    }

    /**
     * @Route("/session-examen/{session<\d+>}/candidat/{member<\d+>}/grade/{grade<\d+>}/ajouter_aikikai", name="exam_candidate_add_aikikai")
     *
     * @param Request $request
     * @param GradeSession $session
     * @param Member $member
     * @param GradeDan $grade
     * @return RedirectResponse|Response
     */
    public function exam_candidate_add_aikikai(Request $request, GradeSession $session, Member $member, GradeDan $grade)
    {
        $grade_aikikai = new GradeDan();

        $grade_aikikai->setGradeDanRank($grade->getGradeDanRank() + 1);
        $grade_aikikai->setGradeDanStatus($grade->getGradeDanStatus() + 1);
        $grade_aikikai->setGradeDanClub($grade->getGradeDanClub());
        $grade_aikikai->setGradeDanExam($grade->getGradeDanExam());
        $grade_aikikai->setGradeDanMember($grade->getGradeDanMember());

        $form = $this->createForm(GradeType::class, $grade_aikikai, array('form' => 'exam_candidate_aikikai', 'data_class' => GradeDan::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $member->setMemberLastGradeDan($grade_aikikai);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($grade_aikikai);
            $entityManager->flush();

            return $this->redirectToRoute('grade_exam_detail', array('session' => $session->getGradeSessionId()));
        }

        return $this->render('Grade/Exam/candidate_detail.html.twig', array('session' => $session, 'member' => $member, 'grade' => $grade, 'form' => $form->createView()));

    }

    /**
     * @Route("/session-examen/{session<\d+>}/candidat/{member<\d+>}/grade/{grade<\d+>}/detail_aikikai", name="exam_candidate_detail_aikikai")
     *
     * @param Request $request
     * @param GradeSession $session
     * @param Member $member
     * @param GradeDan $grade
     * @return RedirectResponse|Response
     */
    public function exam_candidate_detail_aikikai(Request $request, GradeSession $session, Member $member, GradeDan $grade)
    {
        $form = $this->createForm(GradeType::class, $grade, array('form' => 'exam_candidate_aikikai', 'data_class' => GradeDan::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $member->setMemberLastGradeDan($grade);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('grade_exam_detail', array('session' => $session->getGradeSessionId()));
        }

        return $this->render('Grade/Exam/candidate_detail.html.twig', array('session' => $session, 'member' => $member, 'form' => $form->createView()));
    }

    /**
     * @Route("/kagami", name="kagami_index")
     */
    public function kagami_index()
    {
        $sessions = $this->getDoctrine()->getRepository(GradeSession::class)->findBy(['grade_session_type' => 2], ['grade_session_date' => 'DESC', 'grade_session_type' => 'DESC']);

        return $this->render('Grade/Kagami/index.html.twig', array('sessions' => $sessions));
    }

    /**
     * @Route("/kagami/{session<\d+>}/detail", name="kagami_detail")
     *
     * @param GradeSession $session
     * @return Response
     */
    public function kagami_detail(GradeSession $session)
    {
        $candidates = $this->getDoctrine()->getRepository(GradeDan::class)->findBy(['grade_dan_status' => 1, 'grade_dan_exam' => $session->getGradeSessionId()], ['grade_dan_rank' => 'ASC']);

        $refused    = $this->getDoctrine()->getRepository(GradeDan::class)->findBy(['grade_dan_status' => 3, 'grade_dan_exam' => $session->getGradeSessionId()], ['grade_dan_rank' => 'ASC']);

        $promoted   = $this->getDoctrine()->getRepository(GradeDan::class)->findBy(['grade_dan_status' => array(4,5), 'grade_dan_exam' => $session->getGradeSessionId()], ['grade_dan_rank' => 'ASC']);

        return $this->render('Grade/Kagami/detail.html.twig', array('session' => $session, 'candidates' => $candidates, 'refused' => $refused, 'promoted' => $promoted));
    }

    /**
     * @Route("/kagami/{session<\d+>}/candidat/{member<\d+>}/grade/{grade<\d+>}/detail", name="kagami_candidate_detail")
     *
     * @param Request $request
     * @param GradeSession $session
     * @param Member $member
     * @param GradeDan $grade
     * @return RedirectResponse|Response
     */
    public function kagami_candidate_detail(Request $request, GradeSession $session, Member $member, GradeDan $grade)
    {
        $form = $this->createForm(GradeType::class, $grade, array('form' => 'kagami_candidate_result', 'data_class' => GradeDan::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $member->setMemberLastGradeDan($grade);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('grade_kagami_detail', array('session' => $session->getGradeSessionId()));
        }

        return $this->render('Grade/Kagami/candidate_detail.html.twig', array('session' => $session, 'member' => $member, 'form' => $form->createView()));
    }

    /**
     * @Route("/kagami/{session<\d+>}/candidat/{member<\d+>}/grade/{grade<\d+>}/detail_update", name="kagami_candidate_detail_update")
     *
     * @param Request $request
     * @param GradeSession $session
     * @param Member $member
     * @param GradeDan $grade
     * @return RedirectResponse|Response
     */
    public function kagami_candidate_detail_update(Request $request, GradeSession $session, Member $member, GradeDan $grade)
    {
        $form = $this->createForm(GradeType::class, $grade, array('form' => 'kagami_candidate_result', 'data_class' => GradeDan::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('grade_kagami_detail', array('session' => $session->getGradeSessionId()));
        }

        return $this->render('Grade/Kagami/candidate_detail.html.twig', array('session' => $session, 'member' => $member, 'form' => $form->createView()));
    }
}
