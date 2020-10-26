<?php
// src/Entity/Member.php
namespace App\Entity;

use DateTimeInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="afamanager_member")
 * @ORM\Entity(repositoryClass="App\Repository\MemberRepository")
 */
class Member
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $member_id;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotBlank()
     */
    private $member_firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotBlank()
     */
    private $member_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $member_photo;

    /**
     * @ORM\Column(type="integer")
     * 
     * @Assert\NotBlank()
     */
    private $member_sex;

    /**
     * @ORM\Column(type="text")
     * 
     * @Assert\NotBlank()
     */
    private $member_address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Assert\NotBlank()
     */
    private $member_zip;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotBlank()
     */
    private $member_city;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotBlank()
     */
    private $member_country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\Email()
     */
    private $member_email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $member_phone;

    /**
     * @ORM\Column(type="date")
     */
    private $member_birthday;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $member_aikikai_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $member_comment;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\MemberLicence")
     * @ORM\JoinColumn(nullable=true, name="member_join_member_first_licence", referencedColumnName="member_licence_id")
     */
    private $member_first_licence;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\MemberLicence")
     * @ORM\JoinColumn(nullable=true, name="member_join_member_last_licence", referencedColumnName="member_licence_id")
     */
    private $member_last_licence;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club")
     * @ORM\JoinColumn(nullable=false, name="member_join_member_actual_club", referencedColumnName="club_id")
     */
    private $member_actual_club;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Grade", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="member_join_last_grade", referencedColumnName="grade_id")
     */
    private $member_last_grade;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\MemberModification", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true, name="member_join_member_modification", referencedColumnName="member_modification_id")
     */
    private $member_modification;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommissionMember", mappedBy="commission_member", orphanRemoval=true, cascade={"persist"})
     */
    private $member_commissions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Grade", mappedBy="grade_member", orphanRemoval=true, cascade={"persist"})
     */
    private $member_grades;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GradeTitle", mappedBy="grade_title_member", orphanRemoval=true, cascade={"persist"})
     */
    private $member_grades_title;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MemberLicence", mappedBy="member_licence", orphanRemoval=true, cascade={"persist"})
     */
    private $member_licences;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ClubTeacher", mappedBy="club_teacher_member", orphanRemoval=true, cascade={"persist"})
     */
    private $member_teachers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrainingAttendance", mappedBy="training_attendance_member", orphanRemoval=true, cascade={"persist"})
     */
    private $member_training_attendances;

    public function __construct()
    {
        $this->member_commissions          = new ArrayCollection();
        $this->member_grades               = new ArrayCollection();
        $this->member_grades_title         = new ArrayCollection();
        $this->member_licences             = new ArrayCollection();
        $this->member_teachers             = new ArrayCollection();
        $this->member_training_attendances = new ArrayCollection();
    }

    public function getMemberId(): ?int
    {
        return $this->member_id;
    }

    public function setMemberId(int $member_id): self
    {
        $this->member_id = $member_id;

        return $this;
    }

    public function getMemberFirstname(): ?string
    {
        return $this->member_firstname;
    }

    public function setMemberFirstname(string $member_firstname): self
    {
        $this->member_firstname = $member_firstname;

        return $this;
    }

    public function getMemberName(): ?string
    {
        return $this->member_name;
    }

    public function setMemberName(string $member_name): self
    {
        $this->member_name = $member_name;

        return $this;
    }

    public function getMemberPhoto(): ?string
    {
        return $this->member_photo;
    }

    public function setMemberPhoto(string $member_photo): self
    {
        $this->member_photo = $member_photo;

        return $this;
    }

    public function getMemberSex(): ?int
    {
        return $this->member_sex;
    }

    public function setMemberSex(int $member_sex): self
    {
        $this->member_sex = $member_sex;

        return $this;
    }

    public function getMemberAddress(): ?string
    {
        return $this->member_address;
    }

    public function setMemberAddress(string $member_address): self
    {
        $this->member_address = $member_address;

        return $this;
    }

    public function getMemberZip(): ?string
    {
        return $this->member_zip;
    }

    public function setMemberZip(string $member_zip): self
    {
        $this->member_zip = $member_zip;

        return $this;
    }

    public function getMemberCity(): ?string
    {
        return $this->member_city;
    }

    public function setMemberCity(string $member_city): self
    {
        $this->member_city = $member_city;

        return $this;
    }

    public function getMemberCountry(): ?string
    {
        return $this->member_country;
    }

    public function setMemberCountry(string $member_country): self
    {
        $this->member_country = $member_country;

        return $this;
    }

    public function getMemberEmail(): ?string
    {
        return $this->member_email;
    }

    public function setMemberEmail(?string $member_email): self
    {
        $this->member_email = $member_email;

        return $this;
    }

    public function getMemberPhone(): ?string
    {
        return $this->member_phone;
    }

    public function setMemberPhone(?string $member_phone): self
    {
        $this->member_phone = $member_phone;

        return $this;
    }

    public function getMemberBirthday(): ?DateTimeInterface
    {
        return $this->member_birthday;
    }

    public function setMemberBirthday(DateTimeInterface $member_birthday): self
    {
        $this->member_birthday = $member_birthday;

        return $this;
    }

    public function getMemberAikikaiId(): ?string
    {
        return $this->member_aikikai_id;
    }

    public function setMemberAikikaiId(?string $member_aikikai_id): self
    {
        $this->member_aikikai_id = $member_aikikai_id;

        return $this;
    }

    public function getMemberComment(): ?string
    {
        return $this->member_comment;
    }

    public function setMemberComment(string $member_comment): self
    {
        $this->member_comment = $member_comment;

        return $this;
    }

    public function getMemberFirstLicence(): ?MemberLicence
    {
        return $this->member_first_licence;
    }

    public function setMemberFirstLicence(?MemberLicence $member_first_licence): self
    {
        $this->member_first_licence = $member_first_licence;

        return $this;
    }

    public function getMemberLastLicence(): ?MemberLicence
    {
        return $this->member_last_licence;
    }

    public function setMemberLastLicence(?MemberLicence $member_last_licence): self
    {
        $this->member_last_licence = $member_last_licence;

        return $this;
    }

    public function getMemberActualClub(): ?Club
    {
        return $this->member_actual_club;
    }

    public function setMemberActualClub(?Club $member_actual_club): self
    {
        $this->member_actual_club = $member_actual_club;

        return $this;
    }

    public function getMemberLastGrade(): ?Grade
    {
        return $this->member_last_grade;
    }

    public function setMemberLastGrade(?Grade $member_last_grade): self
    {
        $this->member_last_grade = $member_last_grade;

        return $this;
    }

    public function getMemberModification(): ?MemberModification
    {
        return $this->member_modification;
    }

    public function setMemberModification(?MemberModification $member_modification): self
    {
        $this->member_modification = $member_modification;

        return $this;
    }

    /**
     * @return Collection|CommissionMember[]
     */
    public function getMemberCommissions(): Collection
    {
        return $this->member_commissions;
    }

    public function addMemberCommissions(CommissionMember $commissionMember): self
    {
        if (!$this->member_commissions->contains($commissionMember)) {
            $this->member_commissions[] = $commissionMember;
            $commissionMember->setCommissionMember($this);
        }

        return $this;
    }

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
     * @return Collection|Grade[]
     */
    public function getMemberGrades(): Collection
    {
        return $this->member_grades;
    }

    public function addMemberGrades(Grade $grade): self
    {
        if (!$this->member_grades->contains($grade)) {
            $this->member_grades[] = $grade;
            $grade->setGradeMember($this);
        }

        return $this;
    }

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
     * @return Collection|GradeTitle[]
     */
    public function getMemberGradesTitle(): Collection
    {
        return $this->member_grades_title;
    }

    public function addMemberGradesTitle(GradeTitle $gradeTitle): self
    {
        if (!$this->member_grades_title->contains($gradeTitle)) {
            $this->member_grades_title[] = $gradeTitle;
            $gradeTitle->setGradeTitleMember($this);
        }

        return $this;
    }

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
     * @return Collection|MemberLicence[]
     */
    public function getMemberLicences(): Collection
    {
        return $this->member_licences;
    }

    public function addMemberLicences(MemberLicence $memberLicence): self
    {
        if (!$this->member_licences->contains($memberLicence)) {
            $this->member_licences[] = $memberLicence;
            $memberLicence->setMemberLicence($this);
        }

        return $this;
    }

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
     * @return Collection|ClubTeacher[]
     */
    public function getMemberTeachers(): Collection
    {
        return $this->member_teachers;
    }

    public function addMemberTeachers(ClubTeacher $memberTeacher): self
    {
        if (!$this->member_teachers->contains($memberTeacher)) {
            $this->member_teachers[] = $memberTeacher;
            $memberTeacher->setClubTeacherMember($this);
        }

        return $this;
    }

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
     * @return Collection|TrainingAttendance[]
     */
    public function getMemberTrainingAttendances(): Collection
    {
        return $this->member_training_attendances;
    }

    public function addMemberTrainingAttendances(TrainingAttendance $trainingAttendance): self
    {
        if (!$this->member_training_attendances->contains($trainingAttendance)) {
            $this->member_training_attendances[] = $trainingAttendance;
            $trainingAttendance->setTrainingAttendanceMember($this);
        }

        return $this;
    }

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
