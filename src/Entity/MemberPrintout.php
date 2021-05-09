<?php
// src/Entity/MemberPrintout.php
namespace App\Entity;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class MemberPrintout
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_member_printout")
 * @ORM\Entity(repositoryClass="App\Repository\MemberPrintoutRepository")
 */
class MemberPrintout
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $member_printout_id;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private DateTime $member_printout_creation;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $member_printout_done;

    /**
     * @var MemberLicence|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\MemberLicence", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="member_printout_join_member_licence", referencedColumnName="member_licence_id")
     */
    private ?MemberLicence $member_printout_licence;

    /**
     * @var Member|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="member_printout_join_member", referencedColumnName="member_id")
     */
    private ?Member $member_printout_member;

    /**
     * @return int
     */
    public function getMemberPrintoutId(): int
    {
        return $this->member_printout_id;
    }

    /**
     * @param int $member_printout_id
     * @return $this
     */
    public function setMemberPrintoutId(int $member_printout_id): self
    {
        $this->member_printout_id = $member_printout_id;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getMemberPrintoutCreation(): DateTime
    {
        return $this->member_printout_creation;
    }

    /**
     * @param DateTime $member_printout_creation
     * @return $this
     */
    public function setMemberPrintoutCreation(DateTime $member_printout_creation): self
    {
        $this->member_printout_creation = $member_printout_creation;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getMemberPrintoutDone(): ?DateTime
    {
        return $this->member_printout_done;
    }

    /**
     * @param DateTime|null $member_printout_done
     * @return $this
     */
    public function setMemberPrintoutDone(?DateTime $member_printout_done): self
    {
        $this->member_printout_done = $member_printout_done;

        return $this;
    }

    /**
     * @return MemberLicence|null
     */
    public function getMemberPrintoutLicence(): ?MemberLicence
    {
        return $this->member_printout_licence;
    }

    /**
     * @param MemberLicence|null $member_printout_licence
     * @return $this
     */
    public function setMemberPrintoutLicence(?MemberLicence $member_printout_licence): self
    {
        $this->member_printout_licence = $member_printout_licence;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getMemberPrintoutMember(): ?MemberLicence
    {
        return $this->member_printout_member;
    }

    /**
     * @param Member|null $member_printout_member
     * @return $this
     */
    public function setMemberPrintoutMember(?Member $member_printout_member): self
    {
        $this->member_printout_member = $member_printout_member;

        return $this;
    }
}
