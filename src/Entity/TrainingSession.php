<?php
// src/Entity/TrainingSession.php
namespace App\Entity;

use App\Service\ListData;

use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class TrainingSession
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_training_session")
 * @ORM\Entity(repositoryClass="App\Repository\TrainingSessionRepository")
 */
class TrainingSession
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $training_session_id;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $training_session_date;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="time", nullable=true)
     */
    private ?DateTime $training_session_starting_hour;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="time", nullable=true)
     */
    private ?DateTime $training_session_ending_hour;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $training_session_duration;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $training_session_old_id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $training_session_comment;

    /**
     * @var Training|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Training", inversedBy="training_sessions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="training_join_training_session", referencedColumnName="training_id")
     */
    private ?Training $training;

    /**
     * @var ArrayCollection|Collection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\TrainingAttendance", mappedBy="training_attendance_session", orphanRemoval=true, cascade={"persist"})
     */
    private ArrayCollection|Collection|null $training_session_attendances;

    /**
     * TrainingSession constructor.
     */
    public function __construct()
    {
        $this->training_session_attendances = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getTrainingSessionId(): int
    {
        return $this->training_session_id;
    }

    /**
     * @param int $training_session_id
     * @return $this
     */
    public function setTrainingSessionId(int $training_session_id): self
    {
        $this->training_session_id = $training_session_id;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTrainingSessionDate(): ?DateTime
    {
        return $this->training_session_date;
    }

    /**
     * @param DateTime|null $training_session_date
     * @return $this
     */
    public function setTrainingSessionDate(?DateTime $training_session_date): self
    {
        $this->training_session_date = $training_session_date;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTrainingSessionStartingHour(): ?DateTime
    {
        return $this->training_session_starting_hour;
    }

    /**
     * @param DateTime|null $training_session_starting_hour
     * @return $this
     */
    public function setTrainingSessionStartingHour(?DateTime $training_session_starting_hour): self
    {
        $this->training_session_starting_hour = $training_session_starting_hour;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTrainingSessionEndingHour(): ?DateTime
    {
        return $this->training_session_ending_hour;
    }

    /**
     * @param DateTime|null $training_session_ending_hour
     * @return $this
     */
    public function setTrainingSessionEndingHour(?DateTime $training_session_ending_hour): self
    {
        $this->training_session_ending_hour = $training_session_ending_hour;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingSessionDuration(): ?int
    {
        return $this->training_session_duration;
    }

    /**
     * @param int|null $training_session_duration
     * @return $this
     */
    public function setTrainingSessionDuration(?int $training_session_duration): self
    {
        $this->training_session_duration = $training_session_duration;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingSessionOldId(): ?int
    {
        return $this->training_session_old_id;
    }

    /**
     * @param int|null $training_session_old_id
     * @return $this
     */
    public function setTrainingSessionOldId(?int $training_session_old_id): self
    {
        $this->training_session_old_id = $training_session_old_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrainingSessionComment(): ?string
    {
        return $this->training_session_comment;
    }

    /**
     * @param string|null $training_session_comment
     * @return $this
     */
    public function setTrainingSessionComment(?string $training_session_comment): self
    {
        $this->training_session_comment = $training_session_comment;

        return $this;
    }

    /**
     * @return Training|null
     */
    public function getTraining(): ?Training
    {
        return $this->training;
    }

    /**
     * @param Training|null $training
     * @return $this
     */
    public function setTraining(?Training $training): self
    {
        $this->training = $training;

        return $this;
    }

    public function getTrainingSessionChoiceName(): int|string|null
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
     * @return Collection
     */
    public function getTrainingSessionAttendances(): Collection
    {
        return $this->training_session_attendances;
    }

    /**
     * @param TrainingAttendance $trainingAttendance
     * @return $this
     */
    public function addTrainingSessionAttendances(TrainingAttendance $trainingAttendance): self
    {
        if (!$this->training_session_attendances->contains($trainingAttendance)) {
            $this->training_session_attendances[] = $trainingAttendance;
            $trainingAttendance->setTrainingAttendanceSession($this);
        }

        return $this;
    }

    /**
     * @param TrainingAttendance $trainingAttendance
     * @return $this
     */
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
