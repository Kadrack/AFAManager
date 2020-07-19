<?php
// src/Entity/Commission.php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="afamanager_commission")
 * @ORM\Entity(repositoryClass="App\Repository\CommissionRepository")
 */
class Commission
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $commission_id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private $commission_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $commission_role;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommissionMember", mappedBy="commission", orphanRemoval=true, cascade={"persist"})
     */
    private $commission_members;

    public function __construct()
    {
        $this->commission_members = new ArrayCollection();
    }

    public function getCommissionId(): ?int
    {
        return $this->commission_id;
    }

    public function setCommissionId(?int $commission_id): self
    {
        $this->commission_id = $commission_id;

        return $this;
    }

    public function getCommissionName(): ?string
    {
        return $this->commission_name;
    }

    public function setCommissionName(?string $commission_name): self
    {
        $this->commission_name = $commission_name;

        return $this;
    }

    public function getCommissionRole(): ?string
    {
        return $this->commission_role;
    }

    public function setCommissionRole(?string $commission_role): self
    {
        $this->commission_role = $commission_role;

        return $this;
    }

    /**
     * @return Collection|CommissionMember[]
     */
    public function getCommissionMembers(): Collection
    {
        return $this->commission_members;
    }

    public function addCommissionMembers(CommissionMember $commissionMember): self
    {
        if (!$this->commission_members->contains($commissionMember)) {
            $this->commission_members[] = $commissionMember;
            $commissionMember->setCommission($this);
        }

        return $this;
    }

    public function removeMemberGrades(CommissionMember $commissionMember): self
    {
        if ($this->commission_members->contains($commissionMember)) {
            $this->commission_members->removeElement($commissionMember);
            // set the owning side to null (unless already changed)
            if ($commissionMember->getCommission() === $this) {
                $commissionMember->setCommission(null);
            }
        }

        return $this;
    }
}
