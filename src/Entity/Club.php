<?php
// src/Entity/Club.php
namespace App\Entity;

use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Club
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_club")
 * @ORM\Entity(repositoryClass="App\Repository\ClubRepository")
 */
class Club
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private int $club_id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    private string $club_name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $club_address;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $club_zip;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $club_city;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $club_province;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $club_creation;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $club_type;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $club_bce_number;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $club_iban;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Assert\Url]
    private ?string $club_url;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Assert\Email]
    private ?string $club_email_public;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $club_name_contact;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Assert\Email]
    private ?string $club_email_contact;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $club_phone_contact;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $club_address_contact;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $club_zip_contact;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $club_city_contact;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $club_comment;

    /**
     * @var ClubHistory|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\ClubHistory")
     * @ORM\JoinColumn(nullable=true, name="club_join_club_last_history", referencedColumnName="club_history_id")
     */
    private ?ClubHistory $club_last_history;

    /**
     * @var ClubTeacher|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\ClubTeacher")
     * @ORM\JoinColumn(nullable=true, name="club_join_club_main_teacher", referencedColumnName="club_teacher_id")
     */
    private ?ClubTeacher $club_main_teacher;

    /**
     * @var Collection|ArrayCollection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ClubDojo", mappedBy="club_dojo_club", orphanRemoval=true, cascade={"persist"})
     */
    private Collection|ArrayCollection|null $club_dojos;

    /**
     * @var Collection|ArrayCollection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Grade", mappedBy="grade_club", orphanRemoval=true, cascade={"persist"})
     */
    private Collection|ArrayCollection|null $club_grades;

    /**
     * @var Collection|ArrayCollection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ClubHistory", mappedBy="club_history", orphanRemoval=true, cascade={"persist"})
     */
    private Collection|ArrayCollection|null $club_histories;

    /**
     * @var Collection|ArrayCollection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\MemberLicence", mappedBy="member_licence_club", orphanRemoval=true, cascade={"persist"})
     */
    private Collection|ArrayCollection|null $club_licences;

    /**
     * @var Collection|ArrayCollection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ClubTeacher", mappedBy="club_teacher", orphanRemoval=true, cascade={"persist"})
     */
    private Collection|ArrayCollection|null $club_teachers;

    /**
     * @var Collection|ArrayCollection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ClubLesson", mappedBy="club_lesson_club", orphanRemoval=true, cascade={"persist"})
     */
    private Collection|ArrayCollection|null $club_lessons;

    /**
     * @var Collection|ArrayCollection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Training", mappedBy="training_club", orphanRemoval=true, cascade={"persist"})
     */
    private Collection|ArrayCollection|null $club_trainings;

    /**
     * @var Collection|ArrayCollection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\UserAccess", mappedBy="user_access_club", orphanRemoval=true, cascade={"persist"})
     */
    private Collection|ArrayCollection|null $club_accesses;

    /**
     * @var Collection|ArrayCollection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\UserAuditTrail", mappedBy="user_audit_trail_club", orphanRemoval=true, cascade={"persist"})
     */
    private Collection|ArrayCollection|null $club_user_audit_trails;

    /**
     * Club constructor.
     */
    public function __construct()
    {
        $this->club_dojos             = new ArrayCollection();
        $this->club_grades            = new ArrayCollection();
        $this->club_histories         = new ArrayCollection();
        $this->club_lessons           = new ArrayCollection();
        $this->club_licences          = new ArrayCollection();
        $this->club_teachers          = new ArrayCollection();
        $this->club_trainings         = new ArrayCollection();
        $this->club_accesses          = new ArrayCollection();
        $this->club_user_audit_trails = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getClubId(): ?int
    {
        return $this->club_id;
    }

    /**
     * @param int $club_id
     * @return $this
     */
    public function setClubId(int $club_id): self
    {
        $this->club_id = $club_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubName(): ?string
    {
        return $this->club_name;
    }

    /**
     * @param string $club_name
     * @return $this
     */
    public function setClubName(string $club_name): self
    {
        $this->club_name = $club_name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubAddress(): ?string
    {
        return $this->club_address;
    }

    /**
     * @param string|null $club_address
     * @return $this
     */
    public function setClubAddress(?string $club_address): self
    {
        $this->club_address = $club_address;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubZip(): ?int
    {
        return $this->club_zip;
    }

    /**
     * @param int|null $club_zip
     * @return $this
     */
    public function setClubZip(?int $club_zip): self
    {
        $this->club_zip = $club_zip;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubCity(): ?string
    {
        return $this->club_city;
    }

    /**
     * @param string|null $club_city
     * @return $this
     */
    public function setClubCity(?string $club_city): self
    {
        $this->club_city = $club_city;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubProvince(): ?int
    {
        return $this->club_province;
    }

    /**
     * @param int|null $club_province
     * @return $this
     */
    public function setClubProvince(?int $club_province): self
    {
        $this->club_province = $club_province;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getClubCreation(): ?DateTime
    {
        return $this->club_creation;
    }

    /**
     * @param DateTime|null $club_creation
     * @return $this
     */
    public function setClubCreation(?DateTime $club_creation): self
    {
        $this->club_creation = $club_creation;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubType(): ?int
    {
        return $this->club_type;
    }

    /**
     * @param int|null $club_type
     * @return $this
     */
    public function setClubType(?int $club_type): self
    {
        $this->club_type = $club_type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubBceNumber(): ?string
    {
        return $this->club_bce_number;
    }

    /**
     * @param string|null $club_bce_number
     * @return $this
     */
    public function setClubBceNumber(?string $club_bce_number): self
    {
        $this->club_bce_number = $club_bce_number;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubIban(): ?string
    {
        return $this->club_iban;
    }

    /**
     * @param string|null $club_iban
     * @return $this
     */
    public function setClubIban(?string $club_iban): self
    {
        $this->club_iban = $club_iban;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubUrl(): ?string
    {
        return $this->club_url;
    }

    /**
     * @param string|null $club_url
     * @return $this
     */
    public function setClubUrl(?string $club_url): self
    {
        $this->club_url = $club_url;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubEmailPublic(): ?string
    {
        return $this->club_email_public;
    }

    /**
     * @param string|null $club_email_public
     * @return $this
     */
    public function setClubEmailPublic(?string $club_email_public): self
    {
        $this->club_email_public = $club_email_public;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubNameContact(): ?string
    {
        return $this->club_name_contact;
    }

    /**
     * @param string|null $club_name_contact
     * @return $this
     */
    public function setClubNameContact(?string $club_name_contact): self
    {
        $this->club_name_contact = $club_name_contact;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubEmailContact(): ?string
    {
        return $this->club_email_contact;
    }

    /**
     * @param string|null $club_email_contact
     * @return $this
     */
    public function setClubEmailContact(?string $club_email_contact): self
    {
        $this->club_email_contact = $club_email_contact;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubPhoneContact(): ?string
    {
        return $this->club_phone_contact;
    }

    /**
     * @param string|null $club_phone_contact
     * @return $this
     */
    public function setClubPhoneContact(?string $club_phone_contact): self
    {
        $this->club_phone_contact = $club_phone_contact;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubAddressContact(): ?string
    {
        return $this->club_address_contact;
    }

    /**
     * @param string|null $club_address_contact
     * @return $this
     */
    public function setClubAddressContact(?string $club_address_contact): self
    {
        $this->club_address_contact = $club_address_contact;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubZipContact(): ?int
    {
        return $this->club_zip_contact;
    }

    /**
     * @param int|null $club_zip_contact
     * @return $this
     */
    public function setClubZipContact(?int $club_zip_contact): self
    {
        $this->club_zip_contact = $club_zip_contact;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubCityContact(): ?string
    {
        return $this->club_city_contact;
    }

    /**
     * @param string|null $club_city_contact
     * @return $this
     */
    public function setClubCityContact(?string $club_city_contact): self
    {
        $this->club_city_contact = $club_city_contact;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubComment(): ?string
    {
        return $this->club_comment;
    }

    /**
     * @param string|null $club_comment
     * @return $this
     */
    public function setClubComment(?string $club_comment): self
    {
        $this->club_comment = $club_comment;

        return $this;
    }

    /**
     * @return ClubHistory|null
     */
    public function getClubLastHistory(): ?ClubHistory
    {
        return $this->club_last_history;
    }

    /**
     * @param ClubHistory|null $club_last_history
     * @return $this
     */
    public function setClubLastHistory(?ClubHistory $club_last_history): self
    {
        $this->club_last_history = $club_last_history;

        return $this;
    }

    /**
     * @return ClubTeacher|null
     */
    public function getClubMainTeacher(): ?ClubTeacher
    {
        return $this->club_main_teacher;
    }

    /**
     * @param ClubTeacher|null $club_main_teacher
     * @return $this
     */
    public function setClubMainTeacher(?ClubTeacher $club_main_teacher): self
    {
        $this->club_main_teacher = $club_main_teacher;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClubDojos(): Collection
    {
        return $this->club_dojos;
    }

    /**
     * @param ClubDojo $clubDojo
     * @return $this
     */
    public function addClubDojos(ClubDojo $clubDojo): self
    {
        if (!$this->club_dojos->contains($clubDojo)) {
            $this->club_dojos[] = $clubDojo;
            $clubDojo->setClubDojoClub($this);
        }

        return $this;
    }

    /**
     * @param ClubDojo $clubDojo
     * @return $this
     */
    public function removeClubDojos(ClubDojo $clubDojo): self
    {
        if ($this->club_dojos->contains($clubDojo)) {
            $this->club_dojos->removeElement($clubDojo);
            // set the owning side to null (unless already changed)
            if ($clubDojo->getClubDojoClub() === $this) {
                $clubDojo->setClubDojoClub(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClubGrades(): Collection
    {
        return $this->club_grades;
    }

    /**
     * @param Grade $grade
     * @return $this
     */
    public function addClubGrades(Grade $grade): self
    {
        if (!$this->club_grades->contains($grade)) {
            $this->club_grades[] = $grade;
            $grade->setGradeClub($this);
        }

        return $this;
    }

    /**
     * @param Grade $grade
     * @return $this
     */
    public function removeClubGrades(Grade $grade): self
    {
        if ($this->club_grades->contains($grade)) {
            $this->club_grades->removeElement($grade);
            // set the owning side to null (unless already changed)
            if ($grade->getGradeClub() === $this) {
                $grade->setGradeClub(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClubHistories(): Collection
    {
        return $this->club_histories;
    }

    /**
     * @param ClubHistory $clubHistory
     * @return $this
     */
    public function addClubHistories(ClubHistory $clubHistory): self
    {
        if (!$this->club_histories->contains($clubHistory)) {
            $this->club_histories[] = $clubHistory;
            $clubHistory->setClubHistory($this);
        }

        return $this;
    }

    /**
     * @param ClubHistory $clubHistory
     * @return $this
     */
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
     * @return Collection
     */
    public function getClubLicences(): Collection
    {
        return $this->club_licences;
    }

    /**
     * @param MemberLicence $memberLicence
     * @return $this
     */
    public function addClubLicences(MemberLicence $memberLicence): self
    {
        if (!$this->club_licences->contains($memberLicence)) {
            $this->club_licences[] = $memberLicence;
            $memberLicence->setMemberLicenceClub($this);
        }

        return $this;
    }

    /**
     * @param MemberLicence $memberLicence
     * @return $this
     */
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
     * @return Collection
     */
    public function getClubTeachers(): Collection
    {
        return $this->club_teachers;
    }

    /**
     * @param ClubTeacher $clubTeacher
     * @return $this
     */
    public function addClubTeachers(ClubTeacher $clubTeacher): self
    {
        if (!$this->club_teachers->contains($clubTeacher)) {
            $this->club_teachers[] = $clubTeacher;
            $clubTeacher->setClubTeacher($this);
        }

        return $this;
    }

    /**
     * @param ClubTeacher $clubTeacher
     * @return $this
     */
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
     * @return Collection
     */
    public function getClubLessons(): Collection
    {
        return $this->club_lessons;
    }

    /**
     * @param ClubLesson $clubLesson
     * @return $this
     */
    public function addClubLessons(ClubLesson $clubLesson): self
    {
        if (!$this->club_lessons->contains($clubLesson)) {
            $this->club_lessons[] = $clubLesson;
            $clubLesson->setClubLessonClub($this);
        }

        return $this;
    }

    /**
     * @param ClubLesson $clubLesson
     * @return $this
     */
    public function removeClubLessons(ClubLesson $clubLesson): self
    {
        if ($this->club_lessons->contains($clubLesson)) {
            $this->club_lessons->removeElement($clubLesson);
            // set the owning side to null (unless already changed)
            if ($clubLesson->getClubLessonClub() === $this) {
                $clubLesson->setClubLessonClub(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClubTrainings(): Collection
    {
        return $this->club_trainings;
    }

    /**
     * @param Training $training
     * @return $this
     */
    public function addClubTrainings(Training $training): self
    {
        if (!$this->club_trainings->contains($training)) {
            $this->club_trainings[] = $training;
            $training->setTrainingClub($this);
        }

        return $this;
    }

    /**
     * @param Training $training
     * @return $this
     */
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

    /**
     * @return Collection
     */
    public function getClubAccesses(): Collection
    {
        return $this->club_accesses;
    }

    /**
     * @param UserAccess $userAccess
     * @return $this
     */
    public function addClubAccesses(UserAccess $userAccess): self
    {
        if (!$this->club_accesses->contains($userAccess)) {
            $this->club_accesses[] = $userAccess;
            $userAccess->setUserAccessClub($this);
        }

        return $this;
    }

    /**
     * @param UserAccess $userAccess
     * @return $this
     */
    public function removeClubAccesses(UserAccess $userAccess): self
    {
        if ($this->club_accesses->contains($userAccess)) {
            $this->club_accesses->removeElement($userAccess);
            // set the owning side to null (unless already changed)
            if ($userAccess->getUserAccessClub() === $this) {
                $userAccess->setUserAccessClub(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClubUserAuditTrails(): Collection
    {
        return $this->club_user_audit_trails;
    }

    /**
     * @param UserAuditTrail $userAuditTrail
     * @return $this
     */
    public function addClubUserAuditTrails(UserAuditTrail $userAuditTrail): self
    {
        if (!$this->club_user_audit_trails->contains($userAuditTrail)) {
            $this->club_user_audit_trails[] = $userAuditTrail;
            $userAuditTrail->setUserAuditTrailClub($this);
        }

        return $this;
    }

    /**
     * @param UserAuditTrail $userAuditTrail
     * @return $this
     */
    public function removeClubUserAuditTrails(UserAuditTrail $userAuditTrail): self
    {
        if ($this->club_user_audit_trails->contains($userAuditTrail)) {
            $this->club_user_audit_trails->removeElement($userAuditTrail);
            // set the owning side to null (unless already changed)
            if ($userAuditTrail->getUserAuditTrailClub() === $this) {
                $userAuditTrail->setUserAuditTrailClub(null);
            }
        }

        return $this;
    }
}
