<?php
// src/Entity/ClubTeacher.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ClubTeacher
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_club_teacher")
 * @ORM\Entity(repositoryClass="App\Repository\ClubTeacherRepository")
 */
class ClubTeacher
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $club_teacher_id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $club_teacher_firstname;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $club_teacher_name;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $club_teacher_grade;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $club_teacher_grade_title_aikikai;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $club_teacher_grade_title_adeps;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank]
    private int $club_teacher_title;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank]
    private int $club_teacher_type;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $club_teacher_comment;

    /**
     * @var Club
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_teachers", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="club_teacher_join_club", referencedColumnName="club_id")
     */
    private Club $club_teacher;

    /**
     * @var Member|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="member_teachers", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="club_teacher_join_member", referencedColumnName="member_id")
     */
    private ?Member $club_teacher_member;

    /**
     * @return int|null
     */
    public function getClubTeacherId(): ?int
    {
        return $this->club_teacher_id;
    }

    /**
     * @param int $club_teacher_id
     * @return $this
     */
    public function setClubTeacherId(int $club_teacher_id): self
    {
        $this->club_teacher_id = $club_teacher_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubTeacherFirstname(): ?string
    {
        return $this->club_teacher_firstname;
    }

    /**
     * @param string|null $club_teacher_firstname
     * @return $this
     */
    public function setClubTeacherFirstname(?string $club_teacher_firstname): self
    {
        $this->club_teacher_firstname = $club_teacher_firstname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubTeacherName(): ?string
    {
        return $this->club_teacher_name;
    }

    /**
     * @param string|null $club_teacher_name
     * @return $this
     */
    public function setClubTeacherName(?string $club_teacher_name): self
    {
        $this->club_teacher_name = $club_teacher_name;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubTeacherGrade(): ?int
    {
        return $this->club_teacher_grade;
    }

    /**
     * @param int|null $club_teacher_grade
     * @return $this
     */
    public function setClubTeacherGrade(?int $club_teacher_grade): self
    {
        $this->club_teacher_grade = $club_teacher_grade;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubTeacherGradeTitleAikikai(): ?int
    {
        return $this->club_teacher_grade_title_aikikai;
    }

    /**
     * @param int|null $club_teacher_grade_title_aikikai
     * @return $this
     */
    public function setClubTeacherGradeTitleAikikai(?int $club_teacher_grade_title_aikikai): self
    {
        $this->club_teacher_grade_title_aikikai = $club_teacher_grade_title_aikikai;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubTeacherGradeTitleAdeps(): ?int
    {
        return $this->club_teacher_grade_title_adeps;
    }

    /**
     * @param int|null $club_teacher_grade_title_adeps
     * @return $this
     */
    public function setClubTeacherGradeTitleAdeps(?int $club_teacher_grade_title_adeps): self
    {
        $this->club_teacher_grade_title_adeps = $club_teacher_grade_title_adeps;

        return $this;
    }

    /**
     * @return int
     */
    public function getClubTeacherTitle(): int
    {
        return $this->club_teacher_title;
    }

    /**
     * @param int $club_teacher_title
     * @return $this
     */
    public function setClubTeacherTitle(int $club_teacher_title): self
    {
        $this->club_teacher_title = $club_teacher_title;

        return $this;
    }

    /**
     * @return int
     */
    public function getClubTeacherType(): int
    {
        return $this->club_teacher_type;
    }

    /**
     * @param int $club_teacher_type
     * @return $this
     */
    public function setClubTeacherType(int $club_teacher_type): self
    {
        $this->club_teacher_type = $club_teacher_type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubTeacherComment(): ?string
    {
        return $this->club_teacher_comment;
    }

    /**
     * @param string|null $club_teacher_comment
     * @return $this
     */
    public function setClubTeacherComment(?string $club_teacher_comment): self
    {
        $this->club_teacher_comment = $club_teacher_comment;

        return $this;
    }

    /**
     * @return Club
     */
    public function getClubTeacher(): Club
    {
        return $this->club_teacher;
    }

    /**
     * @param Club $club_teacher
     * @return $this
     */
    public function setClubTeacher(Club $club_teacher): self
    {
        $this->club_teacher = $club_teacher;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getClubTeacherMember(): ?Member
    {
        return $this->club_teacher_member;
    }

    /**
     * @param Member|null $club_teacher_member
     * @return $this
     */
    public function setClubTeacherMember(?Member $club_teacher_member): self
    {
        $this->club_teacher_member = $club_teacher_member;

        return $this;
    }
}
