<?php
// src/Entity/ClubLesson.php
namespace App\Entity;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ClubLesson
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_club_lesson")
 * @ORM\Entity(repositoryClass="App\Repository\ClubLessonRepository")
 */
class ClubLesson
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $club_lesson_id;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $club_lesson_day;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="time", nullable=true)
     */
    private ?DateTime $club_lesson_starting_hour;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="time", nullable=true)
     */
    private ?DateTime $club_lesson_ending_hour;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private int $club_lesson_type;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $club_lesson_comment;

    /**
     * @var ClubDojo|null
     *
     * @ORM\ManyToOne(targetEntity="ClubDojo", inversedBy="club_dojo_lessons", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="club_lesson_join_club_dojo", referencedColumnName="club_dojo_id")
     */
    private ?ClubDojo $club_lesson_dojo;

    /**
     * @var Club|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_lessons", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="club_lesson_join_club", referencedColumnName="club_id")
     */
    private ?Club $club_lesson_club;

    /**
     * @return int
     */
    public function getClubLessonId(): int
    {
        return $this->club_lesson_id;
    }

    /**
     * @param int $club_lesson_id
     * @return $this
     */
    public function setClubLessonId(int $club_lesson_id): self
    {
        $this->club_lesson_id = $club_lesson_id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubLessonDay(): ?int
    {
        return $this->club_lesson_day;
    }

    /**
     * @param int|null $club_lesson_day
     * @return $this
     */
    public function setClubLessonDay(?int $club_lesson_day): self
    {
        $this->club_lesson_day = $club_lesson_day;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getClubLessonStartingHour(): ?DateTime
    {
        return $this->club_lesson_starting_hour;
    }

    /**
     * @param DateTime|null $club_lesson_starting_hour
     * @return $this
     */
    public function setClubLessonStartingHour(?DateTime $club_lesson_starting_hour): self
    {
        $this->club_lesson_starting_hour = $club_lesson_starting_hour;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getClubLessonEndingHour(): ?DateTime
    {
        return $this->club_lesson_ending_hour;
    }

    /**
     * @param DateTime|null $club_lesson_ending_hour
     * @return $this
     */
    public function setClubLessonEndingHour(?DateTime $club_lesson_ending_hour): self
    {
        $this->club_lesson_ending_hour = $club_lesson_ending_hour;

        return $this;
    }

    /**
     * @return int
     */
    public function getClubLessonType(): int
    {
        return $this->club_lesson_type;
    }

    /**
     * @param int $club_lesson_type
     * @return $this
     */
    public function setClubLessonType(int $club_lesson_type): self
    {
        $this->club_lesson_type = $club_lesson_type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubLessonComment(): ?string
    {
        return $this->club_lesson_comment;
    }

    /**
     * @param string|null $club_lesson_comment
     * @return $this
     */
    public function setClubLessonComment(?string $club_lesson_comment): self
    {
        $this->club_lesson_comment = $club_lesson_comment;

        return $this;
    }

    /**
     * @return ClubDojo|null
     */
    public function getClubLessonDojo(): ?ClubDojo
    {
        return $this->club_lesson_dojo;
    }

    /**
     * @param ClubDojo|null $club_lesson_dojo
     * @return $this
     */
    public function setClubLessonDojo(?ClubDojo $club_lesson_dojo): self
    {
        $this->club_lesson_dojo = $club_lesson_dojo;

        return $this;
    }

    /**
     * @return Club|null
     */
    public function getClubLessonClub(): ?Club
    {
        return $this->club_lesson_club;
    }

    /**
     * @param Club|null $club_lesson_club
     * @return $this
     */
    public function setClubLessonClub(?Club $club_lesson_club): self
    {
        $this->club_lesson_club = $club_lesson_club;

        return $this;
    }
}
