<?php
// src/Entity/MemberModification.php
namespace App\Entity;

use DateTimeInterface;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="afamanager_member_modification")
 * @ORM\Entity(repositoryClass="App\Repository\MemberModificationRepository")
 */
class MemberModification
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $member_modification_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $member_modification_firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $member_modification_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $member_modification_photo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $member_modification_sex;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $member_modification_address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $member_modification_zip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $member_modification_city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $member_modification_country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $member_modification_email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $member_modification_phone;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $member_modification_birthday;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $member_modification_comment;

    public function getMemberModificationId(): ?int
    {
        return $this->member_modification_id;
    }

    public function setMemberModificationId(int $member_modification_id): self
    {
        $this->member_modification_id = $member_modification_id;

        return $this;
    }

    public function getMemberModificationFirstname(): ?string
    {
        return $this->member_modification_firstname;
    }

    public function setMemberModificationFirstname(string $member_modification_firstname): self
    {
        $this->member_modification_firstname = $member_modification_firstname;

        return $this;
    }

    public function getMemberModificationName(): ?string
    {
        return $this->member_modification_name;
    }

    public function setMemberModificationName(string $member_modification_name): self
    {
        $this->member_modification_name = $member_modification_name;

        return $this;
    }

    public function getMemberModificationPhoto(): ?string
    {
        return $this->member_modification_photo;
    }

    public function setMemberModificationPhoto(string $member_modification_photo): self
    {
        if ($member_modification_photo == null)
        {
            $member_modification_photo = 'nophoto.png';
        }

        $this->member_modification_photo = $member_modification_photo;

        return $this;
    }

    public function getMemberModificationSex(): ?int
    {
        return $this->member_modification_sex;
    }

    public function setMemberModificationSex(int $member_modification_sex): self
    {
        $this->member_modification_sex = $member_modification_sex;

        return $this;
    }

    public function getMemberModificationAddress(): ?string
    {
        return $this->member_modification_address;
    }

    public function setMemberModificationAddress(string $member_modification_address): self
    {
        $this->member_modification_address = $member_modification_address;

        return $this;
    }

    public function getMemberModificationZip(): ?string
    {
        return $this->member_modification_zip;
    }

    public function setMemberModificationZip(string $member_modification_zip): self
    {
        $this->member_modification_zip = $member_modification_zip;

        return $this;
    }

    public function getMemberModificationCity(): ?string
    {
        return $this->member_modification_city;
    }

    public function setMemberModificationCity(string $member_modification_city): self
    {
        $this->member_modification_city = $member_modification_city;

        return $this;
    }

    public function getMemberModificationCountry(): ?string
    {
        return $this->member_modification_country;
    }

    public function setMemberModificationCountry(string $member_modification_country = ""): self
    {
        if ($member_modification_country == "")
        {
            $this->member_modification_country = null;
        }
        else
        {
            $this->member_modification_country = $member_modification_country;
        }

        return $this;
    }

    public function getMemberModificationEmail(): ?string
    {
        return $this->member_modification_email;
    }

    public function setMemberModificationEmail(string $member_modification_email): self
    {
        $this->member_modification_email = $member_modification_email;

        return $this;
    }

    public function getMemberModificationPhone(): ?string
    {
        return $this->member_modification_phone;
    }

    public function setMemberModificationPhone(string $member_modification_phone): self
    {
        $this->member_modification_phone = $member_modification_phone;

        return $this;
    }

    public function getMemberModificationBirthday(): ?DateTimeInterface
    {
        return $this->member_modification_birthday;
    }

    public function setMemberModificationBirthday(?DateTimeInterface $member_modification_birthday): self
    {
        $this->member_modification_birthday = $member_modification_birthday;

        return $this;
    }
    
    public function getMemberModificationComment(): ?string
    {
        return $this->member_modification_comment;
    }

    public function setMemberModificationComment(string $member_modification_comment): self
    {
        $this->member_modification_comment = $member_modification_comment;

        return $this;
    }
}
