<?php
// src/Entity/GradeTitle.php
namespace App\Entity;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class GradeTitle
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_grade_title")
 * @ORM\Entity(repositoryClass="App\Repository\GradeTitleRepository")
 */
class GradeTitle
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $grade_title_id;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $grade_title_date;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank]
    private int $grade_title_rank;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $grade_title_certificate;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private int $grade_title_status;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $grade_title_comment;

    /**
     * @var Member
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="member_grades_title", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="grade_title_join_member", referencedColumnName="member_id")
     */
    private Member $grade_title_member;

    /**
     * @var GradeSession|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\GradeSession", inversedBy="grade_session_titles", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="grade_title_join_grade_session", referencedColumnName="grade_session_id")
     */
    private ?GradeSession $grade_title_exam;

    /**
     * @return int
     */
    public function getGradeTitleId(): int
    {
        return $this->grade_title_id;
    }

    /**
     * @param int $grade_title_id
     * @return $this
     */
    public function setGradeTitleId(int $grade_title_id): self
    {
        $this->grade_title_id = $grade_title_id;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getGradeTitleDate(): ?DateTime
    {
        return $this->grade_title_date;
    }

    /**
     * @param DateTime $grade_title_date
     * @return $this
     */
    public function setGradeTitleDate(DateTime $grade_title_date): self
    {
        $this->grade_title_date = $grade_title_date;

        return $this;
    }

    /**
     * @return int
     */
    public function getGradeTitleRank(): int
    {
        return $this->grade_title_rank;
    }

    /**
     * @param int $grade_title_rank
     * @return $this
     */
    public function setGradeTitleRank(int $grade_title_rank): self
    {
        $this->grade_title_rank = $grade_title_rank;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeTitleCertificate(): ?string
    {
        return $this->grade_title_certificate;
    }

    /**
     * @param string|null $grade_title_certificate
     * @return $this
     */
    public function setGradeTitleCertificate(?string $grade_title_certificate): self
    {
        $this->grade_title_certificate = $grade_title_certificate;

        return $this;
    }

    /**
     * @return int
     */
    public function getGradeTitleStatus(): int
    {
        return $this->grade_title_status;
    }

    /**
     * @param int $grade_title_status
     * @return $this
     */
    public function setGradeTitleStatus(int $grade_title_status): self
    {
        $this->grade_title_status = $grade_title_status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeTitleComment(): ?string
    {
        return $this->grade_title_comment;
    }

    /**
     * @param string|null $grade_title_comment
     * @return $this
     */
    public function setGradeTitleComment(?string $grade_title_comment): self
    {
        $this->grade_title_comment = $grade_title_comment;

        return $this;
    }

    /**
     * @return Member
     */
    public function getGradeTitleMember(): Member
    {
        return $this->grade_title_member;
    }

    /**
     * @param Member $grade_title_member
     * @return $this
     */
    public function setGradeTitleMember(Member $grade_title_member): self
    {
        $this->grade_title_member = $grade_title_member;

        return $this;
    }

    /**
     * @return GradeSession|null
     */
    public function getGradeTitleExam(): ?GradeSession
    {
        return $this->grade_title_exam;
    }

    /**
     * @param GradeSession|null $grade_title_exam
     * @return $this
     */
    public function setGradeTitleExam(?GradeSession $grade_title_exam): self
    {
        $this->grade_title_exam = $grade_title_exam;

        return $this;
    }
}
