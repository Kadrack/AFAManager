<?php
// src/Entity/MemberModification.php
namespace App\Entity;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class MemberModification
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_member_modification")
 * @ORM\Entity(repositoryClass="App\Repository\MemberModificationRepository")
 */
class MemberModification
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private int $member_modification_id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $member_modification_firstname;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $member_modification_name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $member_modification_photo;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $member_modification_sex;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $member_modification_address;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $member_modification_zip;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $member_modification_city;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $member_modification_country;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $member_modification_email;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $member_modification_phone;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $member_modification_birthday;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $member_modification_aikikai_id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $member_modification_comment;

    /**
     * @return int
     */
    public function getMemberModificationId(): int
    {
        return $this->member_modification_id;
    }

    /**
     * @param int $member_modification_id
     * @return $this
     */
    public function setMemberModificationId(int $member_modification_id): self
    {
        $this->member_modification_id = $member_modification_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberModificationFirstname(): ?string
    {
        return $this->member_modification_firstname;
    }

    /**
     * @param string|null $member_modification_firstname
     * @return $this
     */
    public function setMemberModificationFirstname(?string $member_modification_firstname): self
    {
        $this->member_modification_firstname = $member_modification_firstname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberModificationName(): ?string
    {
        return $this->member_modification_name;
    }

    /**
     * @param string|null $member_modification_name
     * @return $this
     */
    public function setMemberModificationName(?string $member_modification_name): self
    {
        $this->member_modification_name = $member_modification_name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberModificationPhoto(): ?string
    {
        return $this->member_modification_photo;
    }

    /**
     * @param string|null $member_modification_photo
     * @return $this
     */
    public function setMemberModificationPhoto(?string $member_modification_photo): self
    {
        if ($member_modification_photo == null)
        {
            $member_modification_photo = 'nophoto.png';
        }

        $this->member_modification_photo = $member_modification_photo;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMemberModificationSex(): ?int
    {
        return $this->member_modification_sex;
    }

    /**
     * @param int|null $member_modification_sex
     * @return $this
     */
    public function setMemberModificationSex(?int $member_modification_sex): self
    {
        $this->member_modification_sex = $member_modification_sex;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberModificationAddress(): ?string
    {
        return $this->member_modification_address;
    }

    /**
     * @param string|null $member_modification_address
     * @return $this
     */
    public function setMemberModificationAddress(?string $member_modification_address): self
    {
        $this->member_modification_address = $member_modification_address;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberModificationZip(): ?string
    {
        return $this->member_modification_zip;
    }

    /**
     * @param string|null $member_modification_zip
     * @return $this
     */
    public function setMemberModificationZip(?string $member_modification_zip): self
    {
        $this->member_modification_zip = $member_modification_zip;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberModificationCity(): ?string
    {
        return $this->member_modification_city;
    }

    /**
     * @param string|null $member_modification_city
     * @return $this
     */
    public function setMemberModificationCity(?string $member_modification_city): self
    {
        $this->member_modification_city = $member_modification_city;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberModificationCountry(): ?string
    {
        return $this->member_modification_country;
    }

    /**
     * @param string|null $member_modification_country
     * @return $this
     */
    public function setMemberModificationCountry(?string $member_modification_country = ""): self
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

    /**
     * @return string|null
     */
    public function getMemberModificationEmail(): ?string
    {
        return $this->member_modification_email;
    }

    /**
     * @param string|null $member_modification_email
     * @return $this
     */
    public function setMemberModificationEmail(?string $member_modification_email): self
    {
        $this->member_modification_email = $member_modification_email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberModificationPhone(): ?string
    {
        return $this->member_modification_phone;
    }

    /**
     * @param string|null $member_modification_phone
     * @return $this
     */
    public function setMemberModificationPhone(?string $member_modification_phone): self
    {
        $this->member_modification_phone = $member_modification_phone;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getMemberModificationBirthday(): ?DateTime
    {
        return $this->member_modification_birthday;
    }

    /**
     * @param DateTime|null $member_modification_birthday
     * @return $this
     */
    public function setMemberModificationBirthday(?DateTime $member_modification_birthday): self
    {
        $this->member_modification_birthday = $member_modification_birthday;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberModificationAikikaiId(): ?string
    {
        return $this->member_modification_aikikai_id;
    }

    /**
     * @param string|null $member_modification_aikikai_id
     * @return $this
     */
    public function setMemberModificationAikikaiId(?string $member_modification_aikikai_id): self
    {
        $this->member_modification_aikikai_id = $member_modification_aikikai_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberModificationComment(): ?string
    {
        return $this->member_modification_comment;
    }

    /**
     * @param string|null $member_modification_comment
     * @return $this
     */
    public function setMemberModificationComment(?string $member_modification_comment): self
    {
        $this->member_modification_comment = $member_modification_comment;

        return $this;
    }
}
