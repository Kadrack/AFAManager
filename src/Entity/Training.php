<?php
// src/Entity/Training.php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Training
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_training")
 * @ORM\Entity(repositoryClass="App\Repository\TrainingRepository")
 */
class Training
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $training_id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $training_name;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private int $training_type;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $training_place;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $training_street;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $training_zip;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $training_city;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $training_total_sessions;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $training_old_id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $training_comment;

    /**
     * @var TrainingSession|null
     */
    #[Assert\Type('App\Entity\TrainingSession')]
    #[Assert\Valid]
    protected ?TrainingSession $session;

    /**
     * @var TrainingSession|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\TrainingSession", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="training_join_training_first_session", referencedColumnName="training_session_id")
     */
    private ?TrainingSession $training_first_session;

    /**
     * @var Club|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_trainings", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="training_join_club", referencedColumnName="club_id")
     */
    private ?Club $training_club;

    /**
     * @var ArrayCollection|Collection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\TrainingAttendance", mappedBy="training", orphanRemoval=true, cascade={"persist"})
     */
    private ArrayCollection|Collection|null $training_attendances;

    /**
     * @var ArrayCollection|Collection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\TrainingSession", mappedBy="training", orphanRemoval=true, cascade={"persist"})
     */
    private ArrayCollection|Collection|null $training_sessions;

    /**
     * Training constructor.
     */
    public function __construct()
    {
        $this->training_attendances = new ArrayCollection();
        $this->training_sessions    = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getTrainingId(): int
    {
        return $this->training_id;
    }

    /**
     * @param int $training_id
     * @return $this
     */
    public function setTrainingId(int $training_id): self
    {
        $this->training_id = $training_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrainingName(): ?string
    {
        return $this->training_name;
    }

    /**
     * @param string|null $training_name
     * @return $this
     */
    public function setTrainingName(?string $training_name): self
    {
        $this->training_name = $training_name;

        return $this;
    }

    /**
     * @return int
     */
    public function getTrainingType(): int
    {
        return $this->training_type;
    }

    /**
     * @param int $training_type
     * @return $this
     */
    public function setTrainingType(int $training_type): self
    {
        $this->training_type = $training_type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrainingPlace(): ?string
    {
        return $this->training_place;
    }

    /**
     * @param string|null $training_place
     * @return $this
     */
    public function setTrainingPlace(?string $training_place): self
    {
        $this->training_place = $training_place;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrainingStreet(): ?string
    {
        return $this->training_street;
    }

    /**
     * @param string|null $training_street
     * @return $this
     */
    public function setTrainingStreet(?string $training_street): self
    {
        $this->training_street = $training_street;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingZip(): ?int
    {
        return $this->training_zip;
    }

    /**
     * @param int|null $training_zip
     * @return $this
     */
    public function setTrainingZip(?int $training_zip): self
    {
        $this->training_zip = $training_zip;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrainingCity(): ?string
    {
        return $this->training_city;
    }

    /**
     * @param string|null $training_city
     * @return $this
     */
    public function setTrainingCity(?string $training_city): self
    {
        $this->training_city = $training_city;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingTotalSessions(): ?int
    {
        return $this->training_total_sessions;
    }

    /**
     * @param int|null $training_total_sessions
     * @return $this
     */
    public function setTrainingTotalSessions(?int $training_total_sessions): self
    {
        $this->training_total_sessions = $training_total_sessions;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingOldId(): ?int
    {
        return $this->training_old_id;
    }

    /**
     * @param int|null $training_old_id
     * @return $this
     */
    public function setTrainingOldId(?int $training_old_id): self
    {
        $this->training_old_id = $training_old_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrainingComment(): ?string
    {
        return $this->training_comment;
    }

    /**
     * @param string|null $training_comment
     * @return $this
     */
    public function setTrainingComment(?string $training_comment): self
    {
        $this->training_comment = $training_comment;

        return $this;
    }

    /**
     * @return TrainingSession|null
     */
    public function getSession(): ?TrainingSession
    {
        return $this->session;
    }

    /**
     * @param TrainingSession|null $session
     */
    public function setSession(?TrainingSession $session = null)
    {
        $this->session = $session;
    }

    /**
     * @return TrainingSession|null
     */
    public function getTrainingFirstSession(): ?TrainingSession
    {
        return $this->training_first_session;
    }

    /**
     * @param TrainingSession|null $training_first_session
     * @return $this
     */
    public function setTrainingFirstSession(?TrainingSession $training_first_session): self
    {
        $this->training_first_session = $training_first_session;

        return $this;
    }

    /**
     * @return Club|null
     */
    public function getTrainingClub(): ?Club
    {
        return $this->training_club;
    }

    /**
     * @param Club|null $training_club
     * @return $this
     */
    public function setTrainingClub(?Club $training_club): self
    {
        $this->training_club = $training_club;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTrainingAttendances(): Collection
    {
        return $this->training_attendances;
    }

    /**
     * @param TrainingAttendance $trainingAttendance
     * @return $this
     */
    public function addTrainingAttendances(TrainingAttendance $trainingAttendance): self
    {
        if (!$this->training_attendances->contains($trainingAttendance)) {
            $this->training_attendances[] = $trainingAttendance;
            $trainingAttendance->setTraining($this);
        }

        return $this;
    }

    /**
     * @param TrainingAttendance $trainingAttendance
     * @return $this
     */
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
     * @return Collection
     */
    public function getTrainingSessions(): Collection
    {
        return $this->training_sessions;
    }

    /**
     * @param TrainingSession $trainingSession
     * @return $this
     */
    public function addTrainingSessions(TrainingSession $trainingSession): self
    {
        if (!$this->training_sessions->contains($trainingSession)) {
            $this->training_sessions[] = $trainingSession;
            $trainingSession->setTraining($this);
        }

        return $this;
    }

    /**
     * @param TrainingSession $trainingSession
     * @return $this
     */
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
