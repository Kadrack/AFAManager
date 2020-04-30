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

        return $this->render('Exam/index.html.twig', array('exams' => $exams));
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

            $exam->setGradeSessionType(1);

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

        $refused    = $this->getDoctrine()->getRepository(GradeDan::class)->findBy(['grade_dan_status' => 3, 'grade_dan_exam' => $exam_id], ['grade_dan_rank' => 'ASC']);

        $promoted   = $this->getDoctrine()->getRepository(GradeDan::class)->findBy(['grade_dan_status' => array(4,5), 'grade_dan_exam' => $exam_id], ['grade_dan_rank' => 'ASC']);

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

        return $this->render('Exam/detail.html.twig', array('exam' => $exam, 'applicants' => $applicants, 'candidates' => $candidates, 'promoted' => $promoted_filtered, 'refused' => $refused, 'listData' => new ListData()));
    }

    /**
     * @Route("/session-examen/{exam_id<\d+>}/postulant/{member_id<\d+>}/grade/{grade_id<\d+>}/detail", name="exam_applicant_detail")
     */
    public function applicant_detail(Request $request, int $exam_id, int $member_id, int $grade_id)
    {
        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

        $grade = $this->getDoctrine()->getRepository(GradeDan::class)->findOneBy(['grade_dan_id' => $grade_id]);

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
     * @Route("/session-examen/{exam_id<\d+>}/candidat/{member_id<\d+>}/grade/{grade_id<\d+>}/detail", name="exam_candidate_detail")
     */
    public function candidate_detail(Request $request, int $exam_id, int $member_id, int $grade_id)
    {
        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

        $grade = $this->getDoctrine()->getRepository(GradeDan::class)->findOneBy(['grade_dan_id' => $grade_id]);

        $form = $this->createForm(ExamType::class, $grade, array('form' => 'candidate_result', 'data_class' => GradeDan::class, 'action' => $this->generateUrl('exam_candidate_detail', array('exam_id' => $exam_id, 'member_id' => $member_id, 'grade_id' => $grade_id)), 'method' => 'POST'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $member->setMemberLastGradeDan($grade);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('exam_detail', array('exam_id' => $exam_id));
        }

        return $this->render('Exam/candidate_detail.html.twig', array('exam_id' => $exam_id, 'member' => $member, 'form' => $form->createView()));
    }

    /**
     * @Route("/session-examen/{exam_id<\d+>}/candidat/{member_id<\d+>}/grade/{grade_id<\d+>}/detail_update", name="exam_candidate_detail_update")
     */
    public function candidate_detail_update(Request $request, int $exam_id, int $member_id, int $grade_id)
    {
        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

        $grade = $this->getDoctrine()->getRepository(GradeDan::class)->findOneBy(['grade_dan_id' => $grade_id]);

        $form = $this->createForm(ExamType::class, $grade, array('form' => 'candidate_result', 'data_class' => GradeDan::class, 'action' => $this->generateUrl('exam_candidate_detail_update', array('exam_id' => $exam_id, 'member_id' => $member_id, 'grade_id' => $grade_id)), 'method' => 'POST'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            if ($grade->getGradeDanStatus() == 3)
            {
                $member->setMemberLastGradeDan($grade);

                $grade_aikikai = $this->getDoctrine()->getRepository(GradeDan::class)->findOneBy(['grade_dan_member' => $member_id, 'grade_dan_exam' => $exam_id, 'grade_dan_status' => 5]);

                if ($grade_aikikai != null)
                {
                    $entityManager->remove($grade_aikikai);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('exam_detail', array('exam_id' => $exam_id));
        }

        return $this->render('Exam/candidate_detail.html.twig', array('exam_id' => $exam_id, 'member' => $member, 'form' => $form->createView()));
    }

    /**
     * @Route("/session-examen/{exam_id<\d+>}/candidat/{member_id<\d+>}/grade/{grade_id<\d+>}/ajouter_aikikai", name="exam_candidate_add_aikikai")
     */
    public function candidate_add_aikikai(Request $request, int $exam_id, int $member_id, int $grade_id)
    {
        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

        $grade = $this->getDoctrine()->getRepository(GradeDan::class)->findOneBy(['grade_dan_id' => $grade_id]);

        $grade_aikikai = new GradeDan();

        $grade_aikikai->setGradeDanRank($grade->getGradeDanRank() + 1);
        $grade_aikikai->setGradeDanStatus($grade->getGradeDanStatus() + 1);
        $grade_aikikai->setGradeDanClub($grade->getGradeDanClub());
        $grade_aikikai->setGradeDanExam($grade->getGradeDanExam());
        $grade_aikikai->setGradeDanMember($grade->getGradeDanMember());

        $form = $this->createForm(ExamType::class, $grade_aikikai, array('form' => 'candidate_aikikai', 'data_class' => GradeDan::class, 'action' => $this->generateUrl('exam_candidate_add_aikikai', array('exam_id' => $exam_id, 'member_id' => $member_id, 'grade_id' => $grade_id)), 'method' => 'POST'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $member->setMemberLastGradeDan($grade_aikikai);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($grade_aikikai);
            $entityManager->flush();

            return $this->redirectToRoute('exam_detail', array('exam_id' => $exam_id));
        }

        return $this->render('Exam/candidate_detail.html.twig', array('exam_id' => $exam_id, 'member' => $member, 'grade' => $grade, 'form' => $form->createView()));

    }

    /**
     * @Route("/session-examen/{exam_id<\d+>}/candidat/{member_id<\d+>}/grade/{grade_id<\d+>}/detail_aikikai", name="exam_candidate_detail_aikikai")
     */
    public function candidate_detail_aikikai(Request $request, int $exam_id, int $member_id, int $grade_id)
    {
        $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

        $grade = $this->getDoctrine()->getRepository(GradeDan::class)->findOneBy(['grade_dan_id' => $grade_id]);

        $form = $this->createForm(ExamType::class, $grade, array('form' => 'candidate_aikikai', 'data_class' => GradeDan::class, 'action' => $this->generateUrl('exam_candidate_detail_aikikai', array('exam_id' => $exam_id, 'member_id' => $member_id, 'grade_id' => $grade_id)), 'method' => 'POST'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $member->setMemberLastGradeDan($grade);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('exam_detail', array('exam_id' => $exam_id));
        }

        return $this->render('Exam/candidate_detail.html.twig', array('exam_id' => $exam_id, 'member' => $member, 'form' => $form->createView()));
    }
}
