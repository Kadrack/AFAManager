<?php
// src/Entity/Club.php
namespace App\Entity;

use DateTimeInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="afamanager_club")
 * @ORM\Entity(repositoryClass="App\Repository\ClubRepository")
 */
class Club
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $club_id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private $club_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $club_address;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $club_zip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $club_city;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $club_province;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $club_creation;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $club_type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $club_bce_number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $club_iban;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Url()
     */
    private $club_url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Email()
     */

    private $club_email_public;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */

    private $club_president;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $club_treasurer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $club_secretary;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Email()
     */
    private $club_email_contact;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $club_comment;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ClubHistory")
     * @ORM\JoinColumn(nullable=true, name="club_join_club_last_history", referencedColumnName="club_history_id")
     */
    private $club_last_history;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ClubTeacher")
     * @ORM\JoinColumn(nullable=true, name="club_join_club_main_teacher", referencedColumnName="club_teacher_id")
     */
    private $club_main_teacher;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrainingAddress", mappedBy="training_address_club", orphanRemoval=true, cascade={"persist"})
     */
    private $club_addresses;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GradeDan", mappedBy="grade_dan_club", orphanRemoval=true, cascade={"persist"})
     */
    private $club_grades_dan;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ClubHistory", mappedBy="club_history", orphanRemoval=true, cascade={"persist"})
     */
    private $club_histories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MemberLicence", mappedBy="member_licence_club", orphanRemoval=true, cascade={"persist"})
     */
    private $club_licences;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ClubTeacher", mappedBy="club_teacher", orphanRemoval=true, cascade={"persist"})
     */
    private $club_teachers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Training", mappedBy="training_club", orphanRemoval=true, cascade={"persist"})
     */
    private $club_trainings;

    public function __construct()
    {
        $this->club_addresses  = new ArrayCollection();
        $this->club_grades_dan = new ArrayCollection();
        $this->club_histories  = new ArrayCollection();
        $this->club_licences   = new ArrayCollection();
        $this->club_teachers   = new ArrayCollection();
        $this->club_trainings  = new ArrayCollection();
    }

    public function getClubId(): ?int
    {
        return $this->club_id;
    }

    public function setClubId(?int $club_id): self
    {
        $this->club_id = $club_id;

        return $this;
    }

    public function getClubName(): ?string
    {
        return $this->club_name;
    }

    public function setClubName(?string $club_name): self
    {
        $this->club_name = $club_name;

        return $this;
    }

    public function getClubAddress(): ?string
    {
        return $this->club_address;
    }

    public function setClubAddress(?string $club_address): self
    {
        $this->club_address = $club_address;

        return $this;
    }

    public function getClubZip(): ?int
    {
        return $this->club_zip;
    }

    public function setClubZip(?int $club_zip): self
    {
        $this->club_zip = $club_zip;

        return $this;
    }

    public function getClubCity(): ?string
    {
        return $this->club_city;
    }

    public function setClubCity(?string $club_city): self
    {
        $this->club_city = $club_city;

        return $this;
    }

    public function getClubProvince(): ?string
    {
        return $this->club_province;
    }

    public function setClubProvince(?string $club_province): self
    {
        $this->club_province = $club_province;

        return $this;
    }

    public function getClubCreation(): ?DateTimeInterface
    {
        return $this->club_creation;
    }

    public function setClubCreation(?DateTimeInterface $club_creation): self
    {
        $this->club_creation = $club_creation;

        return $this;
    }

    public function getClubType(): ?int
    {
        return $this->club_type;
    }

    public function setClubType(?int $club_type): self
    {
        $this->club_type = $club_type;

        return $this;
    }

    public function getClubBceNumber(): ?string
    {
        return $this->club_bce_number;
    }

    public function setClubBceNumber(?string $club_bce_number): self
    {
        $this->club_bce_number = $club_bce_number;

        return $this;
    }

    public function getClubIban(): ?string
    {
        return $this->club_iban;
    }

    public function setClubIban(?string $club_iban): self
    {
        $this->club_iban = $club_iban;

        return $this;
    }

    public function getClubUrl(): ?string
    {
        return $this->club_url;
    }

    public function setClubUrl(?string $club_url): self
    {
        $this->club_url = $club_url;

        return $this;
    }

    public function getClubEmailPublic(): ?string
    {
        return $this->club_email_public;
    }

    public function setClubEmailPublic(?string $club_email_public): self
    {
        $this->club_email_public = $club_email_public;

        return $this;
    }

    public function getClubPresident(): ?string
    {
        return $this->club_president;
    }

    public function setClubPresident(?string $club_president): self
    {
        $this->club_president = $club_president;

        return $this;
    }

    public function getClubTreasurer(): ?string
    {
        return $this->club_treasurer;
    }

    public function setClubTreasurer(?string $club_treasurer): self
    {
        $this->club_treasurer = $club_treasurer;

        return $this;
    }

    public function getClubSecretary(): ?string
    {
        return $this->club_secretary;
    }

    public function setClubSecretary(?string $club_secretary): self
    {
        $this->club_secretary = $club_secretary;

        return $this;
    }

    public function getClubEmailContact(): ?string
    {
        return $this->club_email_contact;
    }

    public function setClubEmailContact(?string $club_email_contact): self
    {
        $this->club_email_contact = $club_email_contact;

        return $this;
    }

    public function getClubComment(): ?string
    {
        return $this->club_comment;
    }

    public function setClubComment(?string $club_comment): self
    {
        $this->club_comment = $club_comment;

        return $this;
    }

    public function getClubLastHistory(): ?ClubHistory
    {
        return $this->club_last_history;
    }

    public function setClubLastHistory(?ClubHistory $club_last_history): self
    {
        $this->club_last_history = $club_last_history;

        return $this;
    }

    public function getClubMainTeacher(): ?ClubTeacher
    {
        return $this->club_main_teacher;
    }

    public function setClubMainTeacher(?ClubTeacher $club_main_teacher): self
    {
        $this->club_main_teacher = $club_main_teacher;

        return $this;
    }

    /**
     * @return Collection|TrainingAddress[]
     */
    public function getClubAddresses(): Collection
    {
        return $this->club_addresses;
    }

    public function addClubAddresses(TrainingAddress $trainingAddress): self
    {
        if (!$this->club_addresses->contains($trainingAddress)) {
            $this->club_addresses[] = $trainingAddress;
            $trainingAddress->setClubAddressClub($this);
        }

        return $this;
    }

    public function removeClubAddresses(TrainingAddress $trainingAddress): self
    {
        if ($this->club_addresses->contains($trainingAddress)) {
            $this->club_addresses->removeElement($trainingAddress);
            // set the owning side to null (unless already changed)
            if ($trainingAddress->getClubAddressClub() === $this) {
                $trainingAddress->setClubAddressClub(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GradeDan[]
     */
    public function getClubGradesDan(): Collection
    {
        return $this->club_grades_dan;
    }

    public function addClubGradesDan(GradeDan $gradeDan): self
    {
        if (!$this->club_grades_dan->contains($gradeDan)) {
            $this->club_grades_dan[] = $gradeDan;
            $gradeDan->setGradeDanClub($this);
        }

        return $this;
    }

    public function removeClubGradesDan(GradeDan $gradeDan): self
    {
        if ($this->club_grades_dan->contains($gradeDan)) {
            $this->club_grades_dan->removeElement($gradeDan);
            // set the owning side to null (unless already changed)
            if ($gradeDan->getGradeDanClub() === $this) {
                $gradeDan->setGradeDanClub(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ClubHistory[]
     */
    public function getClubHistories(): Collection
    {
        return $this->club_histories;
    }

    public function addClubHistories(ClubHistory $clubHistory): self
    {
        if (!$this->club_histories->contains($clubHistory)) {
            $this->club_histories[] = $clubHistory;
            $clubHistory->setClubHistory($this);
        }

        return $this;
    }

    public function removeClubHistories(ClubHistory $clubHistory): self
    {
        if ($this->club_histories->contains($clubHistory)) {
            $this->club_histories->removeElement($clubHistory);
            // set the owning side to null (unless already changed)
            if ($clubHistory->getClubHistory() === $this) {
                $clubHistory->setClubHistory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MemberLicence[]
     */
    public function getClubLicences(): Collection
    {
        return $this->club_licences;
    }

    public function addClubLicences(MemberLicence $memberLicence): self
    {
        if (!$this->club_licences->contains($memberLicence)) {
            $this->club_licences[] = $memberLicence;
            $memberLicence->setMemberLicenceClub($this);
        }

        return $this;
    }

    public function removeClubLicences(MemberLicence $memberLicence): self
    {
        if ($this->club_licences->contains($memberLicence)) {
            $this->club_licences->removeElement($memberLicence);
            // set the owning side to null (unless already changed)
            if ($memberLicence->getMemberLicenceClub() === $this) {
                $memberLicence->setMemberLicenceClub(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ClubTeacher[]
     */
    public function getClubTeachers(): Collection
    {
        return $this->club_teachers;
    }

    public function addClubTeachers(ClubTeacher $clubTeacher): self
    {
        if (!$this->club_teachers->contains($clubTeacher)) {
            $this->club_teachers[] = $clubTeacher;
            $clubTeacher->setClubTeacher($this);
        }

        return $this;
    }

    public function removeClubTeachers(ClubTeacher $clubTeacher): self
    {
        if ($this->club_teachers->contains($clubTeacher)) {
            $this->club_teachers->removeElement($clubTeacher);
            // set the owning side to null (unless already changed)
            if ($clubTeacher->getClubTeacher() === $this) {
                $clubTeacher->setClubTeacher(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Training[]
     */
    public function getClubTrainings(): Collection
    {
        return $this->club_trainings;
    }

    public function addClubTrainings(Training $training): self
    {
        if (!$this->club_trainings->contains($training)) {
            $this->club_trainings[] = $training;
            $training->setTrainingClub($this);
        }

        return $this;
    }

    public function removeClubTrainings(Training $training): self
    {
        if ($this->club_trainings->contains($training)) {
            $this->club_trainings->removeElement($training);
            // set the owning side to null (unless already changed)
            if ($training->getTrainingClub() === $this) {
                $training->setTrainingClub(null);
            }
        }

        return $this;
    }
}
