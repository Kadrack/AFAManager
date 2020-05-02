<?php
// src/Entity/YTrainingAddress.php
namespace App\Entity;

use DateTimeInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="afamanager_training_address")
 * @ORM\Entity(repositoryClass="App\Repository\TrainingAddressRepository")
 */
class TrainingAddress
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $training_address_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $training_address_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Assert\NotBlank()
     */
    private $training_address_street;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @Assert\NotBlank()
     */
    private $training_address_zip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank()
     */
    private $training_address_city;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $training_address_tatamis;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $training_address_dea;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $training_address_dea_formation;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $training_address_comment;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Training", mappedBy="training_address", orphanRemoval=true, cascade={"persist"})
     */
    private $training_addresses;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_addresses", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="training_address_join_club", referencedColumnName="club_id")
     */
    private $training_address_club;

    public function __construct()
    {
        $this->training_addresses = new ArrayCollection();
    }

    public function getTrainingAddressId(): ?int
    {
        return $this->training_address_id;
    }

    public function getTrainingAddressName(): ?string
    {
        return $this->training_address_name;
    }

    public function setTrainingAddressName(?string $training_address_name): self
    {
        $this->training_address_name = $training_address_name;

        return $this;
    }

    public function getTrainingAddressStreet(): ?string
    {
        return $this->training_address_street;
    }

    public function setTrainingAddressStreet(?string $training_address_street): self
    {
        $this->training_address_street = $training_address_street;

        return $this;
    }

    public function getTrainingAddressZip(): ?int
    {
        return $this->training_address_zip;
    }

    public function setTrainingAddressZip(?int $training_address_zip): self
    {
        $this->training_address_zip = $training_address_zip;

        return $this;
    }

    public function getTrainingAddressCity(): ?string
    {
        return $this->training_address_city;
    }

    public function setTrainingAddressCity(?string $training_address_city): self
    {
        $this->training_address_city = $training_address_city;

        return $this;
    }

    public function getTrainingAddressTatamis(): ?int
    {
        return $this->training_address_tatamis;
    }

    public function setTrainingAddressTatamis(?int $training_address_tatamis): self
    {
        $this->training_address_tatamis = $training_address_tatamis;

        return $this;
    }

    public function getTrainingAddressDEA(): ?bool
    {
        return $this->training_address_dea;
    }

    public function setTrainingAddressDEA(?bool $training_address_dea): self
    {
        $this->training_address_dea = $training_address_dea;

        return $this;
    }

    public function getTrainingAddressDEAFormation(): ?DateTimeInterface
    {
        return $this->training_address_dea_formation;
    }

    public function setTrainingAddressDEAFormation(?DateTimeInterface $training_address_dea_formation): self
    {
        $this->training_address_dea_formation = $training_address_dea_formation;

        return $this;
    }

    public function getTrainingAddressComment(): ?string
    {
        return $this->training_address_comment;
    }

    public function setTrainingAddressComment(?string $training_address_comment): self
    {
        $this->training_address_comment = $training_address_comment;

        return $this;
    }

    public function getTrainingAddressClub(): ?Club
    {
        return $this->training_address_club;
    }

    public function setTrainingAddressClub(?Club $training_address_club): self
    {
        $this->training_address_club = $training_address_club;

        return $this;
    }

    /**
     * @return Collection|Training[]
     */
    public function getTrainingAddresses(): Collection
    {
        return $this->training_addresses;
    }

    public function addTrainingAddresses(Training $training): self
    {
        if (!$this->training_addresses->contains($training)) {
            $this->training_addresses[] = $training;
            $training->setTrainingAddress($this);
        }

        return $this;
    }

    public function removeTrainingAddresses(Training $training): self
    {
        if ($this->training_addresses->contains($training)) {
            $this->training_addresses->removeElement($training);
            // set the owning side to null (unless already changed)
            if ($training->getTrainingAddress() === $this) {
                $training->setTrainingAddress(null);
            }
        }

        return $this;
    }
}
