<?php
// src/Entity/MemberPrintout.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="afamanager_member_printout")
 * @ORM\Entity(repositoryClass="App\Repository\MemberPrintoutRepository")
 */
class MemberPrintout
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $member_printout_id;

    /**
     * @ORM\Column(type="date")
     */
    private $member_printout_creation;

    /**
     * @ORM\Column(type="integer")
     */
    private $member_printout_action;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $member_printout_done;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MemberLicence", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="member_printout_join_member_licence", referencedColumnName="member_licence_id")
     */
    private $member_printout_licence;

    public function getMemberPrintoutId()
    {
        return $this->member_printout_id;
    }

    public function getMemberPrintoutCreation(): ?\DateTimeInterface
    {
        return $this->member_printout_creation;
    }

    public function setMemberPrintoutCreation(\DateTimeInterface $member_printout_creation): self
    {
        $this->member_printout_creation = $member_printout_creation;

        return $this;
    }

    public function getMemberPrintoutAction(): ?int
    {
        return $this->member_printout_action;
    }

    public function setMemberPrintoutAction(int $member_printout_action): self
    {
        $this->member_printout_action = $member_printout_action;

        return $this;
    }

    public function getMemberPrintoutDone(): ?\DateTimeInterface
    {
        return $this->member_printout_done;
    }

    public function setMemberPrintoutDone(\DateTimeInterface $member_printout_done): self
    {
        $this->member_printout_done = $member_printout_done;

        return $this;
    }

    public function getMemberPrintoutLicence(): ?MemberLicence
    {
        return $this->member_printout_licence;
    }

    public function setMemberPrintoutLicence(?MemberLicence $member_printout_licence): self
    {
        $this->member_printout_licence = $member_printout_licence;

        return $this;
    }
}
