<?php
// src/Entity/SecretariatSupporter.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class SecretariatSupporter
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_secretariat_supporter")
 * @ORM\Entity(repositoryClass="App\Repository\SecretariatSupporterRepository")
 */
class SecretariatSupporter
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $secretariat_supporter_id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $secretariat_supporter_name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    private string $secretariat_supporter_address;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    #[Assert\NotBlank]
    private int $secretariat_supporter_zip;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    private string $secretariat_supporter_city;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $secretariat_supporter_comment;

    /**
     * @return int
     */
    public function getSecretariatSupporterId(): int
    {
        return $this->secretariat_supporter_id;
    }

    /**
     * @return string
     */
    public function getSecretariatSupporterName(): string
    {
        return $this->secretariat_supporter_name;
    }

    /**
     * @param string $secretariat_supporter_name
     * @return $this
     */
    public function setSecretariatSupporterName(string $secretariat_supporter_name): self
    {
        $this->secretariat_supporter_name = $secretariat_supporter_name;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecretariatSupporterAddress(): string
    {
        return $this->secretariat_supporter_address;
    }

    /**
     * @param string $secretariat_supporter_address
     * @return $this
     */
    public function setSecretariatSupporterAddress(string $secretariat_supporter_address): self
    {
        $this->secretariat_supporter_address = $secretariat_supporter_address;

        return $this;
    }

    /**
     * @return int
     */
    public function getSecretariatSupporterZip(): int
    {
        return $this->secretariat_supporter_zip;
    }

    /**
     * @param int $secretariat_supporter_zip
     * @return $this
     */
    public function setSecretariatSupporterZip(int $secretariat_supporter_zip): self
    {
        $this->secretariat_supporter_zip = $secretariat_supporter_zip;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecretariatSupporterCity(): string
    {
        return $this->secretariat_supporter_city;
    }

    /**
     * @param string $secretariat_supporter_city
     * @return $this
     */
    public function setSecretariatSupporterCity(string $secretariat_supporter_city): self
    {
        $this->secretariat_supporter_city = $secretariat_supporter_city;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSecretariatSupporterComment(): ?string
    {
        return $this->secretariat_supporter_comment;
    }

    /**
     * @param string|null $secretariat_supporter_comment
     * @return $this
     */
    public function setSecretariatSupporterComment(?string $secretariat_supporter_comment): self
    {
        $this->secretariat_supporter_comment = $secretariat_supporter_comment;

        return $this;
    }
}
