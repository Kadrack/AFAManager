<?php
// src/Controller/TrainingController.php
namespace App\Controller;

use App\Entity\Member;
use App\Entity\Training;
use App\Entity\TrainingAttendance;
use App\Entity\TrainingSession;

use App\Form\TrainingType;

use App\Service\ListData;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/stage", name="training_")
 */
class TrainingController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $trainings = $this->getDoctrine()->getRepository(Training::class)->getActiveTrainings();

        return $this->render('Training/training_index.html.twig', array('trainings' => count($trainings) == 0 ? null : $trainings, 'listData' => new ListData()));
    }

    /**
     * @Route("/creer", name="create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function create(Request $request)
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

        return $this->render('Training/training_create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{training<\d+>}/detail", name="detail")
     * @param Training $training
     * @return Response
     */
    public function detail(Training $training)
    {
        $sessions = $this->getDoctrine()->getRepository(TrainingSession::class)->getTrainingSessions($training->getTrainingId());

        return $this->render('Training/training_detail.html.twig', array('training' => $training, 'sessions' => $sessions, 'listData' => new ListData()));
    }

    /**
     * @Route("/{training<\d+>}/modifier", name="update")
     * @param Request $request
     * @param Training $training
     * @return RedirectResponse|Response
     */
    public function update(Request $request, Training $training)
    {
        $form = $this->createForm(TrainingType::class, $training, array('form' => 'training_update'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('training_index');
        }

        return $this->render('Training/training_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{training<\d+>}/supprimer", name="delete")
     * @param Request $request
     * @param Training $training
     * @return RedirectResponse|Response
     */
    public function delete(Request $request, Training $training)
    {
        $form = $this->createForm(TrainingType::class, $training, array('form' => 'training_delete'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($training);
            $entityManager->flush();

            return $this->redirectToRoute('training_index');
        }

        return $this->render('Training/training_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{training<\d+>}/ajouter-session", name="session_add")
     * @param Request $request
     * @param Training $training
     * @return RedirectResponse|Response
     */
    public function sessionAdd(Request $request, Training $training)
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

            return $this->redirectToRoute('training_detail', array('training' => $training->getTrainingId()));
        }

        return $this->render('Training/session_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{training<\d+>}/modifier-session/{session<\d+>}", name="session_update")
     * @param Request $request
     * @param Training $training
     * @param TrainingSession $session
     * @return RedirectResponse|Response
     */
    public function sessionUpdate(Request $request, Training $training, TrainingSession $session)
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

            return $this->redirectToRoute('training_detail', array('training' => $training->getTrainingId()));
        }

        return $this->render('Training/session_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{training<\d+>}/supprimer-session/{session<\d+>}", name="session_delete")
     * @param Request $request
     * @param Training $training
     * @param TrainingSession $session
     * @return RedirectResponse|Response
     */
    public function sessionDelete(Request $request, Training $training, TrainingSession $session)
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
                return $this->redirectToRoute('training_detail', array('training' => $training->getTrainingId()));
            }

        }

        return $this->render('Training/session_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{training<\d+>}/ajouter-pratiquant", name="attendance_add")
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

        return $this->render('Training/attendance_add.html.twig', array('form' => $form->createView(), 'training' => $training, 'practitioners' => $practitioners, 'practitioners_sessions' => $practitioners_sessions, 'listData' => new ListData(), 'total_card' => $card, 'total_cash' => $cash, 'today' => new \DateTime()));
    }

    /**
     * @Route("/{training<\d+>}/ajouter-pratiquant-non-afa", name="attendance_foreign_add")
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

        return $this->render('Training/attendance_foreign_add.html.twig', array('form' => $form->createView(), 'training' => $training, 'practitioners' => $practitioners, 'practitioners_sessions' => $practitioners_sessions, 'listData' => new ListData(), 'total_card' => $card, 'total_cash' => $cash));
    }
}
