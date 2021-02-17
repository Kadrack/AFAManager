<?php
// src/Service/ClubTools.php
namespace App\Service;

use App\Entity\Club;
use App\Entity\ClubDojo;
use App\Entity\ClubLesson;
use App\Entity\ClubTeacher;
use App\Entity\Member;
use App\Entity\UserAccess;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class Tools
 * @package App\Service
 */
class ClubTools
{
    private Club $club;

    private ?array $lessons;

    private ?array $managers;

    private EntityManagerInterface $em;

    /**
     * ClubTools constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;

        $this->lessons  = null;
        $this->managers = null;
    }

    /**
     * @return Club
     */
    public function getClub(): Club
    {
        return $this->club;
    }

    /**
     * @param Club $club
     * @return Club
     */
    public function setClub(Club $club): Club
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

        $dojos = $this->em->getRepository(ClubDojo::class)->findBy(['club_dojo_club' => $this->club->getClubId()]);

        $lessons = $this->em->getRepository(ClubLesson::class)->findBy(['club_lesson_club' => $this->club->getClubId(), 'club_lesson_type' => array(1, 2, 3)], ['club_lesson_day' => 'ASC', 'club_lesson_starting_hour' => 'ASC']);

        $afa_teachers = $this->em->getRepository(ClubTeacher::class)->getAFATeachers($this->club);

        foreach ($afa_teachers as $teacher)
        {
            if (!isset($teachers[$teacher['Licence']]))
            {
                $teachers[$teacher['Licence']] = $teacher;

                $teachers[$teacher['Licence']]['GradeTitleAdeps']   = null;
                $teachers[$teacher['Licence']]['GradeTitleAikikai'] = null;
            }

            if (($teacher['GradeTitleAikikai'] <= 3) && ($teachers[$teacher['Licence']]['GradeTitleAikikai'] < $teacher['GradeTitleAikikai']))
            {
                $teachers[$teacher['Licence']]['GradeTitleAikikai'] = $teacher['GradeTitleAikikai'];
            }

            if ((($teacher['GradeTitleAdeps'] >= 4 ) && ($teacher['GradeTitleAdeps'] <= 9 )) && ($teachers[$teacher['Licence']]['GradeTitleAdeps'] < $teacher['GradeTitleAdeps']))
            {
                $teachers[$teacher['Licence']]['GradeTitleAdeps'] = $teacher['GradeTitleAdeps'];
            }
        }

        $foreign_teachers = $this->em->getRepository(ClubTeacher::class)->getForeignTeachers($this->club);

        $this->lessons = array('Dojos' => $dojos, 'Lessons' => $lessons, 'AFA_teachers' => $teachers, 'Foreign_teachers' => $foreign_teachers);

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

        $managers = $this->em->getRepository(UserAccess::class)->findBy(['user_access_club' => $this->club, 'user_access_role' => 'ROLE_CLUB']);

        foreach ($managers as $manager)
        {
            $this->managers[] = $manager->getUserAccessUser();
        }

        return $this->managers;
    }

    /**
     * @param ClubDojo $clubDojo
     * @param string|null $action
     * @return bool
     */
    public function dojoAddress(ClubDojo $clubDojo, ?string $action = null): bool
    {
        if ($action == 'Add')
        {
            if ($clubDojo->getClubDojoDEA() == false)
            {
                $clubDojo->setClubDojoDEAFormation(null);
            }

            $clubDojo->setClubDojoClub($this->getClub());

            $this->em->persist($clubDojo);
        }

        if ($action == 'Delete')
        {
            $this->em->remove($clubDojo);
        }

        $this->em->flush();

        return true;
    }

    /**
     * @param ClubLesson $clubLesson
     * @param string|null $action
     * @return bool
     */
    public function dojoTraining(ClubLesson $clubLesson, ?string $action = null): bool
    {
        if ($action == 'Add')
        {
            $clubLesson->setClubLessonClub($this->getClub());

            $this->em->persist($clubLesson);
        }

        if ($action == 'Delete')
        {
            $this->em->remove($clubLesson);
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
    public function dojoTeacher(ClubTeacher $clubTeacher, ?string $action = null, ?int $member_id = null): bool
    {
        if ($action == 'Add')
        {
            $clubTeacher->setClubTeacher($this->getClub());

            if (!is_null($member_id))
            {
                $member = $this->em->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

                if ($member != null)
                {
                    $clubTeacher->setClubTeacherMember($member);
                }
            }

            $this->em->persist($clubTeacher);

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
    public function associationDetails(): bool
    {
        $this->em->flush();

        return true;
    }
}
