<?php
// src/Entity/Training.php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="afamanager_training")
 * @ORM\Entity(repositoryClass="App\Repository\TrainingRepository")
 */
class Training
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private ?int $training_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $training_name;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $training_type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $training_place;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $training_street;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $training_zip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $training_city;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $training_total_sessions;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $training_old_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $training_comment;

    /**
     * @Assert\Type(type="App\Entity\TrainingSession")
     * @Assert\Valid
     */
    protected ?TrainingSession $session;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\TrainingSession", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="training_join_training_first_session", referencedColumnName="training_session_id")
     */
    private ?TrainingSession $training_first_session;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_trainings", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="training_join_club", referencedColumnName="club_id")
     */
    private ?Club $training_club;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrainingAttendance", mappedBy="training", orphanRemoval=true, cascade={"persist"})
     */
    private ArrayCollection $training_attendances;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrainingSession", mappedBy="training", orphanRemoval=true, cascade={"persist"})
     */
    private ArrayCollection $training_sessions;

    public function __construct()
    {
        $this->training_attendances = new ArrayCollection();
        $this->training_sessions    = new ArrayCollection();
    }

    public function getTrainingId(): ?int
    {
        return $this->training_id;
    }

    public function setTrainingId(?int $training_id): self
    {
        $this->training_id = $training_id;

        return $this;
    }

    public function getTrainingName(): ?string
    {
        return $this->training_name;
    }

    public function setTrainingName(?string $training_name): self
    {
        $this->training_name = $training_name;

        return $this;
    }

    public function getTrainingType(): ?int
    {
        return $this->training_type;
    }

    public function setTrainingType(?int $training_type): self
    {
        $this->training_type = $training_type;

        return $this;
    }

    public function getTrainingPlace(): ?string
    {
        return $this->training_place;
    }

    public function setTrainingPlace(?string $training_place): self
    {
        $this->training_place = $training_place;

        return $this;
    }

    public function getTrainingStreet(): ?string
    {
        return $this->training_street;
    }

    public function setTrainingStreet(?string $training_street): self
    {
        $this->training_street = $training_street;

        return $this;
    }

    public function getTrainingZip(): ?int
    {
        return $this->training_zip;
    }

    public function setTrainingZip(?int $training_zip): self
    {
        $this->training_zip = $training_zip;

        return $this;
    }

    public function getTrainingCity(): ?string
    {
        return $this->training_city;
    }

    public function setTrainingCity(?string $training_city): self
    {
        $this->training_city = $training_city;

        return $this;
    }

    public function getTrainingTotalSessions(): ?int
    {
        return $this->training_total_sessions;
    }

    public function setTrainingTotalSessions(?int $training_total_sessions): self
    {
        $this->training_total_sessions = $training_total_sessions;

        return $this;
    }

    public function getTrainingOldId(): ?int
    {
        return $this->training_old_id;
    }

    public function setTrainingOldId(?int $training_old_id): self
    {
        $this->training_old_id = $training_old_id;

        return $this;
    }

    public function getTrainingComment(): ?string
    {
        return $this->training_comment;
    }

    public function setTrainingComment(?string $training_comment): self
    {
        $this->training_comment = $training_comment;

        return $this;
    }

    public function getSession(): ?TrainingSession
    {
        return $this->session;
    }

    public function setSession(?TrainingSession $session = null)
    {
        $this->session = $session;
    }

    public function getTrainingFirstSession(): ?TrainingSession
    {
        return $this->training_first_session;
    }

    public function setTrainingFirstSession(?TrainingSession $training_first_session): self
    {
        $this->training_first_session = $training_first_session;

        return $this;
    }

    public function getTrainingClub(): ?Club
    {
        return $this->training_club;
    }

    public function setTrainingClub(?Club $training_club): self
    {
        $this->training_club = $training_club;

        return $this;
    }

    /**
     * @return Collection|TrainingAttendance[]
     */
    public function getTrainingAttendances(): Collection
    {
        return $this->training_attendances;
    }

    public function addTrainingAttendances(TrainingAttendance $trainingAttendance): self
    {
        if (!$this->training_attendances->contains($trainingAttendance)) {
            $this->training_attendances[] = $trainingAttendance;
            $trainingAttendance->setTraining($this);
        }

        return $this;
    }

    public function removeTrainingAttendances(TrainingAttendance $trainingAttendance): self
    {
        if ($this->training_attendances->contains($trainingAttendance)) {
            $this->training_attendances->removeElement($trainingAttendance);
            // set the owning side to null (unless already changed)
            if ($trainingAttendance->getTraining() === $this) {
                $trainingAttendance->setTraining(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrainingSession[]
     */
    public function getTrainingSessions(): Collection
    {
        return $this->training_sessions;
    }

    public function addTrainingSessions(TrainingSession $trainingSession): self
    {
        if (!$this->training_sessions->contains($trainingSession)) {
            $this->training_sessions[] = $trainingSession;
            $trainingSession->setTraining($this);
        }

        return $this;
    }

    public function removeTrainingSessions(TrainingSession $trainingSession): self
    {
        if ($this->training_sessions->contains($trainingSession)) {
            $this->training_sessions->removeElement($trainingSession);
            // set the owning side to null (unless already changed)
            if ($trainingSession->getTraining() === $this) {
                $trainingSession->setTraining(null);
            }
        }

        return $this;
    }
}
