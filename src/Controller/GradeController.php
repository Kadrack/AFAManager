<?php
// src/Controller/GradeController.php
namespace App\Controller;

use App\Entity\GradeDan;
use App\Entity\GradeSession;
use App\Entity\Member;

use App\Form\ExamType;

use App\Service\ListData;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GradeController extends AbstractController
{
    /**
     * @Route("/grade/session-examen", name="grade_exam_index")
     */    
    public function exam_index()
    {
        $sessions = $this->getDoctrine()->getRepository(GradeSession::class)->findBy(['grade_session_type' => 1], ['grade_session_date' => 'DESC', 'grade_session_type' => 'DESC']);

        return $this->render('Grade/Exam/index.html.twig', array('sessions' => $sessions));
    }

    /**
     * @Route("/grade/session-examen/creer", name="grade_exam_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function exam_create(Request $request)
    {
        $form = $this->createForm(ExamType::class, new GradeSession());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $session = $form->getData();

            $session->setGradeSessionType(1);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('grade_exam_index');
        }

        return $this->render('Grade/Exam/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/grade/session-examen/{session_id<\d+>}/modifier", name="grade_exam_update")
     * @param Request $request
     * @param int $session_id
     * @return RedirectResponse|Response
     */
    public function exam_update(Request $request, int $session_id)
    {
        $session = $this->getDoctrine()->getRepository(GradeSession::class)->findOneBy(['grade_session_id' => $session_id]);

        $form = $this->createForm(ExamType::class, $session, array('form' => 'update'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('grade_exam_index', array('session_id' => $session_id));
        }

        return $this->render('Grade/Exam/update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/grade/session-examen/{session_id<\d+>}/detail", name="grade_exam_detail")
     * @param int $session_id
     * @return Response
     */
    public function exam_detail(int $session_id)
    {
        $applicants = $this->getDoctrine()->getRepository(GradeDan::class)->findBy(['grade_dan_status' => 1, 'grade_dan_exam' => $session_id], ['grade_dan_rank' => 'ASC']);

        $candidates = $this->getDoctrine()->getRepository(GradeDan::class)->findBy(['grade_dan_status' => 2, 'grade_dan_exam' => $session_id], ['grade_dan_rank' => 'ASC']);

        $refused    = $this->getDoctrine()->getRepository(GradeDan::class)->findBy(['grade_dan_status' => 3, 'grade_dan_exam' => $session_id], ['grade_dan_rank' => 'ASC']);

        $promoted   = $this->getDoctrine()->getRepository(GradeDan::class)->findBy(['grade_dan_status' => array(4,5), 'grade_dan_exam' => $session_id], ['grade_dan_rank' => 'ASC']);

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
                $promoted_filtered[$i-1]['Aikikai_Certificate'] = $promote->getGradeDanCertificate();
            }

            $i++;
        }

        return $this->render('Grade/Exam/detail.html.twig', array('session_id' => $session_id, 'applicants' => $applicants, 'candidates' => $candidates, 'promoted' => $promoted_filtered, 'refused' => $refused, 'listData' => new ListData()));
    }

    /**
     * @Route("/grade/session-examen/{session_id<\d+>}/postulant/{member_id<\d+>}/grade/{grade_id<\d+>}/detail", name="grade_exam_applicant_detail")
     * @param Request $request
     * @param int $session_id
     * @param int $member_id
     * @param int $grade_id
     * @return RedirectResponse|Response
     */
    public function exam_applicant_detail(Request $request, int $session_id, int $member_id, int $grade_id)
    {
        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

        $grade = $this->getDoctrine()->getRepository(GradeDan::class)->findOneBy(['grade_dan_id' => $grade_id]);

        $form = $this->createForm(ExamType::class, $grade, array('form' => 'applicant_validation', 'data_class' => GradeDan::class, 'action' => $this->generateUrl('grade_exam_applicant_detail', array('session_id' => $session_id, 'member_id' => $member_id, 'grade_id' => $grade_id)), 'method' => 'POST'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $grade->setGradeDanStatus(2);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('grade_exam_detail', array('session_id' => $session_id));
        }

        return $this->render('Grade/Exam/applicant_detail.html.twig', array('session_id' => $session_id, 'member' => $member, 'form' => $form->createView()));
    }

    /**
     * @Route("/grade/session-examen/{session_id<\d+>}/candidat/{member_id<\d+>}/grade/{grade_id<\d+>}/detail", name="grade_exam_candidate_detail")
     * @param Request $request
     * @param int $session_id
     * @param int $member_id
     * @param int $grade_id
     * @return RedirectResponse|Response
     */
    public function exam_candidate_detail(Request $request, int $session_id, int $member_id, int $grade_id)
    {
        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

        $grade = $this->getDoctrine()->getRepository(GradeDan::class)->findOneBy(['grade_dan_id' => $grade_id]);

        $form = $this->createForm(ExamType::class, $grade, array('form' => 'candidate_result', 'data_class' => GradeDan::class, 'action' => $this->generateUrl('grade_exam_candidate_detail', array('session_id' => $session_id, 'member_id' => $member_id, 'grade_id' => $grade_id)), 'method' => 'POST'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $member->setMemberLastGradeDan($grade);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('grade_exam_detail', array('session_id' => $session_id));
        }

        return $this->render('Grade/Exam/candidate_detail.html.twig', array('session_id' => $session_id, 'member' => $member, 'form' => $form->createView()));
    }

    /**
     * @Route("/grade/session-examen/{session_id<\d+>}/candidat/{member_id<\d+>}/grade/{grade_id<\d+>}/detail_update", name="grade_exam_candidate_detail_update")
     * @param Request $request
     * @param int $session_id
     * @param int $member_id
     * @param int $grade_id
     * @return RedirectResponse|Response
     */
    public function exam_candidate_detail_update(Request $request, int $session_id, int $member_id, int $grade_id)
    {
        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

        $grade = $this->getDoctrine()->getRepository(GradeDan::class)->findOneBy(['grade_dan_id' => $grade_id]);

        $form = $this->createForm(ExamType::class, $grade, array('form' => 'candidate_result', 'data_class' => GradeDan::class, 'action' => $this->generateUrl('grade_exam_candidate_detail_update', array('session_id' => $session_id, 'member_id' => $member_id, 'grade_id' => $grade_id)), 'method' => 'POST'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            if ($grade->getGradeDanStatus() == 3)
            {
                $member->setMemberLastGradeDan($grade);

                $grade_aikikai = $this->getDoctrine()->getRepository(GradeDan::class)->findOneBy(['grade_dan_member' => $member_id, 'grade_dan_exam' => $session_id, 'grade_dan_status' => 5]);

                if ($grade_aikikai != null)
                {
                    $entityManager->remove($grade_aikikai);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('grade_exam_detail', array('session_id' => $session_id));
        }

        return $this->render('Grade/Exam/candidate_detail.html.twig', array('session_id' => $session_id, 'member' => $member, 'form' => $form->createView()));
    }

    /**
     * @Route("/grade/session-examen/{session_id<\d+>}/candidat/{member_id<\d+>}/grade/{grade_id<\d+>}/ajouter_aikikai", name="grade_exam_candidate_add_aikikai")
     * @param Request $request
     * @param int $session_id
     * @param int $member_id
     * @param int $grade_id
     * @return RedirectResponse|Response
     */
    public function exam_candidate_add_aikikai(Request $request, int $session_id, int $member_id, int $grade_id)
    {
        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

        $grade = $this->getDoctrine()->getRepository(GradeDan::class)->findOneBy(['grade_dan_id' => $grade_id]);

        $grade_aikikai = new GradeDan();

        $grade_aikikai->setGradeDanRank($grade->getGradeDanRank() + 1);
        $grade_aikikai->setGradeDanStatus($grade->getGradeDanStatus() + 1);
        $grade_aikikai->setGradeDanClub($grade->getGradeDanClub());
        $grade_aikikai->setGradeDanExam($grade->getGradeDanExam());
        $grade_aikikai->setGradeDanMember($grade->getGradeDanMember());

        $form = $this->createForm(ExamType::class, $grade_aikikai, array('form' => 'candidate_aikikai', 'data_class' => GradeDan::class, 'action' => $this->generateUrl('grade_exam_candidate_add_aikikai', array('session_id' => $session_id, 'member_id' => $member_id, 'grade_id' => $grade_id)), 'method' => 'POST'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $member->setMemberLastGradeDan($grade_aikikai);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($grade_aikikai);
            $entityManager->flush();

            return $this->redirectToRoute('grade_exam_detail', array('session_id' => $session_id));
        }

        return $this->render('Grade/Exam/candidate_detail.html.twig', array('session_id' => $session_id, 'member' => $member, 'grade' => $grade, 'form' => $form->createView()));

    }

    /**
     * @Route("/grade/session-examen/{session_id<\d+>}/candidat/{member_id<\d+>}/grade/{grade_id<\d+>}/detail_aikikai", name="grade_exam_candidate_detail_aikikai")
     * @param Request $request
     * @param int $session_id
     * @param int $member_id
     * @param int $grade_id
     * @return RedirectResponse|Response
     */
    public function exam_candidate_detail_aikikai(Request $request, int $session_id, int $member_id, int $grade_id)
    {
        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

        $grade = $this->getDoctrine()->getRepository(GradeDan::class)->findOneBy(['grade_dan_id' => $grade_id]);

        $form = $this->createForm(ExamType::class, $grade, array('form' => 'candidate_aikikai', 'data_class' => GradeDan::class, 'action' => $this->generateUrl('grade_exam_candidate_detail_aikikai', array('session_id' => $session_id, 'member_id' => $member_id, 'grade_id' => $grade_id)), 'method' => 'POST'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $member->setMemberLastGradeDan($grade);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('grade_exam_detail', array('session_id' => $session_id));
        }

        return $this->render('Grade/Exam/candidate_detail.html.twig', array('session_id' => $session_id, 'member' => $member, 'form' => $form->createView()));
    }
}
