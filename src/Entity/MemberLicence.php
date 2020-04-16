<?php
// src/Entity/MemberLicence.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="afamanager_member_licence")
 * @ORM\Entity(repositoryClass="App\Repository\MemberLicenceRepository")
 */
class MemberLicence
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $member_licence_id;

    /**
     * @ORM\Column(type="date")
     */
    private $member_licence_update;
    
    /**
     * @ORM\Column(type="date")
     */
    private $member_licence_deadline;
    
    /**
     * @ORM\Column(type="date")
     */
    private $member_licence_medical_certificate;

    /**
     * @ORM\Column(type="integer")
     */
    private $member_licence_status;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\GradeKyu")
     * @ORM\JoinColumn(nullable=true, name="member_licence_join_grade_kyu", referencedColumnName="grade_kyu_id")
     */
    private $member_licence_grade_kyu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_licences", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="member_licence_join_club", referencedColumnName="club_id")
     */
    private $member_licence_club;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="member_licences", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="member_licence_join_member", referencedColumnName="member_id")
     */
    private $member_licence;

    public function getMemberLicenceId()
    {
        return $this->member_licence_id;
    }

    public function getMemberLicenceUpdate(): ?\DateTimeInterface
    {
        return $this->member_licence_update;
    }

    public function setMemberLicenceUpdate(\DateTimeInterface $member_licence_update): self
    {
        $this->member_licence_update = $member_licence_update;

        return $this;
    }

    public function getMemberLicenceDeadline(): ?\DateTimeInterface
    {
        return $this->member_licence_deadline;
    }

    public function setMemberLicenceDeadline(\DateTimeInterface $member_licence_deadline): self
    {
        $this->member_licence_deadline = $member_licence_deadline;

        return $this;
    }

    public function getMemberLicenceMedicalCertificate(): ?\DateTimeInterface
    {
        return $this->member_licence_medical_certificate;
    }

    public function setMemberLicenceMedicalCertificate(\DateTimeInterface $member_licence_medical_certificate): self
    {
        $this->member_licence_medical_certificate = $member_licence_medical_certificate;

        return $this;
    }

    public function getMemberLicenceStatus(): ?int
    {
        return $this->member_licence_status;
    }

    public function setMemberLicenceStatus(int $member_licence_status): self
    {
        $this->member_licence_status = $member_licence_status;

        return $this;
    }

    public function getMemberLicenceGradeKyu(): ?GradeKyu
    {
        return $this->member_licence_grade_kyu;
    }

    public function setMemberLicenceGradeKyu(?GradeKyu $member_licence_grade_kyu): self
    {
        $this->member_licence_grade_kyu = $member_licence_grade_kyu;

        return $this;
    }

    public function getMemberLicenceClub(): ?Club
    {
        return $this->member_licence_club;
    }

    public function setMemberLicenceClub(?Club $member_licence_club): self
    {
        $this->member_licence_club = $member_licence_club;

        return $this;
    }

    public function getMemberLicence(): ?Member
    {
        return $this->member_licence;
    }

    public function setMemberLicence(?Member $member_licence): self
    {
        $this->member_licence = $member_licence;

        return $this;
    }
}
