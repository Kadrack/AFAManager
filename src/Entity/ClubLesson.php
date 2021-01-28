<?php
// src/Entity/ClubLesson.php
namespace App\Entity;

use DateTimeInterface;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="afamanager_club_lesson")
 * @ORM\Entity(repositoryClass="App\Repository\ClubLessonRepository")
 */
class ClubLesson
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private ?int $club_lesson_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $club_lesson_day;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private ?DateTimeInterface $club_lesson_starting_hour;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private ?DateTimeInterface $club_lesson_ending_hour;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $club_lesson_type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $club_lesson_comment;

    /**
     * @ORM\ManyToOne(targetEntity="ClubDojo", inversedBy="club_dojos", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="club_lesson_join_club_dojo", referencedColumnName="club_dojo_id")
     */
    private ?ClubDojo $club_lesson_dojo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_lessons", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="club_lesson_join_club", referencedColumnName="club_id")
     */
    private ?Club $club_lesson;

    public function getClubLessonId(): ?int
    {
        return $this->club_lesson_id;
    }

    public function setClubLessonId(?int $club_lesson_id): self
    {
        $this->club_lesson_id = $club_lesson_id;

        return $this;
    }

    public function getClubLessonDay(): ?int
    {
        return $this->club_lesson_day;
    }

    public function setClubLessonDay(?int $club_lesson_day): self
    {
        $this->club_lesson_day = $club_lesson_day;

        return $this;
    }

    public function getClubLessonStartingHour(): ?DateTimeInterface
    {
        return $this->club_lesson_starting_hour;
    }

    public function setClubLessonStartingHour(?DateTimeInterface $club_lesson_starting_hour): self
    {
        $this->club_lesson_starting_hour = $club_lesson_starting_hour;

        return $this;
    }

    public function getClubLessonEndingHour(): ?DateTimeInterface
    {
        return $this->club_lesson_ending_hour;
    }

    public function setClubLessonEndingHour(?DateTimeInterface $club_lesson_ending_hour): self
    {
        $this->club_lesson_ending_hour = $club_lesson_ending_hour;

        return $this;
    }

    public function getClubLessonType(): ?int
    {
        return $this->club_lesson_type;
    }

    public function setClubLessonType(?int $club_lesson_type): self
    {
        $this->club_lesson_type = $club_lesson_type;

        return $this;
    }

    public function getClubLessonComment(): ?string
    {
        return $this->club_lesson_comment;
    }

    public function setClubLessonComment(?string $club_lesson_comment): self
    {
        $this->club_lesson_comment = $club_lesson_comment;

        return $this;
    }

    public function getClubLessonDojo(): ?ClubDojo
    {
        return $this->club_lesson_dojo;
    }

    public function setClubLessonDojo(?ClubDojo $club_lesson_dojo): self
    {
        $this->club_lesson_dojo = $club_lesson_dojo;

        return $this;
    }

    public function getClubLesson(): ?Club
    {
        return $this->club_lesson;
    }

    public function setClubLesson(?Club $club_lesson): self
    {
        $this->club_lesson = $club_lesson;

        return $this;
    }
}
