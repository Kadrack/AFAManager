<?php
// src/Entity/CommissionMember.php
namespace App\Entity;

use DateTimeInterface;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="afamanager_commission_member")
 * @ORM\Entity(repositoryClass="App\Repository\CommissionMemberRepository")
 */
class CommissionMember
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $commission_member_id;

    /**
     * @ORM\Column(type="date")
     */
    private $commission_member_date_in;

    /**
     * @ORM\Column(type="date")
     */
    private $commission_member_date_out;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Commission", inversedBy="commission_members", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="commission_member_join_commission", referencedColumnName="commission_id")
     */
    private $commission;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="member_commissions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="commission_member_join_member", referencedColumnName="member_id")
     */
    private $commission_member;

    public function getCommissionMemberId(): ?int
    {
        return $this->commission_member_id;
    }

    public function setCommissionMemberId(?int $commission_member_id): self
    {
        $this->commission_member_id = $commission_member_id;

        return $this;
    }

    public function getCommissionMemberDateIn(): ?DateTimeInterface
    {
        return $this->commission_member_date_in;
    }

    public function setCommissionMemberDateIn(DateTimeInterface $commission_member_date_in): self
    {
        $this->commission_member_date_in = $commission_member_date_in;

        return $this;
    }

    public function getCommissionMemberDateOut(): ?DateTimeInterface
    {
        return $this->commission_member_date_out;
    }

    public function setCommissionMemberDateOut(DateTimeInterface $commission_member_date_out): self
    {
        $this->commission_member_date_out = $commission_member_date_out;

        return $this;
    }

    public function getCommission(): ?Commission
    {
        return $this->commission;
    }

    public function setCommission(?Commission $commission): self
    {
        $this->commission = $commission;

        return $this;
    }

    public function getCommissionMember(): ?Member
    {
        return $this->commission_member;
    }

    public function setCommissionMember(?Member $member): self
    {
        $this->commission_member = $member;

        return $this;
    }
}
