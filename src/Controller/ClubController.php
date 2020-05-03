<?php
// src/Controller/ClubController.php
namespace App\Controller;

use App\Entity\Club;
use App\Entity\ClubHistory;
use App\Entity\ClubTeacher;
use App\Entity\Email;
use App\Entity\Member;
use App\Entity\Training;
use App\Entity\TrainingAddress;

use App\Form\ClubType;
use App\Form\EmailType;

use App\Service\ListData;

use DateTime;

use Swift_Mailer;
use Swift_Message;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/club", name="club_")
 */
class ClubController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
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

        return $this->render('Club/index.html.twig', array('active_clubs' => $active_clubs, 'inactive_clubs' => $inactive_list));
    }

    /**
     * @Route("/creer", name="create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function create(Request $request)
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

            return $this->redirectToRoute('club_index');
        }
        
        return $this->render('Club/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{club<\d+>}/detail_association", name="detail_association")
     * @param Request $request
     * @param Club $club
     * @return RedirectResponse|Response
     */
    public function detailAssociation(Request $request, Club $club)
    {
        $form = $this->createForm(ClubType::class, $club, array('form' => 'detail_association'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club_index');
        }

        return $this->render('Club/detail_association.html.twig', array('form' => $form->createView(), 'club' => $club));
    }

    /**
     * @Route("/{club<\d+>}/detail_cours", name="detail_class")
     * @param Club $club
     * @return Response
     */
    public function detailClass(Club $club)
    {
        $addresses = $club->getClubAddresses();

        $trainings = $this->getDoctrine()->getRepository(Training::class)->findBy(['training_club' => $club->getClubId()], ['training_day' => 'ASC', 'training_starting_hour' => 'ASC']);

        $afa_teachers = $this->getDoctrine()->getRepository(ClubTeacher::class)->getAFATeachers($club);

        $foreign_teachers = $this->getDoctrine()->getRepository(ClubTeacher::class)->getForeignTeachers($club);

        return $this->render('Club/detail_dojo.html.twig', array('club' => $club, 'addresses' => $addresses, 'trainings' => $trainings, 'afa_teachers' => $afa_teachers, 'foreign_teachers' => $foreign_teachers, 'listData' => new ListData()));
    }

    /**
     * @Route("/{club<\d+>}/membres_actifs", name="active_members")
     * @param Club $club
     * @return Response
     */
    public function activeMembers(Club $club)
    {
        $today = new DateTime('today');

        $members = $this->getDoctrine()->getRepository(Member::class)->getClubActiveMembers($club, $today->format('Y-m-d'));

        return $this->render('Club/members_active.html.twig', array('members' => $members, 'club' => $club));
    }

    /**
     * @Route("/{club<\d+>}/membres_inactifs", name="inactive_members")
     * @param Club $club
     * @return Response
     */
    public function inactiveMembers(Club $club)
    {
        $today = new DateTime('today');

        $members = $this->getDoctrine()->getRepository(Member::class)->getClubInactiveMembers($club, $today->format('Y-m-d'));

        return $this->render('Club/members_inactive.html.twig', array('members' => $members == null ? null : $members, 'club' => $club));
    }

    /**
     * @Route("/{club<\d+>}/desaffilier", name="disaffiliate")
     * @param Request $request
     * @param Club $club
     * @return RedirectResponse|Response
     */
    public function disaffiliate(Request $request, Club $club)
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

            return $this->redirectToRoute('club_index');
        }

        return $this->render('Club/history_entry.html.twig', array('form' => $form->createView(), 'club' => $club, 'listData' => new ListData()));
    }

    /**
     * @Route("/{club<\d+>}/reaffilier", name="reassign")
     * @param Request $request
     * @param Club $club
     * @return RedirectResponse|Response
     */
    public function reassign(Request $request, Club $club)
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

        return $this->render('Club/history_entry.html.twig', array('form' => $form->createView(), 'club' => $club, 'listData' => new ListData()));
    }

    /**
     * @Route("/{club<\d+>}/ajouter_adresse", name="address_add")
     * @param Request $request
     * @param Club $club
     * @return RedirectResponse|Response
     */
    public function addressAdd(Request $request, Club $club)
    {
        $form = $this->createForm(ClubType::class, new TrainingAddress(), array('form' => 'address_create', 'data_class' => TrainingAddress::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $address = $form->getData();

            if ($address->getTrainingAddressDEA() == false)
            {
                $address->setTrainingAddressDEAFormation(null);
            }

            $address->setTrainingAddressClub($club);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($address);
            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club' => $club->getClubId()));
        }

        return $this->render('Club/address_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{club<\d+>}/modifier_adresse/{address<\d+>}", name="address_update")
     * @param Request $request
     * @param Club $club
     * @param TrainingAddress $address
     * @return RedirectResponse|Response
     */
    public function addressUpdate(Request $request, Club $club, TrainingAddress $address)
    {
        $form = $this->createForm(ClubType::class, $address, array('form' => 'address_update', 'data_class' => TrainingAddress::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club' => $club->getClubId()));
        }

        return $this->render('Club/address_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{club<\d+>}/supprimer_adresse/{address<\d+>}", name="address_delete")
     * @param Request $request
     * @param Club $club
     * @param TrainingAddress $address
     * @return RedirectResponse|Response
     */
    public function addressDelete(Request $request, Club $club, TrainingAddress $address)
    {
        $form = $this->createForm(ClubType::class, $address, array('form' => 'address_delete', 'data_class' => TrainingAddress::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($address);
            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club' => $club->getClubId()));
        }

        return $this->render('Club/address_delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{club<\d+>}/ajouter_horaire", name="training_add")
     * @param Request $request
     * @param Club $club
     * @return RedirectResponse|Response
     */
    public function trainingAdd(Request $request, Club $club)
    {
        $form = $this->createForm(ClubType::class, new Training(), array('form' => 'training_create', 'data_class' => Training::class, 'choices' => $club->getClubAddresses()));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $training = $form->getData();

            $training->setTrainingClub($club);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($training);
            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club' => $club->getClubId()));
        }

        return $this->render('Club/training_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{club<\d+>}/modifier_horaire/{training<\d+>}", name="training_update")
     * @param Request $request
     * @param Club $club
     * @param Training $training
     * @return RedirectResponse|Response
     */
    public function trainingUpdate(Request $request, Club $club, Training $training)
    {
        $form = $this->createForm(ClubType::class, $training, array('form' => 'training_update', 'data_class' => Training::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club' => $club->getClubId()));
        }

        return $this->render('Club/training_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{club<\d+>}/supprimer_horaire/{training<\d+>}", name="training_delete")
     * @param Request $request
     * @param Club $club
     * @param Training $training
     * @return RedirectResponse|Response
     */
    public function trainingDelete(Request $request, Club $club, Training $training)
    {
        $form = $this->createForm(ClubType::class, $training, array('form' => 'training_delete', 'data_class' => Training::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($training);
            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club' => $club->getClubId()));
        }

        return $this->render('Club/training_delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{club<\d+>}/ajouter_professeur_afa", name="teacher_afa_add")
     * @param Request $request
     * @param Club $club
     * @return RedirectResponse|Response
     */
    public function teacherAFAAdd(Request $request, Club $club)
    {
        $form = $this->createForm(ClubType::class, new ClubTeacher(), array('form' => 'teacher_afa_create', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $teacher = $form->getData();

            $teacher->setClubTeacher($club);

            $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $form->get('ClubTeacherMember')->getData()]);
            
            if ($member != null)
            {
                $teacher->setClubTeacherFirstname($member->getMemberFirstname());
                $teacher->setClubTeacherMember($member);
                $teacher->setClubTeacherName($member->getMemberName());

                if ($form->get('ClubTeacherTitle')->getData() == 1) {
                    $club->setClubMainTeacher($teacher);
                }

                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->persist($teacher);
                $entityManager->flush();
            }

            return $this->redirectToRoute('club_detail_class', array('club' => $club->getClubId()));
        }

        return $this->render('Club/teacher_afa_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{club<\d+>}/modifier_professeur_afa/{teacher<\d+>}", name="teacher_afa_update")
     * @param Request $request
     * @param Club $club
     * @param ClubTeacher $teacher
     * @return RedirectResponse|Response
     */
    public function teacherAFAUpdate(Request $request, Club $club, ClubTeacher $teacher)
    {
        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_afa_update', 'data_class' => ClubTeacher::class));

        $form->get('ClubTeacherMember')->setData($teacher->getClubTeacherMember()->getMemberId());
        $form->get('ClubTeacherFirstname')->setData($teacher->getClubTeacherMember()->getMemberFirstname());
        $form->get('ClubTeacherName')->setData($teacher->getClubTeacherMember()->getMemberName());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($form->get('ClubTeacherTitle')->getData() == 1)
            {
                $club->setClubMainTeacher($teacher);
            }

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club' => $club->getClubId()));
        }

        return $this->render('Club/teacher_afa_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{club<\d+>}/supprimer_professeur_afa/{teacher<\d+>}", name="teacher_afa_delete")
     * @param Request $request
     * @param Club $club
     * @param ClubTeacher $teacher
     * @return RedirectResponse|Response
     */
    public function teacherAFADelete(Request $request, Club $club, ClubTeacher $teacher)
    {
        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_afa_delete', 'data_class' => ClubTeacher::class));

        $form->get('ClubTeacherMember')->setData($teacher->getClubTeacherMember()->getMemberId());
        $form->get('ClubTeacherFirstname')->setData($teacher->getClubTeacherMember()->getMemberFirstname());
        $form->get('ClubTeacherName')->setData($teacher->getClubTeacherMember()->getMemberName());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            if ($form->get('ClubTeacherTitle')->getData() == 1)
            {
                $main_teacher = $this->getDoctrine()->getRepository(ClubTeacher::class)->findOneBy(['club_teacher' => $club->getClubId(), 'club_teacher_title' => 1]);

                $club->setClubMainTeacher($main_teacher->getClubTeacherId() == $teacher->getClubTeacherId() ? null : $main_teacher);
            }

            $entityManager->remove($teacher);
            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club' => $club->getClubId()));
        }

        return $this->render('Club/teacher_afa_delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{club<\d+>}/ajouter_professeur_etranger", name="teacher_foreign_add")
     * @param Request $request
     * @param Club $club
     * @return RedirectResponse|Response
     */
    public function teacherForeignAdd(Request $request, Club $club)
    {
        $form = $this->createForm(ClubType::class, new ClubTeacher(), array('form' => 'teacher_foreign_create', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $teacher = $form->getData();

            $teacher->setClubTeacher($club);

            if ($form->get('ClubTeacherTitle')->getData() == 1)
            {
                $club->setClubMainTeacher($teacher);
            }

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($teacher);
            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club' => $club->getClubId()));
        }

        return $this->render('Club/teacher_afa_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{club<\d+>}/modifier_professeur_etranger/{teacher<\d+>}", name="teacher_foreign_update")
     * @param Request $request
     * @param Club $club
     * @param ClubTeacher $teacher
     * @return RedirectResponse|Response
     */
    public function teacherForeignUpdate(Request $request, Club $club, ClubTeacher $teacher)
    {
        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_foreign_update', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($form->get('ClubTeacherTitle')->getData() == 1)
            {
                $club->setClubMainTeacher($teacher);
            }

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club' => $club->getClubId()));
        }

        return $this->render('Club/teacher_afa_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{club<\d+>}/supprimer_professeur_etranger/{teacher<\d+>}", name="teacher_foreign_delete")
     * @param Request $request
     * @param Club $club
     * @param ClubTeacher $teacher
     * @return RedirectResponse|Response
     */
    public function teacherForeignDelete(Request $request, Club $club, ClubTeacher $teacher)
    {
        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_foreign_delete', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            if ($form->get('ClubTeacherTitle')->getData() == 1)
            {
                $main_teacher = $this->getDoctrine()->getRepository(ClubTeacher::class)->findOneBy(['club_teacher' => $club->getClubId(), 'club_teacher_title' => 1]);

                $club->setClubMainTeacher($main_teacher->getClubTeacherId() == $teacher->getClubTeacherId() ? null : $main_teacher);
            }

            $entityManager->remove($teacher);
            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club' => $club->getClubId()));
        }

        return $this->render('Club/teacher_afa_delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/{club<\d+>}/contact", name="contact")
     * @param Request $request
     * @param Swift_Mailer $mailer
     * @param Club $club
     * @return RedirectResponse|Response
     */
    public function contact(Request $request, Swift_Mailer $mailer, Club $club)
    {
        $form = $this->createForm(EmailType::class, new Email());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $email = $form->getData();
            
            $email->setEmailFrom('kadrack@gmail.com');

            $email->setEmailTo($club->getClubContactEmail());

            $entityManager = $this->getDoctrine()->getManager();

            $message = (new Swift_Message($email->getEmailTitle()))
                ->setFrom($email->getEmailFrom())
                ->setTo($email->getEmailTo())
                ->setBody($email->getEmailBody());

            $mailer->send($message);

            $entityManager->persist($email);
            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club' => $club->getClubId()));
        }

        return $this->render('Common/email.html.twig', array('form' => $form->createView()));
    }
}
