<?php
// src/Entity/GradeDan.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="afamanager_grade_dan")
 * @ORM\Entity(repositoryClass="App\Repository\GradeDanRepository")
 */
class GradeDan
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $grade_dan_id;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank()
     */
    private $grade_dan_rank;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank()
     */
    private $grade_dan_status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $grade_dan_certificate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $grade_dan_comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_grades_dan", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="grade_dan_join_club", referencedColumnName="club_id")
     */
    private $grade_dan_club;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GradeSession", inversedBy="grade_session_grades_dan", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="grade_dan_join_grade_session", referencedColumnName="grade_session_id")
     */
    private $grade_dan_exam;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="member_exams", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="grade_dan_join_member", referencedColumnName="member_id")
     */
    private $grade_dan_member;

    public function getGradeDanId(): ?int
    {
        return $this->grade_dan_id;
    }

    public function setGradeDanId(?int $grade_dan_id): self
    {
        $this->grade_dan_id = $grade_dan_id;

        return $this;
    }

    public function getGradeDanRank(): ?int
    {
        return $this->grade_dan_rank;
    }

    public function setGradeDanRank(?int $grade_dan_rank): self
    {
        $this->grade_dan_rank = $grade_dan_rank;

        return $this;
    }

    public function getGradeDanStatus(): ?int
    {
        return $this->grade_dan_status;
    }

    public function setGradeDanStatus(?int $grade_dan_status): self
    {
        $this->grade_dan_status = $grade_dan_status;

        return $this;
    }

    public function getGradeDanCertificate(): ?string
    {
        return $this->grade_dan_certificate;
    }

    public function setGradeDanCertificate(?string $grade_dan_certificate): self
    {
        $this->grade_dan_certificate = $grade_dan_certificate;

        return $this;
    }

    public function getGradeDanComment(): ?string
    {
        return $this->grade_dan_comment;
    }

    public function setGradeDanComment(?string $grade_dan_comment): self
    {
        $this->grade_dan_comment = $grade_dan_comment;

        return $this;
    }

    public function getGradeDanClub(): ?Club
    {
        return $this->grade_dan_club;
    }

    public function setGradeDanClub(?Club $grade_dan_club): self
    {
        $this->grade_dan_club = $grade_dan_club;

        return $this;
    }

    public function getGradeDanExam(): ?GradeSession
    {
        return $this->grade_dan_exam;
    }

    public function setGradeDanExam(?GradeSession $grade_dan_exam): self
    {
        $this->grade_dan_exam = $grade_dan_exam;

        return $this;
    }

    public function getGradeDanMember(): ?Member
    {
        return $this->grade_dan_member;
    }

    public function setGradeDanMember(?Member $grade_dan_member): self
    {
        $this->grade_dan_member = $grade_dan_member;

        return $this;
    }
}
