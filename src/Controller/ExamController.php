<?php
// src/Controller/ExamController.php
namespace App\Controller;

use App\Entity\GradeDan;
use App\Entity\GradeSession;
use App\Entity\Member;

use App\Form\ExamType;

use App\Service\ListData;

use Doctrine\Common\Collections\Criteria;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExamController extends AbstractController
{
    /**
     * @Route("/session-examen", name="exam_index")
     */    
    public function index()
    {
        $exams = $this->getDoctrine()->getRepository(GradeSession::class)->findBy([], ['grade_session_date' => 'DESC', 'grade_session_type' => 'DESC']);

        return $this->render('Exam/index.html.twig', array('exams' => $exams, 'listData' => new ListData()));
    }

    /**
     * @Route("/session-examen/creer", name="exam_create")
     */
    public function create(Request $request)
    {
        $form = $this->createForm(ExamType::class, new GradeSession());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $exam = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($exam);
            $entityManager->flush();

            return $this->redirectToRoute('exam_index');
        }

        return $this->render('Exam/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/session-examen/{exam_id<\d+>}/modifier", name="exam_update")
     */
    public function update(Request $request, int $exam_id)
    {
        $repository = $this->getDoctrine()->getRepository(GradeSession::class);

        $exam = $repository->findOneBy(['grade_session_id' => $exam_id]);

        $form = $this->createForm(ExamType::class, $exam, array('form' => 'update'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('exam_index', array('exam_id' => $exam_id));
        }

        return $this->render('Exam/update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/session-examen/{exam_id<\d+>}/detail", name="exam_detail")
     */
    public function detail(int $exam_id)
    {
        $exam = $this->getDoctrine()->getRepository(GradeSession::class)->findOneBy(['grade_session_id' => $exam_id]);

        $applicants = $this->getDoctrine()->getRepository(GradeDan::class)->findBy(['grade_dan_status' => 1, 'grade_dan_exam' => $exam_id], ['grade_dan_rank' => 'ASC']);

        $candidates = $this->getDoctrine()->getRepository(GradeDan::class)->findBy(['grade_dan_status' => 2, 'grade_dan_exam' => $exam_id], ['grade_dan_rank' => 'ASC']);

        $promoted   = $this->getDoctrine()->getRepository(GradeDan::class)->findBy(['grade_dan_status' => 3, 'grade_dan_exam' => $exam_id], ['grade_dan_rank' => 'ASC']);

        $refused    = $this->getDoctrine()->getRepository(GradeDan::class)->findBy(['grade_dan_status' => 4, 'grade_dan_exam' => $exam_id], ['grade_dan_rank' => 'ASC']);

        return $this->render('Exam/detail.html.twig', array('exam' => $exam, 'applicants' => $applicants, 'candidates' => $candidates, 'promoted' => $promoted, 'refused' => $refused, 'listData' => new ListData()));
    }

    /**
     * @Route("/session-examen/{exam_id<\d+>}/postulant/{member_id<\d+>}/detail", name="exam_applicant_detail")
     */
    public function applicant_detail(Request $request, int $exam_id, int $member_id)
    {
        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

        $grade = $this->getDoctrine()->getRepository(GradeDan::class)->findOneBy(['grade_dan_member' => $member_id, 'grade_dan_exam' => $exam_id]);

        $form = $this->createForm(ExamType::class, $grade, array('form' => 'applicant_validation', 'data_class' => GradeDan::class, 'action' => $this->generateUrl('exam_applicant_detail', array('exam_id' => $exam_id, 'member_id' => $member_id)), 'method' => 'POST'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $grade->setGradeDanStatus(2);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('exam_detail', array('exam_id' => $exam_id));
        }

        return $this->render('Exam/applicant_detail.html.twig', array('exam_id' => $exam_id, 'member' => $member, 'form' => $form->createView()));
    }

    /**
     * @Route("/session-examen/{exam_id<\d+>}/candidat/{member_id<\d+>}/detail", name="exam_candidate_detail")
     */
    public function candidate_detail(Request $request, int $exam_id, int $member_id)
    {
        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

        $grade = $this->getDoctrine()->getRepository(GradeDan::class)->findOneBy(['grade_dan_member' => $member_id, 'grade_dan_exam' => $exam_id]);

        $form = $this->createForm(ExamType::class, $grade, array('form' => 'candidate_result', 'data_class' => GradeDan::class, 'action' => $this->generateUrl('exam_candidate_detail', array('exam_id' => $exam_id, 'member_id' => $member_id)), 'method' => 'POST'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $member->setMemberLastExamResult($grade);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('exam_detail', array('exam_id' => $exam_id));
        }

        return $this->render('Exam/candidate_detail.html.twig', array('exam_id' => $exam_id, 'member' => $member, 'grade' => $grade, 'form' => $form->createView()));
    }
}
