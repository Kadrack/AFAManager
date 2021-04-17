<?php
// src/Entity/Commission.php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Commission
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_commission")
 * @ORM\Entity(repositoryClass="App\Repository\CommissionRepository")
 */
class Commission
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $commission_id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    private string $commission_name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $commission_role;

    /**
     * @var ArrayCollection|Collection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\CommissionMember", mappedBy="commission", orphanRemoval=true, cascade={"persist"})
     */
    private ArrayCollection|Collection|null $commission_members;

    /**
     * Commission constructor.
     */
    public function __construct()
    {
        $this->commission_members = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getCommissionId(): int
    {
        return $this->commission_id;
    }

    /**
     * @param int $commission_id
     * @return $this
     */
    public function setCommissionId(int $commission_id): self
    {
        $this->commission_id = $commission_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCommissionName(): ?string
    {
        return $this->commission_name;
    }

    /**
     * @param string $commission_name
     * @return $this
     */
    public function setCommissionName(string $commission_name): self
    {
        $this->commission_name = $commission_name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCommissionRole(): ?string
    {
        return $this->commission_role;
    }

    /**
     * @param string|null $commission_role
     * @return $this
     */
    public function setCommissionRole(?string $commission_role): self
    {
        $this->commission_role = $commission_role;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCommissionMembers(): Collection
    {
        return $this->commission_members;
    }

    /**
     * @param CommissionMember $commissionMember
     * @return $this
     */
    public function addCommissionMembers(CommissionMember $commissionMember): self
    {
        if (!$this->commission_members->contains($commissionMember)) {
            $this->commission_members[] = $commissionMember;
            $commissionMember->setCommission($this);
        }

        return $this;
    }

    /**
     * @param CommissionMember $commissionMember
     * @return $this
     */
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
