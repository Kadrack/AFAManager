<?php
// src/Entity/Grade.php
namespace App\Entity;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Grade
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_grade")
 * @ORM\Entity(repositoryClass="App\Repository\GradeRepository")
 */
class Grade
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $grade_id;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $grade_date;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank]
    private int $grade_rank;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank]
    private int $grade_status;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $grade_certificate;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $grade_comment;

    /**
     * @var Club|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_grades", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="grade_join_club", referencedColumnName="club_id")
     */
    private ?Club $grade_club;

    /**
     * @var GradeSession|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\GradeSession", inversedBy="grade_session_grades", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="grade_join_grade_session", referencedColumnName="grade_session_id")
     */
    private ?GradeSession $grade_exam;

    /**
     * @var Member
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="member_grades", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="grade_join_member", referencedColumnName="member_id")
     */
    private Member $grade_member;

    /**
     * @return int
     */
    public function getGradeId(): int
    {
        return $this->grade_id;
    }

    /**
     * @param int $grade_id
     * @return $this
     */
    public function setGradeId(int $grade_id): self
    {
        $this->grade_id = $grade_id;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getGradeDate(): ?DateTime
    {
        return $this->grade_date;
    }

    /**
     * @param DateTime $grade_date
     * @return $this
     */
    public function setGradeDate(DateTime $grade_date): self
    {
        $this->grade_date = $grade_date;

        return $this;
    }

    /**
     * @return int
     */
    public function getGradeRank(): int
    {
        return $this->grade_rank;
    }

    /**
     * @param int $grade_rank
     * @return $this
     */
    public function setGradeRank(int $grade_rank): self
    {
        $this->grade_rank = $grade_rank;

        return $this;
    }

    /**
     * @return int
     */
    public function getGradeStatus(): int
    {
        return $this->grade_status;
    }

    /**
     * @param int $grade_status
     * @return $this
     */
    public function setGradeStatus(int $grade_status): self
    {
        $this->grade_status = $grade_status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeCertificate(): ?string
    {
        return $this->grade_certificate;
    }

    /**
     * @param string|null $grade_certificate
     * @return $this
     */
    public function setGradeCertificate(?string $grade_certificate): self
    {
        $this->grade_certificate = $grade_certificate;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeComment(): ?string
    {
        return $this->grade_comment;
    }

    /**
     * @param string|null $grade_comment
     * @return $this
     */
    public function setGradeComment(?string $grade_comment): self
    {
        $this->grade_comment = $grade_comment;

        return $this;
    }

    /**
     * @return Club|null
     */
    public function getGradeClub(): ?Club
    {
        return $this->grade_club;
    }

    /**
     * @param Club|null $grade_club
     * @return $this
     */
    public function setGradeClub(?Club $grade_club): self
    {
        $this->grade_club = $grade_club;

        return $this;
    }

    /**
     * @return GradeSession|null
     */
    public function getGradeExam(): ?GradeSession
    {
        return $this->grade_exam;
    }

    /**
     * @param GradeSession|null $grade_exam
     * @return $this
     */
    public function setGradeExam(?GradeSession $grade_exam): self
    {
        $this->grade_exam = $grade_exam;

        return $this;
    }

    /**
     * @return Member
     */
    public function getGradeMember(): Member
    {
        return $this->grade_member;
    }

    /**
     * @param Member $grade_member
     * @return $this
     */
    public function setGradeMember(Member $grade_member): self
    {
        $this->grade_member = $grade_member;

        return $this;
    }
}
