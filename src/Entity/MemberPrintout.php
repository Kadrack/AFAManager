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
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private int $member_printout_action;

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
     * @return int
     */
    public function getMemberPrintoutId(): int
    {
        return $this->member_printout_id;
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
     * @return int
     */
    public function getMemberPrintoutAction(): int
    {
        return $this->member_printout_action;
    }

    /**
     * @param int $member_printout_action
     * @return $this
     */
    public function setMemberPrintoutAction(int $member_printout_action): self
    {
        $this->member_printout_action = $member_printout_action;

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
}
