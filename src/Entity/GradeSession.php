<?php
// src/Entity/GradeSession.php
namespace App\Entity;

use DateTimeInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="afamanager_grade_session")
 * @ORM\Entity(repositoryClass="App\Repository\GradeSessionRepository")
 */
class GradeSession
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $grade_session_id;

    /**
     * @ORM\Column(type="date")
     *
     * @Assert\NotBlank()
     */
    private $grade_session_date;

    /**
     * @ORM\Column(type="integer")
     *
     */
    private $grade_session_type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $grade_session_place;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $grade_session_street;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $grade_session_zip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $grade_session_city;

    /**
     * @ORM\Column(type="date")
     */
    private $grade_session_candidate_open;

    /**
     * @ORM\Column(type="date")
     */
    private $grade_session_candidate_close;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $grade_session_comment;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GradeDan", mappedBy="grade_dan_exam", orphanRemoval=true, cascade={"persist"})
     */
    private $grade_session_grades_dan;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GradeTitle", mappedBy="grade_title_exam", orphanRemoval=true, cascade={"persist"})
     */
    private $grade_session_titles;

    public function __construct()
    {
        $this->grade_session_grades_dan = new ArrayCollection();
        $this->grade_session_titles     = new ArrayCollection();
    }

    public function getGradeSessionId(): ?int
    {
        return $this->grade_session_id;
    }

    public function setGradeSessionId(int $grade_session_id): self
    {
        $this->grade_session_id = $grade_session_id;

        return $this;
    }

    public function getGradeSessionDate(): ?DateTimeInterface
    {
        return $this->grade_session_date;
    }

    public function setGradeSessionDate(DateTimeInterface $grade_session_date): self
    {
        $this->grade_session_date = $grade_session_date;

        return $this;
    }

    public function getGradeSessionType(): ?int
    {
        return $this->grade_session_type;
    }

    public function setGradeSessionType(int $grade_session_type): self
    {
        $this->grade_session_type = $grade_session_type;

        return $this;
    }

    public function getGradeSessionPlace(): ?string
    {
        return $this->grade_session_place;
    }

    public function setGradeSessionPlace(string $grade_session_place): self
    {
        $this->grade_session_place = $grade_session_place;

        return $this;
    }

    public function getGradeSessionStreet(): ?string
    {
        return $this->grade_session_street;
    }

    public function setGradeSessionStreet(string $grade_session_street): self
    {
        $this->grade_session_street = $grade_session_street;

        return $this;
    }

    public function getGradeSessionZip(): ?int
    {
        return $this->grade_session_zip;
    }

    public function setGradeSessionZip(int $grade_session_zip): self
    {
        $this->grade_session_zip = $grade_session_zip;

        return $this;
    }

    public function getGradeSessionCity(): ?string
    {
        return $this->grade_session_city;
    }

    public function setGradeSessionCity(string $grade_session_city): self
    {
        $this->grade_session_city = $grade_session_city;

        return $this;
    }

    public function getGradeSessionCandidateOpen(): ?DateTimeInterface
    {
        return $this->grade_session_candidate_open;
    }

    public function setGradeSessionCandidateOpen(DateTimeInterface $grade_session_candidate_open): self
    {
        $this->grade_session_candidate_open = $grade_session_candidate_open;

        return $this;
    }

    public function getGradeSessionCandidateClose(): ?DateTimeInterface
    {
        return $this->grade_session_candidate_close;
    }

    public function setGradeSessionCandidateClose(DateTimeInterface $grade_session_candidate_close): self
    {
        $this->grade_session_candidate_close = $grade_session_candidate_close;

        return $this;
    }

    public function getGradeSessionComment(): ?string
    {
        return $this->grade_session_comment;
    }

    public function setGradeSessionComment(?string $grade_session_comment): self
    {
        $this->grade_session_comment = $grade_session_comment;

        return $this;
    }

    /**
     * @return Collection|GradeDan[]
     */
    public function getGradeSessionGradesDan(): Collection
    {
        return $this->grade_session_grades_dan;
    }

    public function addGradeSessionGradesDan(GradeDan $gradeDan): self
    {
        if (!$this->grade_session_grades_dan->contains($gradeDan)) {
            $this->grade_session_grades_dan[] = $gradeDan;
            $gradeDan->setGradeDanExam($this);
        }

        return $this;
    }

    public function removeGradeSessionGradesDan(GradeDan $gradeDan): self
    {
        if ($this->grade_session_grades_dan->contains($gradeDan)) {
            $this->grade_session_grades_dan->removeElement($gradeDan);
            // set the owning side to null (unless already changed)
            if ($gradeDan->getGradeDanExam() === $this) {
                $gradeDan->setGradeDanExam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GradeTitle[]
     */
    public function getGradeSessionTitles(): Collection
    {
        return $this->grade_session_titles;
    }

    public function addGradeSessionTitles(GradeTitle $gradeTitle): self
    {
        if (!$this->grade_session_titles->contains($gradeTitle)) {
            $this->grade_session_titles[] = $gradeTitle;
            $gradeTitle->setGradeTitleExam($this);
        }

        return $this;
    }

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
