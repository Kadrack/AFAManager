<?php
// src/Entity/Member.php
namespace App\Entity;

use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Member
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_member")
 * @ORM\Entity(repositoryClass="App\Repository\MemberRepository")
 */
class Member
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $member_id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    private string $member_firstname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    private string $member_name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $member_photo;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank]
    private int $member_sex;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    #[Assert\NotBlank]
    private string $member_address;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Assert\NotBlank]
    private ?string $member_zip;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    private string $member_city;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    private string $member_country;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Assert\Email]
    private ?string $member_email;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $member_phone;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private DateTime $member_birthday;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private DateTime $member_start_practice;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $member_aikikai_id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $member_comment;

    /**
     * @var MemberLicence|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\MemberLicence")
     * @ORM\JoinColumn(nullable=true, name="member_join_member_last_licence", referencedColumnName="member_licence_id")
     */
    private ?MemberLicence $member_last_licence;

    /**
     * @var Club
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Club")
     * @ORM\JoinColumn(nullable=false, name="member_join_member_actual_club", referencedColumnName="club_id")
     */
    private Club $member_actual_club;

    /**
     * @var Grade|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Grade", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="member_join_last_grade", referencedColumnName="grade_id")
     */
    private ?Grade $member_last_grade;

    /**
     * @var MemberModification|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\MemberModification", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true, name="member_join_member_modification", referencedColumnName="member_modification_id")
     */
    private ?MemberModification $member_modification;

    /**
     * @var ArrayCollection|Collection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\CommissionMember", mappedBy="commission_member", orphanRemoval=true, cascade={"persist"})
     */
    private ArrayCollection|Collection|null $member_commissions;

    /**
     * @var ArrayCollection|Collection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Grade", mappedBy="grade_member", orphanRemoval=true, cascade={"persist"})
     */
    private ArrayCollection|Collection|null $member_grades;

    /**
     * @var ArrayCollection|Collection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\GradeTitle", mappedBy="grade_title_member", orphanRemoval=true, cascade={"persist"})
     */
    private ArrayCollection|Collection|null $member_grades_title;

    /**
     * @var ArrayCollection|Collection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\MemberLicence", mappedBy="member_licence", orphanRemoval=true, cascade={"persist"})
     */
    private ArrayCollection|Collection|null $member_licences;

    /**
     * @var ArrayCollection|Collection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ClubTeacher", mappedBy="club_teacher_member", orphanRemoval=true, cascade={"persist"})
     */
    private ArrayCollection|Collection|null $member_teachers;

    /**
     * @var ArrayCollection|Collection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\TrainingAttendance", mappedBy="training_attendance_member", orphanRemoval=true, cascade={"persist"})
     */
    private ArrayCollection|Collection|null $member_training_attendances;

    /**
     * Member constructor.
     */
    public function __construct()
    {
        $this->member_commissions          = new ArrayCollection();
        $this->member_grades               = new ArrayCollection();
        $this->member_grades_title         = new ArrayCollection();
        $this->member_licences             = new ArrayCollection();
        $this->member_teachers             = new ArrayCollection();
        $this->member_training_attendances = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getMemberId(): int
    {
        return $this->member_id;
    }

    /**
     * @param int $member_id
     * @return $this
     */
    public function setMemberId(int $member_id): self
    {
        $this->member_id = $member_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getMemberFirstname(): string
    {
        return $this->member_firstname;
    }

    /**
     * @param string $member_firstname
     * @return $this
     */
    public function setMemberFirstname(string $member_firstname): self
    {
        $this->member_firstname = $member_firstname;

        return $this;
    }

    /**
     * @return string
     */
    public function getMemberName(): string
    {
        return $this->member_name;
    }

    /**
     * @param string $member_name
     * @return $this
     */
    public function setMemberName(string $member_name): self
    {
        $this->member_name = $member_name;

        return $this;
    }

    /**
     * @return string
     */
    public function getMemberPhoto(): string
    {
        return $this->member_photo;
    }

    /**
     * @param string $member_photo
     * @return $this
     */
    public function setMemberPhoto(string $member_photo): self
    {
        $this->member_photo = $member_photo;

        return $this;
    }

    /**
     * @return int
     */
    public function getMemberSex(): int
    {
        return $this->member_sex;
    }

    public function setMemberSex(int $member_sex): self
    {
        $this->member_sex = $member_sex;

        return $this;
    }

    /**
     * @return string
     */
    public function getMemberAddress(): string
    {
        return $this->member_address;
    }

    /**
     * @param string $member_address
     * @return $this
     */
    public function setMemberAddress(string $member_address): self
    {
        $this->member_address = $member_address;

        return $this;
    }

    /**
     * @return string
     */
    public function getMemberZip(): string
    {
        return $this->member_zip;
    }

    /**
     * @param string $member_zip
     * @return $this
     */
    public function setMemberZip(string $member_zip): self
    {
        $this->member_zip = $member_zip;

        return $this;
    }

    /**
     * @return string
     */
    public function getMemberCity(): string
    {
        return $this->member_city;
    }

    /**
     * @param string $member_city
     * @return $this
     */
    public function setMemberCity(string $member_city): self
    {
        $this->member_city = $member_city;

        return $this;
    }

    /**
     * @return string
     */
    public function getMemberCountry(): string
    {
        return $this->member_country;
    }

    /**
     * @param string $member_country
     * @return $this
     */
    public function setMemberCountry(string $member_country): self
    {
        $this->member_country = $member_country;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberEmail(): ?string
    {
        return $this->member_email;
    }

    /**
     * @param string|null $member_email
     * @return $this
     */
    public function setMemberEmail(?string $member_email): self
    {
        $this->member_email = $member_email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberPhone(): ?string
    {
        return $this->member_phone;
    }

    public function getMemberPhoneFormated(): ?string
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $this->member_phone);

        if (strlen($phoneNumber) > 10) {
            $countryCode = substr($phoneNumber, 0, strlen($phoneNumber) - 10);
            $areaCode = substr($phoneNumber, -10, 3);
            $nextThree = substr($phoneNumber, -7, 3);
            $lastFour = substr($phoneNumber, -4, 4);

            $phoneNumber = '+' . $countryCode . ' (' . $areaCode . ') ' . $nextThree . '-' . $lastFour;
        } else if (strlen($phoneNumber) == 10) {
            $areaCode = substr($phoneNumber, 0, 4);
            $firstTwo = substr($phoneNumber, 4, 2);
            $secondTwo = substr($phoneNumber, 6, 2);
            $thirdTwo = substr($phoneNumber, 8, 2);

            $phoneNumber = $areaCode . '/' . $firstTwo . ' ' . $secondTwo . ' ' . $thirdTwo;
        } else if (strlen($phoneNumber) == 9) {
            $twoFirst = substr($phoneNumber, 0, 2);
            $nextThree = substr($phoneNumber, 2, 3);
            $firstTwo = substr($phoneNumber, 5, 2);
            $secondTwo = substr($phoneNumber, 7, 2);

            $phoneNumber = $twoFirst . '/' . $nextThree . ' ' . $firstTwo . ' ' . $secondTwo;
        }

        return $phoneNumber;
    }

    /**
     * @param string|null $member_phone
     * @return $this
     */
    public function setMemberPhone(?string $member_phone): self
    {
        $this->member_phone = $member_phone;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getMemberBirthday(): DateTime
    {
        return $this->member_birthday;
    }

    /**
     * @param DateTime $member_birthday
     * @return $this
     */
    public function setMemberBirthday(DateTime $member_birthday): self
    {
        $this->member_birthday = $member_birthday;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getMemberStartPractice(): DateTime
    {
        return $this->member_start_practice;
    }

    /**
     * @param DateTime $member_start_practice
     * @return $this
     */
    public function setMemberStartPractice(DateTime $member_start_practice): self
    {
        $this->member_start_practice = $member_start_practice;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberAikikaiId(): ?string
    {
        return $this->member_aikikai_id;
    }

    /**
     * @param string|null $member_aikikai_id
     * @return $this
     */
    public function setMemberAikikaiId(?string $member_aikikai_id): self
    {
        $this->member_aikikai_id = $member_aikikai_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberComment(): ?string
    {
        return $this->member_comment;
    }

    /**
     * @param string|null $member_comment
     * @return $this
     */
    public function setMemberComment(?string $member_comment): self
    {
        $this->member_comment = $member_comment;

        return $this;
    }

    /**
     * @return MemberLicence|null
     */
    public function getMemberLastLicence(): ?MemberLicence
    {
        return $this->member_last_licence;
    }

    /**
     * @param MemberLicence|null $member_last_licence
     * @return $this
     */
    public function setMemberLastLicence(?MemberLicence $member_last_licence): self
    {
        $this->member_last_licence = $member_last_licence;

        return $this;
    }

    /**
     * @return Club
     */
    public function getMemberActualClub(): Club
    {
        return $this->member_actual_club;
    }

    /**
     * @param Club $member_actual_club
     * @return $this
     */
    public function setMemberActualClub(Club $member_actual_club): self
    {
        $this->member_actual_club = $member_actual_club;

        return $this;
    }

    /**
     * @return Grade|null
     */
    public function getMemberLastGrade(): ?Grade
    {
        return $this->member_last_grade;
    }

    /**
     * @param Grade|null $member_last_grade
     * @return $this
     */
    public function setMemberLastGrade(?Grade $member_last_grade): self
    {
        $this->member_last_grade = $member_last_grade;

        return $this;
    }

    /**
     * @return MemberModification|null
     */
    public function getMemberModification(): ?MemberModification
    {
        return $this->member_modification;
    }

    /**
     * @param MemberModification|null $member_modification
     * @return $this
     */
    public function setMemberModification(?MemberModification $member_modification): self
    {
        $this->member_modification = $member_modification;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMemberCommissions(): Collection
    {
        return $this->member_commissions;
    }

    /**
     * @param CommissionMember $commissionMember
     * @return $this
     */
    public function addMemberCommissions(CommissionMember $commissionMember): self
    {
        if (!$this->member_commissions->contains($commissionMember)) {
            $this->member_commissions[] = $commissionMember;
            $commissionMember->setCommissionMember($this);
        }

        return $this;
    }

    /**
     * @param CommissionMember $commissionMember
     * @return $this
     */
    public function removeMemberCommissions(CommissionMember $commissionMember): self
    {
        if ($this->member_commissions->contains($commissionMember)) {
            $this->member_commissions->removeElement($commissionMember);
            // set the owning side to null (unless already changed)
            if ($commissionMember->getCommissionMember() === $this) {
                $commissionMember->setCommissionMember(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMemberGrades(): Collection
    {
        return $this->member_grades;
    }

    /**
     * @param Grade $grade
     * @return $this
     */
    public function addMemberGrades(Grade $grade): self
    {
        if (!$this->member_grades->contains($grade)) {
            $this->member_grades[] = $grade;
            $grade->setGradeMember($this);
        }

        return $this;
    }

    /**
     * @param Grade $grade
     * @return $this
     */
    public function removeMemberGrades(Grade $grade): self
    {
        if ($this->member_grades->contains($grade)) {
            $this->member_grades->removeElement($grade);
            // set the owning side to null (unless already changed)
            if ($grade->getGradeMember() === $this) {
                $grade->setGradeMember(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMemberGradesTitle(): Collection
    {
        return $this->member_grades_title;
    }

    /**
     * @param GradeTitle $gradeTitle
     * @return $this
     */
    public function addMemberGradesTitle(GradeTitle $gradeTitle): self
    {
        if (!$this->member_grades_title->contains($gradeTitle)) {
            $this->member_grades_title[] = $gradeTitle;
            $gradeTitle->setGradeTitleMember($this);
        }

        return $this;
    }

    /**
     * @param GradeTitle $gradeTitle
     * @return $this
     */
    public function removeMemberGradesTitle(GradeTitle $gradeTitle): self
    {
        if ($this->member_grades_title->contains($gradeTitle)) {
            $this->member_grades_title->removeElement($gradeTitle);
            // set the owning side to null (unless already changed)
            if ($gradeTitle->getGradeTitleMember() === $this) {
                $gradeTitle->setGradeTitleMember(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMemberLicences(): Collection
    {
        return $this->member_licences;
    }

    /**
     * @param MemberLicence $memberLicence
     * @return $this
     */
    public function addMemberLicences(MemberLicence $memberLicence): self
    {
        if (!$this->member_licences->contains($memberLicence)) {
            $this->member_licences[] = $memberLicence;
            $memberLicence->setMemberLicence($this);
        }

        return $this;
    }

    /**
     * @param MemberLicence $memberLicence
     * @return $this
     */
    public function removeMemberLicences(MemberLicence $memberLicence): self
    {
        if ($this->member_licences->contains($memberLicence)) {
            $this->member_licences->removeElement($memberLicence);
            // set the owning side to null (unless already changed)
            if ($memberLicence->getMemberLicence() === $this) {
                $memberLicence->setMemberLicence(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMemberTeachers(): Collection
    {
        return $this->member_teachers;
    }

    /**
     * @param ClubTeacher $memberTeacher
     * @return $this
     */
    public function addMemberTeachers(ClubTeacher $memberTeacher): self
    {
        if (!$this->member_teachers->contains($memberTeacher)) {
            $this->member_teachers[] = $memberTeacher;
            $memberTeacher->setClubTeacherMember($this);
        }

        return $this;
    }

    /**
     * @param ClubTeacher $memberTeacher
     * @return $this
     */
    public function removeMemberTeachers(ClubTeacher $memberTeacher): self
    {
        if ($this->member_teachers->contains($memberTeacher)) {
            $this->member_teachers->removeElement($memberTeacher);
            // set the owning side to null (unless already changed)
            if ($memberTeacher->getClubTeacherMember() === $this) {
                $memberTeacher->setClubTeacherMember(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMemberTrainingAttendances(): Collection
    {
        return $this->member_training_attendances;
    }

    /**
     * @param TrainingAttendance $trainingAttendance
     * @return $this
     */
    public function addMemberTrainingAttendances(TrainingAttendance $trainingAttendance): self
    {
        if (!$this->member_training_attendances->contains($trainingAttendance)) {
            $this->member_training_attendances[] = $trainingAttendance;
            $trainingAttendance->setTrainingAttendanceMember($this);
        }

        return $this;
    }

    /**
     * @param TrainingAttendance $trainingAttendance
     * @return $this
     */
    public function removeMemberTrainingAttendances(TrainingAttendance $trainingAttendance): self
    {
        if ($this->member_training_attendances->contains($trainingAttendance)) {
            $this->member_training_attendances->removeElement($trainingAttendance);
            // set the owning side to null (unless already changed)
            if ($trainingAttendance->getTrainingAttendanceMember() === $this) {
                $trainingAttendance->setTrainingAttendanceMember(null);
            }
        }

        return $this;
    }
}
