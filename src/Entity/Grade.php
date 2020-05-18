<?php
// src/Entity/Grade.php
namespace App\Entity;

use DateTimeInterface;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="afamanager_grade")
 * @ORM\Entity(repositoryClass="App\Repository\GradeRepository")
 */
class Grade
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $grade_id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $grade_date;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank()
     */
    private $grade_rank;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank()
     */
    private $grade_status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $grade_certificate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $grade_comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_grades", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="grade_join_club", referencedColumnName="club_id")
     */
    private $grade_club;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GradeSession", inversedBy="grade_session_grades", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="grade_join_grade_session", referencedColumnName="grade_session_id")
     */
    private $grade_exam;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="member_grades", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="grade_join_member", referencedColumnName="member_id")
     */
    private $grade_member;

    public function getGradeId(): ?int
    {
        return $this->grade_id;
    }

    public function setGradeId(?int $grade_id): self
    {
        $this->grade_id = $grade_id;

        return $this;
    }

    public function getGradeDate(): ?DateTimeInterface
    {
        return $this->grade_date;
    }

    public function setGradeDate(DateTimeInterface $grade_date): self
    {
        $this->grade_date = $grade_date;

        return $this;
    }

    public function getGradeRank(): ?int
    {
        return $this->grade_rank;
    }

    public function setGradeRank(?int $grade_rank): self
    {
        $this->grade_rank = $grade_rank;

        return $this;
    }

    public function getGradeStatus(): ?int
    {
        return $this->grade_status;
    }

    public function setGradeStatus(?int $grade_status): self
    {
        $this->grade_status = $grade_status;

        return $this;
    }

    public function getGradeCertificate(): ?string
    {
        return $this->grade_certificate;
    }

    public function setGradeCertificate(?string $grade_certificate): self
    {
        $this->grade_certificate = $grade_certificate;

        return $this;
    }

    public function getGradeComment(): ?string
    {
        return $this->grade_comment;
    }

    public function setGradeComment(?string $grade_comment): self
    {
        $this->grade_comment = $grade_comment;

        return $this;
    }

    public function getGradeClub(): ?Club
    {
        return $this->grade_club;
    }

    public function setGradeClub(?Club $grade_club): self
    {
        $this->grade_club = $grade_club;

        return $this;
    }

    public function getGradeExam(): ?GradeSession
    {
        return $this->grade_exam;
    }

    public function setGradeExam(?GradeSession $grade_exam): self
    {
        $this->grade_exam = $grade_exam;

        return $this;
    }

    public function getGradeMember(): ?Member
    {
        return $this->grade_member;
    }

    public function setGradeMember(?Member $grade_member): self
    {
        $this->grade_member = $grade_member;

        return $this;
    }
}
