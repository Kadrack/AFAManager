<?php
// src/Entity/GradeKyu.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="afamanager_grade_kyu")
 * @ORM\Entity(repositoryClass="App\Repository\GradeKyuRepository")
 */
class GradeKyu
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $grade_kyu_id;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank()
     */
    private $grade_kyu_rank;

    /**
     * @ORM\Column(type="date", nullable=true)
     *
     * @Assert\NotBlank()
     */
    private $grade_kyu_date;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $grade_kyu_comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="member_grades_kyu", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="grade_kyu_join_member", referencedColumnName="member_id")
     */
    private $grade_kyu_member;

    public function getGradeKyuId(): ?int
    {
        return $this->grade_kyu_id;
    }

    public function setGradeKyuId(int $grade_kyu_id): self
    {
        $this->grade_kyu_id = $grade_kyu_id;

        return $this;
    }

    public function getGradeKyuRank(): ?int
    {
        return $this->grade_kyu_rank;
    }

    public function setGradeKyuRank(int $grade_kyu_rank): self
    {
        $this->grade_kyu_rank = $grade_kyu_rank;

        return $this;
    }

    public function getGradeKyuDate(): ?\DateTimeInterface
    {
        return $this->grade_kyu_date;
    }

    public function setGradeKyuDate(\DateTimeInterface $grade_kyu_date): self
    {
        $this->grade_kyu_date = $grade_kyu_date;

        return $this;
    }

    public function getGradeKyuComment(): ?string
    {
        return $this->grade_kyu_comment;
    }

    public function setGradeKyuComment(?string $grade_kyu_comment): self
    {
        $this->grade_kyu_comment = $grade_kyu_comment;

        return $this;
    }

    public function getGradeKyuMember(): ?Member
    {
        return $this->grade_kyu_member;
    }

    public function setGradeKyuMember(?Member $grade_kyu_member): self
    {
        $this->grade_kyu_member = $grade_kyu_member;

        return $this;
    }
}
