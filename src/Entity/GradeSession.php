<?php
// src/Entity/GradeSession.php
namespace App\Entity;

use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class GradeSession
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_grade_session")
 * @ORM\Entity(repositoryClass="App\Repository\GradeSessionRepository")
 */
class GradeSession
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $grade_session_id;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    #[Assert\NotBlank]
    private DateTime $grade_session_date;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private int $grade_session_type;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $grade_session_place;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $grade_session_street;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $grade_session_zip;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $grade_session_city;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private DateTime $grade_session_candidate_open;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private DateTime $grade_session_candidate_close;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $grade_session_comment;

    /**
     * @var ArrayCollection|Collection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Grade", mappedBy="grade_exam", orphanRemoval=true, cascade={"persist"})
     */
    private ArrayCollection|Collection|null $grade_session_grades;

    /**
     * @var ArrayCollection|Collection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\GradeTitle", mappedBy="grade_title_exam", orphanRemoval=true, cascade={"persist"})
     */
    private ArrayCollection|Collection|null $grade_session_titles;

    /**
     * GradeSession constructor.
     */
    public function __construct()
    {
        $this->grade_session_grades = new ArrayCollection();
        $this->grade_session_titles = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getGradeSessionId(): int
    {
        return $this->grade_session_id;
    }

    /**
     * @param int $grade_session_id
     * @return $this
     */
    public function setGradeSessionId(int $grade_session_id): self
    {
        $this->grade_session_id = $grade_session_id;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getGradeSessionDate(): DateTime
    {
        return $this->grade_session_date;
    }

    /**
     * @param DateTime $grade_session_date
     * @return $this
     */
    public function setGradeSessionDate(DateTime $grade_session_date): self
    {
        $this->grade_session_date = $grade_session_date;

        return $this;
    }

    /**
     * @return int
     */
    public function getGradeSessionType(): int
    {
        return $this->grade_session_type;
    }

    /**
     * @param int $grade_session_type
     * @return $this
     */
    public function setGradeSessionType(int $grade_session_type): self
    {
        $this->grade_session_type = $grade_session_type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeSessionPlace(): ?string
    {
        return $this->grade_session_place;
    }

    /**
     * @param string|null $grade_session_place
     * @return $this
     */
    public function setGradeSessionPlace(?string $grade_session_place): self
    {
        $this->grade_session_place = $grade_session_place;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeSessionStreet(): ?string
    {
        return $this->grade_session_street;
    }

    /**
     * @param string|null $grade_session_street
     * @return $this
     */
    public function setGradeSessionStreet(?string $grade_session_street): self
    {
        $this->grade_session_street = $grade_session_street;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGradeSessionZip(): ?int
    {
        return $this->grade_session_zip;
    }

    /**
     * @param int|null $grade_session_zip
     * @return $this
     */
    public function setGradeSessionZip(?int $grade_session_zip): self
    {
        $this->grade_session_zip = $grade_session_zip;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeSessionCity(): ?string
    {
        return $this->grade_session_city;
    }

    /**
     * @param string|null $grade_session_city
     * @return $this
     */
    public function setGradeSessionCity(?string $grade_session_city): self
    {
        $this->grade_session_city = $grade_session_city;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getGradeSessionCandidateOpen(): DateTime
    {
        return $this->grade_session_candidate_open;
    }

    /**
     * @param DateTime $grade_session_candidate_open
     * @return $this
     */
    public function setGradeSessionCandidateOpen(DateTime $grade_session_candidate_open): self
    {
        $this->grade_session_candidate_open = $grade_session_candidate_open;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getGradeSessionCandidateClose(): DateTime
    {
        return $this->grade_session_candidate_close;
    }

    /**
     * @param DateTime $grade_session_candidate_close
     * @return $this
     */
    public function setGradeSessionCandidateClose(DateTime $grade_session_candidate_close): self
    {
        $this->grade_session_candidate_close = $grade_session_candidate_close;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeSessionComment(): ?string
    {
        return $this->grade_session_comment;
    }

    /**
     * @param string|null $grade_session_comment
     * @return $this
     */
    public function setGradeSessionComment(?string $grade_session_comment): self
    {
        $this->grade_session_comment = $grade_session_comment;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getGradeSessionGrades(): Collection
    {
        return $this->grade_session_grades;
    }

    /**
     * @param Grade $grade
     * @return $this
     */
    public function addGradeSessionGrades(Grade $grade): self
    {
        if (!$this->grade_session_grades->contains($grade)) {
            $this->grade_session_grades[] = $grade;
            $grade->setGradeExam($this);
        }

        return $this;
    }

    /**
     * @param Grade $grade
     * @return $this
     */
    public function removeGradeSessionGrades(Grade $grade): self
    {
        if ($this->grade_session_grades->contains($grade)) {
            $this->grade_session_grades->removeElement($grade);
            // set the owning side to null (unless already changed)
            if ($grade->getGradeExam() === $this) {
                $grade->setGradeExam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getGradeSessionTitles(): Collection
    {
        return $this->grade_session_titles;
    }

    /**
     * @param GradeTitle $gradeTitle
     * @return $this
     */
    public function addGradeSessionTitles(GradeTitle $gradeTitle): self
    {
        if (!$this->grade_session_titles->contains($gradeTitle)) {
            $this->grade_session_titles[] = $gradeTitle;
            $gradeTitle->setGradeTitleExam($this);
        }

        return $this;
    }

    /**
     * @param GradeTitle $gradeTitle
     * @return $this
     */
    public function removeGradeSessionTitles(GradeTitle $gradeTitle): self
    {
        if ($this->grade_session_titles->contains($gradeTitle)) {
            $this->grade_session_titles->removeElement($gradeTitle);
            // set the owning side to null (unless already changed)
            if ($gradeTitle->getGradeTitleExam() === $this) {
                $gradeTitle->setGradeTitleExam(null);
            }
        }

        return $this;
    }
}
