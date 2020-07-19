<?php
// src/Entity/Commission.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="afamanager_commission")
 * @ORM\Entity(repositoryClass="App\Repository\ClubRepository")
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

    public function getClubId(): ?int
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
}
