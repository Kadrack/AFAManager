<?php
// src/Entity/CommissionMember.php
namespace App\Entity;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class CommissionMember
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_commission_member")
 * @ORM\Entity(repositoryClass="App\Repository\CommissionMemberRepository")
 */
class CommissionMember
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $commission_member_id;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private DateTime $commission_member_date_in;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $commission_member_date_out;

    /**
     * @var Commission
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Commission", inversedBy="commission_members", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="commission_member_join_commission", referencedColumnName="commission_id")
     */
    private Commission $commission;

    /**
     * @var Member
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Member", inversedBy="member_commissions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="commission_member_join_member", referencedColumnName="member_id")
     */
    private Member $commission_member;

    /**
     * @return int
     */
    public function getCommissionMemberId(): int
    {
        return $this->commission_member_id;
    }

    /**
     * @param int $commission_member_id
     * @return $this
     */
    public function setCommissionMemberId(int $commission_member_id): self
    {
        $this->commission_member_id = $commission_member_id;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getCommissionMemberDateIn(): ?DateTime
    {
        return $this->commission_member_date_in;
    }

    /**
     * @param DateTime $commission_member_date_in
     * @return $this
     */
    public function setCommissionMemberDateIn(DateTime $commission_member_date_in): self
    {
        $this->commission_member_date_in = $commission_member_date_in;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getCommissionMemberDateOut(): ?DateTime
    {
        return $this->commission_member_date_out;
    }

    /**
     * @param DateTime $commission_member_date_out
     * @return $this
     */
    public function setCommissionMemberDateOut(DateTime $commission_member_date_out): self
    {
        $this->commission_member_date_out = $commission_member_date_out;

        return $this;
    }

    /**
     * @return Commission
     */
    public function getCommission(): Commission
    {
        return $this->commission;
    }

    /**
     * @param Commission $commission
     * @return $this
     */
    public function setCommission(Commission $commission): self
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * @return Member
     */
    public function getCommissionMember(): Member
    {
        return $this->commission_member;
    }

    /**
     * @param Member $member
     * @return $this
     */
    public function setCommissionMember(Member $member): self
    {
        $this->commission_member = $member;

        return $this;
    }
}
