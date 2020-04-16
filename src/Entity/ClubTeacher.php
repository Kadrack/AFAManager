<?php
// src/Entity/ClubTeacher.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="afamanager_club_teacher")
 * @ORM\Entity(repositoryClass="App\Repository\ClubTeacherRepository")
 */
class ClubTeacher
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $club_teacher_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $club_teacher_firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $club_teacher_name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $club_teacher_grade;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $club_teacher_grade_title_aikikai;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $club_teacher_grade_title_adeps;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank()
     */
    private $club_teacher_title;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank()
     */
    private $club_teacher_type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $club_teacher_comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_teachers", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="club_teacher_join_club", referencedColumnName="club_id")
     */
    private $club_teacher;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="member_teachers", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="club_teacher_join_member", referencedColumnName="member_id")
     */
    private $club_teacher_member;

    public function getClubTeacherId(): ?int
    {
        return $this->club_teacher_id;
    }

    public function setClubTeacherId(?int $club_teacher_id): self
    {
        $this->club_teacher_id = $club_teacher_id;

        return $this;
    }

    public function getClubTeacherFirstname(): ?string
    {
        return $this->club_teacher_firstname;
    }

    public function setClubTeacherFirstname(?string $club_teacher_firstname): self
    {
        $this->club_teacher_firstname = $club_teacher_firstname;

        return $this;
    }

    public function getClubTeacherName(): ?string
    {
        return $this->club_teacher_name;
    }

    public function setClubTeacherName(?string $club_teacher_name): self
    {
        $this->club_teacher_name = $club_teacher_name;

        return $this;
    }

    public function getClubTeacherGrade(): ?int
    {
        return $this->club_teacher_grade;
    }

    public function setClubTeacherGrade(?int $club_teacher_grade): self
    {
        $this->club_teacher_grade = $club_teacher_grade;

        return $this;
    }

    public function getClubTeacherGradeTitleAikikai(): ?int
    {
        return $this->club_teacher_grade_title_aikikai;
    }

    public function setClubTeacherGradeTitleAikikai(?int $club_teacher_grade_title_aikikai): self
    {
        $this->club_teacher_grade_title_aikikai = $club_teacher_grade_title_aikikai;

        return $this;
    }

    public function getClubTeacherGradeTitleAdeps(): ?int
    {
        return $this->club_teacher_grade_title_adeps;
    }

    public function setClubTeacherGradeTitleAdeps(?int $club_teacher_grade_title_adeps): self
    {
        $this->club_teacher_grade_title_adeps = $club_teacher_grade_title_adeps;

        return $this;
    }

    public function getClubTeacherTitle(): ?int
    {
        return $this->club_teacher_title;
    }

    public function setClubTeacherTitle(?int $club_teacher_title): self
    {
        $this->club_teacher_title = $club_teacher_title;

        return $this;
    }

    public function getClubTeacherType(): ?int
    {
        return $this->club_teacher_type;
    }

    public function setClubTeacherType(?int $club_teacher_type): self
    {
        $this->club_teacher_type = $club_teacher_type;

        return $this;
    }

    public function getClubTeacherComment(): ?string
    {
        return $this->club_teacher_comment;
    }

    public function setClubTeacherComment(?string $club_teacher_comment): self
    {
        $this->club_teacher_comment = $club_teacher_comment;

        return $this;
    }

    public function getClubTeacher(): ?Club
    {
        return $this->club_teacher;
    }

    public function setClubTeacher(?Club $club_teacher): self
    {
        $this->club_teacher = $club_teacher;

        return $this;
    }

    public function getClubTeacherMember(): ?Member
    {
        return $this->club_teacher_member;
    }

    public function setClubTeacherMember(?Member $club_teacher_member): self
    {
        $this->club_teacher_member = $club_teacher_member;

        return $this;
    }
}
