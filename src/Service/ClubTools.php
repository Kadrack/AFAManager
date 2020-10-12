<?php
// src/Service/ClubTools.php
namespace App\Service;

use App\Entity\Club;
use App\Entity\ClubTeacher;
use App\Entity\Member;
use App\Entity\Training;
use App\Entity\TrainingAddress;
use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class Tools
 * @package App\Service
 */
class ClubTools
{
    private $club;

    private $lessons;

    private $managers;

    private $em;

    /**
     * ClubTools constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @return Club
     */
    public function getClub()
    {
        return $this->club;
    }

    /**
     * @param Club $club
     * @return Club
     */
    public function setClub(Club $club)
    {
        $this->club = $club;

        return $this->club;
    }

    /**
     * @return array|null
     */
    public function getLessonsDetails(): ?array
    {
        if ($this->lessons !== null)
        {
            return $this->lessons;
        }

        $dojos = $this->em->getRepository(TrainingAddress::class)->findBy(['training_address_club' => $this->club->getClubId()]);

        $trainings = $this->em->getRepository(Training::class)->findBy(['training_club' => $this->club->getClubId(), 'training_type' => array(1, 2, 3)], ['training_day' => 'ASC', 'training_starting_hour' => 'ASC']);

        $afa_teachers = $this->em->getRepository(ClubTeacher::class)->getAFATeachers($this->club);

        $foreign_teachers = $this->em->getRepository(ClubTeacher::class)->getForeignTeachers($this->club);

        $this->lessons = array('Dojos' => $dojos, 'Trainings' => $trainings, 'AFA_teachers' => $afa_teachers, 'Foreign_teachers' => $foreign_teachers);

        return $this->lessons;
    }

    /**
     * @return array|null
     */
    public function getManagerList(): ?array
    {
        if ($this->managers !== null)
        {
            return $this->managers;
        }

        $managers = $this->em->getRepository(User::class)->findBy(['user_club' => $this->club]);

        $this->managers = $managers;

        return $this->managers;
    }

    /**
     * @param TrainingAddress $trainingAddress
     * @param string|null $action
     * @return bool
     */
    public function dojoAddress(TrainingAddress $trainingAddress, ?string $action)
    {
        if ($action == 'Add')
        {
            if ($trainingAddress->getTrainingAddressDEA() == false)
            {
                $trainingAddress->setTrainingAddressDEAFormation(null);
            }

            $trainingAddress->setTrainingAddressClub($this->getClub());

            $this->em->persist($trainingAddress);
        }

        if ($action == 'Delete')
        {
            $this->em->remove($trainingAddress);
        }

        $this->em->flush();

        return true;
    }

    /**
     * @param Training $training
     * @param string|null $action
     * @return bool
     */
    public function dojoTraining(Training $training, ?string $action)
    {
        if ($action == 'Add')
        {
            $training->setTrainingClub($this->getClub());

            $this->em->persist($training);
        }

        if ($action == 'Delete')
        {
            $this->em->remove($training);
        }

        $this->em->flush();

        return true;
    }

    /**
     * @param ClubTeacher $clubTeacher
     * @param string|null $action
     * @param int|null $member_id
     * @return bool
     */
    public function dojoTeacher(ClubTeacher $clubTeacher, ?string $action, ?int $member_id = null)
    {
        if ($action == 'Add')
        {
            $clubTeacher->setClubTeacher($this->getClub());

            $member = $this->em->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

            if ($member != null)
            {
                $clubTeacher->setClubTeacherMember($member);

                $this->em->persist($clubTeacher);
            }
        }

        if ($action == 'Delete')
        {
            if ($clubTeacher->getClubTeacherTitle() == 1)
            {
                $main_teacher = $this->em->getRepository(ClubTeacher::class)->findOneBy(['club_teacher' => $this->club->getClubId(), 'club_teacher_title' => 1]);

                /** @var ClubTeacher $main_teacher */
                $this->club->setClubMainTeacher($main_teacher->getClubTeacherId() == $clubTeacher->getClubTeacherId() ? null : $main_teacher);
            }

            $this->em->remove($clubTeacher);
        }

        if ($clubTeacher->getClubTeacherTitle() == 1)
        {
            $this->club->setClubMainTeacher($clubTeacher);
        }

        $this->em->flush();

        return true;
    }

    /**
     * @return bool
     */
    public function associationDetails()
    {
        $this->em->flush();

        return true;
    }
}
