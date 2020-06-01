<?php
// src/Service/ClubTools.php
namespace App\Service;

use App\Entity\Club;

use App\Entity\ClubTeacher;
use App\Entity\Training;
use App\Entity\TrainingAddress;
use DateTime;

use Doctrine\Persistence\ObjectManager;

/**
 * Class Tools
 * @package App\Service
 */
class ClubTools
{
    private $club;

    private $lessons;

    private $em;

    /**
     * MemberTools constructor.
     * @param ObjectManager $entityManager
     * @param Club $club
     */
    public function __construct(ObjectManager $entityManager, Club $club)
    {
        $this->em = $entityManager;

        $this->club = $club;

        $this->lessons = null;
    }

    public function getLessonsDetails(): ?array
    {
        if ($this->lessons != null)
        {
            return $this->lessons;
        }

        $dojos = $this->em->getRepository(TrainingAddress::class)->findBy(['training_address_club' => $this->club->getClubId()]);

        $trainings = $this->em->getRepository(Training::class)->findBy(['training_club' => $this->club->getClubId()], ['training_day' => 'ASC', 'training_starting_hour' => 'ASC']);

        $afa_teachers = $this->em->getRepository(ClubTeacher::class)->getAFATeachers($this->club);

        $foreign_teachers = $this->em->getRepository(ClubTeacher::class)->getForeignTeachers($this->club);

        $this->lessons = array('Dojos' => $dojos, 'Trainings' => $trainings, 'AFA_teachers' => $afa_teachers, 'Foreign_teachers' => $foreign_teachers);

        return $this->lessons;
    }
}
