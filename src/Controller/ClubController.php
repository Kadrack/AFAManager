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

use Doctrine\ORM\EntityRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\Routing\Annotation\Route;

class ClubController extends AbstractController
{
    /**
     * @Route("/club/", name="club_index")
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
     * @Route("/club/creer", name="club_create")
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
     * @Route("/club/{club_number<\d+>}/detail_association", name="club_detail_association")
     */
    public function detailAssociation(Request $request, int $club_number)
    {
        $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

        $form = $this->createForm(ClubType::class, $club, array('form' => 'detail_association'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club_index');
        }

        return $this->render('Club/detail_association.html.twig', array('form' => $form->createView(), 'club' => $club, 'listData' => new ListData()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/detail_cours", name="club_detail_class")
     */
    public function detailClass(int $club_number)
    {
        $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

        $addresses = $club->getClubAddresses();

        $trainings = $this->getDoctrine()->getRepository(Training::class)->findBy(['training_club' => $club->getClubId()], ['training_day' => 'ASC', 'training_starting_hour' => 'ASC']);

        $afa_teachers = $this->getDoctrine()->getRepository(ClubTeacher::class)->getAFATeachers($club);

        $foreign_teachers = $this->getDoctrine()->getRepository(ClubTeacher::class)->getForeignTeachers($club);

        return $this->render('Club/detail_dojo.html.twig', array('club' => $club, 'addresses' => $addresses, 'trainings' => $trainings, 'afa_teachers' => $afa_teachers, 'foreign_teachers' => $foreign_teachers, 'listData' => new ListData()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/membres_actifs", name="club_active_members")
     */
    public function activeMembers(SessionInterface $session, int $club_number)
    {
        $session->set('origin', 'active');

        $today = new \DateTime('today');

        $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

        $members = $this->getDoctrine()->getRepository(Member::class)->getClubActiveMembers($club, $today->format('Y-m-d'));

        return $this->render('Club/members_active.html.twig', array('members' => $members, 'club' => $club, 'list' => new ListData()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/membres_inactifs", name="club_inactive_members")
     */
    public function inactiveMembers(SessionInterface $session, int $club_number)
    {
        $session->set('origin', 'inactive');

        $today = new \DateTime('today');

        $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

        $members = $this->getDoctrine()->getRepository(Member::class)->getClubInactiveMembers($club, $today->format('Y-m-d'));

        return $this->render('Club/members_inactive.html.twig', array('members' => $members == null ? null : $members, 'club' => $club));
    }

    /**
     * @Route("/club/{club_number<\d+>}/desaffilier", name="club_disaffiliate")
     */
    public function disaffiliate(Request $request, int $club_number)
    {
        $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

        $history = new ClubHistory();

        $form = $this->createForm(ClubType::class, $history, array('form' => 'history_entry', 'data_class' => ClubHistory::class));

        $form->get('ClubHistoryUpdate')->setData(new \DateTime('today'));

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
     * @Route("/club/{club_number<\d+>}/reaffilier", name="club_reassign")
     */
    public function reassign(Request $request, int $club_number)
    {
        $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

        $history = new ClubHistory();

        $form = $this->createForm(ClubType::class, $history, array('form' => 'history_entry', 'data_class' => ClubHistory::class));

        $form->get('ClubHistoryUpdate')->setData(new \DateTime('today'));

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
     * @Route("/club/{club_number<\d+>}/ajouter_adresse", name="club_address_add")
     */
    public function addressAdd(Request $request, int $club_number)
    {
        $form = $this->createForm(ClubType::class, new TrainingAddress(), array('form' => 'address_create', 'data_class' => TrainingAddress::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

            $address = $form->getData();

            $address->setTrainingAddressClub($club);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($address);
            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club_number' => $club->getClubNumber()));
        }

        return $this->render('Club/address_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/modifier_adresse/{address_id<\d+>}", name="club_address_update")
     */
    public function addressUpdate(Request $request, int $club_number, int $address_id)
    {
        $address = $this->getDoctrine()->getRepository(TrainingAddress::class)->findOneBy(['training_address_id' => $address_id]);

        $form = $this->createForm(ClubType::class, $address, array('form' => 'address_update', 'data_class' => TrainingAddress::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club_number' => $club->getClubNumber()));
        }

        return $this->render('Club/address_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/supprimer_adresse/{address_id<\d+>}", name="club_address_delete")
     */
    public function addressDelete(Request $request, int $club_number, int $address_id)
    {
        $address = $this->getDoctrine()->getRepository(TrainingAddress::class)->findOneBy(['training_address_id' => $address_id]);

        $form = $this->createForm(ClubType::class, $address, array('form' => 'address_delete', 'data_class' => TrainingAddress::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($address);
            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club_number' => $club->getClubNumber()));
        }

        return $this->render('Club/address_delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/ajouter_horaire", name="club_training_add")
     */
    public function trainingAdd(Request $request, int $club_number)
    {
        $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

        $form = $this->createForm(ClubType::class, new Training(), array('form' => 'training_create', 'data_class' => Training::class, 'choices' => $club->getClubAddresses()));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $training = $form->getData();

            $training->setTrainingClub($club);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($training);
            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club_number' => $club->getClubNumber()));
        }

        return $this->render('Club/training_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/modifier_horaire/{training_id<\d+>}", name="club_training_update")
     */
    public function trainingUpdate(Request $request, int $club_number, int $training_id)
    {
        $training = $this->getDoctrine()->getRepository(Training::class)->findOneBy(['training_id' => $training_id]);

        $form = $this->createForm(ClubType::class, $training, array('form' => 'training_update', 'data_class' => Training::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club_number' => $club->getClubNumber()));
        }

        return $this->render('Club/training_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/supprimer_horaire/{training_id<\d+>}", name="club_training_delete")
     */
    public function trainingDelete(Request $request, int $club_number, int $training_id)
    {
        $training = $this->getDoctrine()->getRepository(Training::class)->findOneBy(['training_id' => $training_id]);

        $form = $this->createForm(ClubType::class, $training, array('form' => 'training_delete', 'data_class' => Training::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($training);
            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club_number' => $club->getClubNumber()));
        }

        return $this->render('Club/training_delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/ajouter_professeur_afa", name="club_teacher_afa_add")
     */
    public function teacherAFAAdd(Request $request, int $club_number)
    {
        $form = $this->createForm(ClubType::class, new ClubTeacher(), array('form' => 'teacher_afa_create', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

            $teacher = $form->getData();

            $teacher->setClubTeacher($club);

            $member = $this->getDoctrine()->getRepository(Member::class)->findOneBy(['member_id' => $form->get('ClubTeacherMember')->getData()]);

            $teacher->setClubTeacherFirstname($member->getMemberFirstname());
            $teacher->setClubTeacherMember($member);
            $teacher->setClubTeacherName($member->getMemberName());

            if ($form->get('ClubTeacherTitle')->getData() == 1)
            {
                $club->setClubMainTeacher($teacher);
            }

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($teacher);
            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club_number' => $club->getClubNumber()));
        }

        return $this->render('Club/teacher_afa_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/modifier_professeur_afa/{teacher_id<\d+>}", name="club_teacher_afa_update")
     */
    public function teacherAFAUpdate(Request $request, int $club_number, int $teacher_id)
    {
        $teacher = $this->getDoctrine()->getRepository(ClubTeacher::class)->findOneBy(['club_teacher_id' => $teacher_id]);

        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_afa_update', 'data_class' => ClubTeacher::class));

        $form->get('ClubTeacherMember')->setData($teacher->getClubTeacherMember()->getMemberId());
        $form->get('ClubTeacherFirstname')->setData($teacher->getClubTeacherMember()->getMemberFirstname());
        $form->get('ClubTeacherName')->setData($teacher->getClubTeacherMember()->getMemberName());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

            if ($form->get('ClubTeacherTitle')->getData() == 1)
            {
                $club->setClubMainTeacher($teacher);
            }

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club_number' => $club->getClubNumber()));
        }

        return $this->render('Club/teacher_afa_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/supprimer_professeur_afa/{teacher_id<\d+>}", name="club_teacher_afa_delete")
     */
    public function teacherAFADelete(Request $request, int $club_number, int $teacher_id)
    {
        $teacher = $this->getDoctrine()->getRepository(ClubTeacher::class)->findOneBy(['club_teacher_id' => $teacher_id]);

        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_afa_delete', 'data_class' => ClubTeacher::class));

        $form->get('ClubTeacherMember')->setData($teacher->getClubTeacherMember()->getMemberId());
        $form->get('ClubTeacherFirstname')->setData($teacher->getClubTeacherMember()->getMemberFirstname());
        $form->get('ClubTeacherName')->setData($teacher->getClubTeacherMember()->getMemberName());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

            $entityManager = $this->getDoctrine()->getManager();

            if ($form->get('ClubTeacherTitle')->getData() == 1)
            {
                $main_teacher = $this->getDoctrine()->getRepository(ClubTeacher::class)->findOneBy(['club_teacher' => $club->getClubId(), 'club_teacher_title' => 1]);

                $club->setClubMainTeacher($main_teacher->getClubTeacherId() == $teacher_id ? null : $main_teacher);
            }

            $entityManager->remove($teacher);
            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club_number' => $club->getClubNumber()));
        }

        return $this->render('Club/teacher_afa_delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/ajouter_professeur_etranger", name="club_teacher_foreign_add")
     */
    public function teacherForeignAdd(Request $request, int $club_number)
    {
        $form = $this->createForm(ClubType::class, new ClubTeacher(), array('form' => 'teacher_foreign_create', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

            $teacher = $form->getData();

            $teacher->setClubTeacher($club);

            if ($form->get('ClubTeacherTitle')->getData() == 1)
            {
                $club->setClubMainTeacher($teacher);
            }

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($teacher);
            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club_number' => $club->getClubNumber()));
        }

        return $this->render('Club/teacher_afa_add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/modifier_professeur_etranger/{teacher_id<\d+>}", name="club_teacher_foreign_update")
     */
    public function teacherForeignUpdate(Request $request, int $club_number, int $teacher_id)
    {
        $teacher = $this->getDoctrine()->getRepository(ClubTeacher::class)->findOneBy(['club_teacher_id' => $teacher_id]);

        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_foreign_update', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

            if ($form->get('ClubTeacherTitle')->getData() == 1)
            {
                $club->setClubMainTeacher($teacher);
            }

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club_number' => $club->getClubNumber()));
        }

        return $this->render('Club/teacher_afa_update.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/supprimer_professeur_etranger/{teacher_id<\d+>}", name="club_teacher_foreign_delete")
     */
    public function teacherForeignDelete(Request $request, int $club_number, int $teacher_id)
    {
        $teacher = $this->getDoctrine()->getRepository(ClubTeacher::class)->findOneBy(['club_teacher_id' => $teacher_id]);

        $form = $this->createForm(ClubType::class, $teacher, array('form' => 'teacher_foreign_delete', 'data_class' => ClubTeacher::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

            $entityManager = $this->getDoctrine()->getManager();

            if ($form->get('ClubTeacherTitle')->getData() == 1)
            {
                $main_teacher = $this->getDoctrine()->getRepository(ClubTeacher::class)->findOneBy(['club_teacher' => $club->getClubId(), 'club_teacher_title' => 1]);

                $club->setClubMainTeacher($main_teacher->getClubTeacherId() == $teacher_id ? null : $main_teacher);
            }

            $entityManager->remove($teacher);
            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club_number' => $club->getClubNumber()));
        }

        return $this->render('Club/teacher_afa_delete.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/club/{club_number<\d+>}/contact", name="club_contact")
     */
    public function contact(Request $request, \Swift_Mailer $mailer, int $club_number)
    {
        $form = $this->createForm(EmailType::class, new Email());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $club = $this->getDoctrine()->getRepository(Club::class)->findOneBy(['club_number' => $club_number]);

            $email = $form->getData();
            
            $email->setEmailFrom('kadrack@gmail.com');

            $email->setEmailTo($club->getClubContactEmail());

            $entityManager = $this->getDoctrine()->getManager();

            $message = (new \Swift_Message($email->getEmailTitle()))
                ->setFrom($email->getEmailFrom())
                ->setTo($email->getEmailTo())
                ->setBody($email->getEmailBody());

            $mailer->send($message);

            $entityManager->persist($email);
            $entityManager->flush();

            return $this->redirectToRoute('club_detail_class', array('club_number' => $club->getClubNumber()));
        }

        return $this->render('Common/email.html.twig', array('form' => $form->createView()));
    }
}
