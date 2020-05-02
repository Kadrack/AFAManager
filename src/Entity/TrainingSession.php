<?php
// src/Entity/TrainingSession.php
namespace App\Entity;

use App\Service\ListData;

use DateTimeInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="afamanager_training_session")
 * @ORM\Entity(repositoryClass="App\Repository\TrainingSessionRepository")
 */
class TrainingSession
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $training_session_id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $training_session_date;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $training_session_starting_hour;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $training_session_ending_hour;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $training_session_duration;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $training_session_old_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $training_session_comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Training", inversedBy="training_sessions", cascade={"persist"})
     * @ORM\JoinColumn(name="training_join_training_session", referencedColumnName="training_id")
     */
    private $training;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrainingAttendance", mappedBy="training_attendance_session", orphanRemoval=true, cascade={"persist"})
     */
    private $training_session_attendances;

    public function __construct()
    {
        $this->training_session_attendances = new ArrayCollection();
    }

    public function getTrainingSessionId(): ?int
    {
        return $this->training_session_id;
    }

    public function setTrainingSessionId(?int $training_session_id): self
    {
        $this->training_session_id = $training_session_id;

        return $this;
    }

    public function getTrainingSessionDate(): ?DateTimeInterface
    {
        return $this->training_session_date;
    }

    public function setTrainingSessionDate(?DateTimeInterface $training_session_date): self
    {
        $this->training_session_date = $training_session_date;

        return $this;
    }

    public function getTrainingSessionStartingHour(): ?DateTimeInterface
    {
        return $this->training_session_starting_hour;
    }

    public function setTrainingSessionStartingHour(?DateTimeInterface $training_session_starting_hour): self
    {
        $this->training_session_starting_hour = $training_session_starting_hour;

        return $this;
    }

    public function getTrainingSessionEndingHour(): ?DateTimeInterface
    {
        return $this->training_session_ending_hour;
    }

    public function setTrainingSessionEndingHour(?DateTimeInterface $training_session_ending_hour): self
    {
        $this->training_session_ending_hour = $training_session_ending_hour;

        return $this;
    }

    public function getTrainingSessionDuration(): ?int
    {
        return $this->training_session_duration;
    }

    public function setTrainingSessionDuration(?int $training_session_duration): self
    {
        $this->training_session_duration = $training_session_duration;

        return $this;
    }

    public function getTrainingSessionOldId(): ?int
    {
        return $this->training_session_old_id;
    }

    public function setTrainingSessionOldId(?int $training_session_old_id): self
    {
        $this->training_session_old_id = $training_session_old_id;

        return $this;
    }

    public function getTrainingSessionComment(): ?string
    {
        return $this->training_session_comment;
    }

    public function setTrainingSessionComment(?string $training_session_comment): self
    {
        $this->training_session_comment = $training_session_comment;

        return $this;
    }

    public function getTraining(): ?Training
    {
        return $this->training;
    }

    public function setTraining(?Training $training): self
    {
        $this->training = $training;

        return $this;
    }

    public function getTrainingSessionChoiceName()
    {
        $list_data = new ListData();

        if ($this->training_session_starting_hour == null)
        {
            return $this->training_session_duration;
        }

        if ($this->training_session_starting_hour->format('H') < 12)
        {
            $moment = 'Matin';
        }
        else
        {
            $moment = 'Après-midi';
        }

        return $list_data->getDay($this->training_session_date->format('N')).' '.$moment;
    }

    /**
     * @return Collection|TrainingAttendance[]
     */
    public function getTrainingSessionAttendances(): Collection
    {
        return $this->training_session_attendances;
    }

    public function addTrainingSessionAttendances(TrainingAttendance $trainingAttendance): self
    {
        if (!$this->training_session_attendances->contains($trainingAttendance)) {
            $this->training_session_attendances[] = $trainingAttendance;
            $trainingAttendance->setTrainingAttendanceSession($this);
        }

        return $this;
    }

    public function removeTrainingSessionAttendances(TrainingAttendance $trainingAttendance): self
    {
        if ($this->training_session_attendances->contains($trainingAttendance)) {
            $this->training_session_attendances->removeElement($trainingAttendance);
            // set the owning side to null (unless already changed)
            if ($trainingAttendance->getTrainingAttendanceSession() === $this) {
                $trainingAttendance->setTrainingAttendanceSession(null);
            }
        }

        return $this;
    }
}
