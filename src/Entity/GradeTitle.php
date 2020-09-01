<?php
// src/Entity/GradeTitle.php
namespace App\Entity;

use DateTimeInterface;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="afamanager_grade_title")
 * @ORM\Entity(repositoryClass="App\Repository\GradeTitleRepository")
 */
class GradeTitle
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $grade_title_id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $grade_title_date;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank()
     */
    private $grade_title_rank;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $grade_title_certificate;

    /**
     * @ORM\Column(type="integer")
     */
    private $grade_title_status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $grade_title_comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="member_grades_title", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="grade_title_join_member", referencedColumnName="member_id")
     */
    private $grade_title_member;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GradeSession", inversedBy="grade_session_titles", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="grade_title_join_grade_session", referencedColumnName="grade_session_id")
     */
    private $grade_title_exam;

    public function getGradeTitleId(): ?int
    {
        return $this->grade_title_id;
    }

    public function setGradeTitleId(int $grade_title_id): self
    {
        $this->grade_title_id = $grade_title_id;

        return $this;
    }

    public function getGradeTitleDate(): ?DateTimeInterface
    {
        return $this->grade_title_date;
    }

    public function setGradeTitleDate(DateTimeInterface $grade_title_date): self
    {
        $this->grade_title_date = $grade_title_date;

        return $this;
    }

    public function getGradeTitleRank(): ?int
    {
        return $this->grade_title_rank;
    }

    public function setGradeTitleRank(int $grade_title_rank): self
    {
        $this->grade_title_rank = $grade_title_rank;

        return $this;
    }

    public function getGradeTitleCertificate(): ?string
    {
        return $this->grade_title_certificate;
    }

    public function setGradeTitleCertificate(string $grade_title_certificate): self
    {
        $this->grade_title_certificate = $grade_title_certificate;

        return $this;
    }

    public function getGradeTitleStatus(): ?int
    {
        return $this->grade_title_status;
    }

    public function setGradeTitleStatus(int $grade_title_status): self
    {
        $this->grade_title_status = $grade_title_status;

        return $this;
    }

    public function getGradeTitleComment(): ?string
    {
        return $this->grade_title_comment;
    }

    public function setGradeTitleComment(?string $grade_title_comment): self
    {
        $this->grade_title_comment = $grade_title_comment;

        return $this;
    }

    public function getGradeTitleMember(): ?Member
    {
        return $this->grade_title_member;
    }

    public function setGradeTitleMember(?Member $grade_title_member): self
    {
        $this->grade_title_member = $grade_title_member;

        return $this;
    }

    public function getGradeTitleExam(): ?GradeSession
    {
        return $this->grade_title_exam;
    }

    public function setGradeTitleExam(?GradeSession $grade_title_exam): self
    {
        $this->grade_title_exam = $grade_title_exam;

        return $this;
    }
}
