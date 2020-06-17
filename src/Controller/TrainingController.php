<?php
// src/Controller/TrainingController.php
namespace App\Controller;

use App\Entity\Member;
use App\Entity\Training;
use App\Entity\TrainingAttendance;
use App\Entity\TrainingSession;

use App\Form\TrainingType;

use DateTime;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/stage", name="training_")
 *
 * @IsGranted("ROLE_STAGES")
 */
class TrainingController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function stageIndex()
    {
        $trainings = $this->getDoctrine()->getRepository(Training::class)->getActiveTrainings(4);

        return $this->render('Training/training_index.html.twig', array('trainings' => count($trainings) == 0 ? null : $trainings));
    }

    /**
     * @Route("/{training<\d+>}/ajouter-pratiquant", name="attendance_add")
     *
     * @param Request $request
     * @param Training $training
     * @return RedirectResponse|Response
     */
    public function attendanceAdd(Request $request, Training $training)
    {
        $choices = $this->getDoctrine()->getRepository(TrainingSession::class)->findBy(['training' => $training->getTrainingId()], ['training_session_date' => 'ASC', 'training_session_starting_hour' => 'ASC', 'training_session_duration' => 'ASC']);

        $form = $this->createForm(TrainingType::class, null, array('form' => 'attendance_add', 'data_class' => null, 'choices' => $choices));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $sessions = $form->get('TrainingAttendanceSession')->getData();

            $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $form->get('TrainingAttendanceId')->getData()]);

            $unique = microtime();

            $i = 0;

            foreach ($sessions as $session)
            {
                $attendance[$i] = new TrainingAttendance();

                $attendance[$i]->setTraining($training);
                /** @var Member $member */
                $attendance[$i]->setTrainingAttendanceMember($member);
                $attendance[$i]->setTrainingAttendanceUnique($unique);
                $attendance[$i]->setTrainingAttendanceSession($session);

                if ($i == 0)
                {
                    $attendance[$i]->setTrainingAttendancePayment($form->get('TrainingAttendancePayment')->getData());
                    $attendance[$i]->setTrainingAttendancePaymentType($form->get('TrainingAttendancePaymentType')->getData());
                }

                $entityManager->persist($attendance[$i]);

                $i++;
            }

            $entityManager->flush();

            return $this->redirectToRoute('training_attendance_add', array('training' => $training->getTrainingId()));
        }

        $payments = $this->getDoctrine()->getRepository(TrainingAttendance::class)->getPayments($training->getTrainingId());

        $cash = 0; $card = 0;

        foreach ($payments as $payment)
        {
            if ($payment['Type'] == 1)
            {
                $cash = $cash + $payment['Payment'];
            }
            else
            {
                $card = $card + $payment['Payment'];
            }
        }

        $practitioners = $this->getDoctrine()->getRepository(TrainingAttendance::class)->getPractitioners($training->getTrainingId());

        $practitioners_sessions = $this->getDoctrine()->getRepository(TrainingAttendance::class)->getPractitionersSessions($training->getTrainingId());

        return $this->render('Training/attendance_add.html.twig', array('form' => $form->createView(), 'training' => $training, 'practitioners' => $practitioners, 'practitioners_sessions' => $practitioners_sessions, 'total_card' => $card, 'total_cash' => $cash, 'today' => new DateTime()));
    }

    /**
     * @Route("/{training<\d+>}/ajouter-pratiquant-non-afa", name="attendance_foreign_add")
     *
     * @param Request $request
     * @param Training $training
     * @return RedirectResponse|Response
     */
    public function attendanceForeignAdd(Request $request, Training $training)
    {
        $choices = $this->getDoctrine()->getRepository(TrainingSession::class)->findBy(['training' => $training->getTrainingId()], ['training_session_date' => 'ASC', 'training_session_starting_hour' => 'ASC']);

        $form = $this->createForm(TrainingType::class, null, array('form' => 'attendance_foreign_add', 'data_class' => null, 'choices' => $choices));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $sessions = $form->get('TrainingAttendanceSession')->getData();

            $unique = microtime();

            $i = 0;

            foreach ($sessions as $session)
            {
                $attendance[$i] = new TrainingAttendance();

                $attendance[$i]->setTraining($training);
                $attendance[$i]->setTrainingAttendanceName($form->get('TrainingAttendanceName')->getData());
                $attendance[$i]->setTrainingAttendanceSex($form->get('TrainingAttendanceSex')->getData());
                $attendance[$i]->setTrainingAttendanceCountry($form->get('TrainingAttendanceCountry')->getData());
                $attendance[$i]->setTrainingAttendanceUnique($unique);
                $attendance[$i]->setTrainingAttendanceSession($session);

                if ($i == 0)
                {
                    $attendance[$i]->setTrainingAttendancePayment($form->get('TrainingAttendancePayment')->getData());
                    $attendance[$i]->setTrainingAttendancePaymentType($form->get('TrainingAttendancePaymentType')->getData());
                }

                $entityManager->persist($attendance[$i]);

                $i++;
            }

            $entityManager->flush();

            return $this->redirectToRoute('training_attendance_foreign_add', array('training' => $training->getTrainingId()));
        }

        $payments = $this->getDoctrine()->getRepository(TrainingAttendance::class)->getPayments($training->getTrainingId());

        $cash = 0; $card = 0;

        foreach ($payments as $payment)
        {
            if ($payment['Type'] == 1)
            {
                $cash = $cash + $payment['Payment'];
            }
            else
            {
                $card = $card + $payment['Payment'];
            }
        }

        $practitioners = $this->getDoctrine()->getRepository(TrainingAttendance::class)->getForeignPractitioners($training->getTrainingId());

        $practitioners_sessions = $this->getDoctrine()->getRepository(TrainingAttendance::class)->getForeignPractitionersSessions($training->getTrainingId());

        return $this->render('Training/attendance_foreign_add.html.twig', array('form' => $form->createView(), 'training' => $training, 'practitioners' => $practitioners, 'practitioners_sessions' => $practitioners_sessions, 'total_card' => $card, 'total_cash' => $cash));
    }
}
