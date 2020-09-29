<?php
// src/Service/ClubTools.php
namespace App\Service;

use App\Entity\Club;

use App\Entity\ClubTeacher;
use App\Entity\Training;
use App\Entity\TrainingAddress;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

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
     * @param ObjectManager $entityManager
     * @param Club $club
     */
    public function __construct(ObjectManager $entityManager, Club $club)
    {
        $this->em = $entityManager;

        $this->club = $club;
    }

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
}
