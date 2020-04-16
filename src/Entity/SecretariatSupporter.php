<?php
// src/Entity/SecretariatSupporter.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="afamanager_secretariat_supporter")
 * @ORM\Entity(repositoryClass="App\Repository\SecretariatSupporterRepository")
 */
class SecretariatSupporter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $secretariat_supporter_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $secretariat_supporter_name;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotBlank()
     */
    private $secretariat_supporter_address;

    /**
     * @ORM\Column(type="integer")
     * 
     * @Assert\NotBlank()
     */
    private $secretariat_supporter_zip;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private $secretariat_supporter_city;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $secretariat_supporter_comment;

    public function getSecretariatSupporterId(): ?int
    {
        return $this->secretariat_supporter_id;
    }

    public function getSecretariatSupporterName(): ?string
    {
        return $this->secretariat_supporter_name;
    }

    public function setSecretariatSupporterName(?string $secretariat_supporter_name): self
    {
        $this->secretariat_supporter_name = $secretariat_supporter_name;

        return $this;
    }

    public function getSecretariatSupporterAddress(): ?string
    {
        return $this->secretariat_supporter_address;
    }

    public function setSecretariatSupporterAddress(?string $secretariat_supporter_address): self
    {
        $this->secretariat_supporter_address = $secretariat_supporter_address;

        return $this;
    }

    public function getSecretariatSupporterZip(): ?int
    {
        return $this->secretariat_supporter_zip;
    }

    public function setSecretariatSupporterZip(?int $secretariat_supporter_zip): self
    {
        $this->secretariat_supporter_zip = $secretariat_supporter_zip;

        return $this;
    }

    public function getSecretariatSupporterCity(): ?string
    {
        return $this->secretariat_supporter_city;
    }

    public function setSecretariatSupporterCity(?string $secretariat_supporter_city): self
    {
        $this->secretariat_supporter_city = $secretariat_supporter_city;

        return $this;
    }

    public function getSecretariatSupporterComment(): ?string
    {
        return $this->secretariat_supporter_comment;
    }

    public function setSecretariatSupporterComment(?string $secretariat_supporter_comment): self
    {
        $this->secretariat_supporter_comment = $secretariat_supporter_comment;

        return $this;
    }
}
