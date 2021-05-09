<?php
// src/Entity/MemberLicence.php
namespace App\Entity;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class MemberLicence
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_member_licence")
 * @ORM\Entity(repositoryClass="App\Repository\MemberLicenceRepository")
 */
class MemberLicence
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $member_licence_id;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private DateTime $member_licence_update;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private DateTime $member_licence_deadline;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private DateTime $member_licence_medical_certificate;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private DateTime $member_licence_payment_date;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $member_licence_payment_value;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private int $member_licence_status;

    /**
     * @var Grade|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Grade")
     * @ORM\JoinColumn(nullable=true, name="member_licence_join_grade", referencedColumnName="grade_id")
     */
    private ?Grade $member_licence_grade;

    /**
     * @var Club
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_licences", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="member_licence_join_club", referencedColumnName="club_id")
     */
    private Club $member_licence_club;

    /**
     * @var Member
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="member_licences", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="member_licence_join_member", referencedColumnName="member_id")
     */
    private Member $member_licence;

    /**
     * @return int
     */
    public function getMemberLicenceId(): int
    {
        return $this->member_licence_id;
    }

    /**
     * @param int $member_licence_id
     * @return $this
     */
    public function setMemberLicenceId(int $member_licence_id): self
    {
        $this->member_licence_id = $member_licence_id;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getMemberLicenceUpdate(): DateTime
    {
        return $this->member_licence_update;
    }

    /**
     * @param DateTime $member_licence_update
     * @return $this
     */
    public function setMemberLicenceUpdate(DateTime $member_licence_update): self
    {
        $this->member_licence_update = $member_licence_update;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getMemberLicenceDeadline(): DateTime
    {
        return $this->member_licence_deadline;
    }

    /**
     * @param DateTime $member_licence_deadline
     * @return $this
     */
    public function setMemberLicenceDeadline(DateTime $member_licence_deadline): self
    {
        $this->member_licence_deadline = $member_licence_deadline;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getMemberLicenceMedicalCertificate(): DateTime
    {
        return $this->member_licence_medical_certificate;
    }

    /**
     * @param DateTime $member_licence_medical_certificate
     * @return $this
     */
    public function setMemberLicenceMedicalCertificate(DateTime $member_licence_medical_certificate): self
    {
        $this->member_licence_medical_certificate = $member_licence_medical_certificate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getMemberLicencePaymentDate(): DateTime
    {
        return $this->member_licence_payment_date;
    }

    /**
     * @param DateTime $member_licence_payment_date
     * @return $this
     */
    public function setMemberLicencePaymentDate(DateTime $member_licence_payment_date): self
    {
        $this->member_licence_payment_date = $member_licence_payment_date;

        return $this;
    }

    /**
     * @return int
     */
    public function getMemberLicencePaymentValue(): int
    {
        return $this->member_licence_payment_value;
    }

    /**
     * @param int $member_licence_payment_value
     * @return $this
     */
    public function setMemberLicencePaymentValue(int $member_licence_payment_value): self
    {
        $this->member_licence_payment_value = $member_licence_payment_value;

        return $this;
    }

    /**
     * @return int
     */
    public function getMemberLicenceStatus(): int
    {
        return $this->member_licence_status;
    }

    /**
     * @param int $member_licence_status
     * @return $this
     */
    public function setMemberLicenceStatus(int $member_licence_status): self
    {
        $this->member_licence_status = $member_licence_status;

        return $this;
    }

    /**
     * @return Grade|null
     */
    public function getMemberLicenceGrade(): ?Grade
    {
        return $this->member_licence_grade;
    }

    /**
     * @param Grade|null $member_licence_grade
     * @return $this
     */
    public function setMemberLicenceGrade(?Grade $member_licence_grade): self
    {
        $this->member_licence_grade = $member_licence_grade;

        return $this;
    }

    /**
     * @return Club
     */
    public function getMemberLicenceClub(): Club
    {
        return $this->member_licence_club;
    }

    /**
     * @param Club $member_licence_club
     * @return $this
     */
    public function setMemberLicenceClub(Club $member_licence_club): self
    {
        $this->member_licence_club = $member_licence_club;

        return $this;
    }

    /**
     * @return Member
     */
    public function getMemberLicence(): Member
    {
        return $this->member_licence;
    }

    /**
     * @param Member $member_licence
     * @return $this
     */
    public function setMemberLicence(Member $member_licence): self
    {
        $this->member_licence = $member_licence;

        return $this;
    }
}
