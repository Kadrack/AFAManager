<?php
// src/Entity/Email.php
namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="afamanager_email")
 * @ORM\Entity(repositoryClass="App\Repository\EmailRepository")
 */
class Email
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $email_id;

    /**
     * @ORM\Column(type="date")
     */
    private $email_creation_date;
    
    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotBlank()
     */
    private $email_title;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email_from;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email_to;
    
    /**
     * @ORM\Column(type="text")
     */
    private $email_body;
    
    public function __construct()
    {
        $this->email_creation_date = new DateTime('today');
    }

    public function getEmailId()
    {
        return $this->email_id;
    }

    public function getEmailCreationDate(): ?DateTimeInterface
    {
        return $this->email_creation_date;
    }

    public function setEmailCreationDate(DateTimeInterface $email_creation_date): self
    {
        $this->email_creation_date = $email_creation_date;

        return $this;
    }
    
    public function getEmailTitle(): ?string
    {
        return $this->email_title;
    }

    public function setEmailTitle(string $email_title): self
    {
        $this->email_title = $email_title;

        return $this;
    }

    public function getEmailFrom(): ?string
    {
        return $this->email_from;
    }

    public function setEmailFrom(string $email_from): self
    {
        $this->email_from = $email_from;

        return $this;
    }

    public function getEmailTo(): ?string
    {
        return $this->email_to;
    }

    public function setEmailTo(string $email_to): self
    {
        $this->email_to = $email_to;

        return $this;
    }

    public function getEmailBody(): ?string
    {
        return $this->email_body;
    }

    public function setEmailBody(string $email_body): self
    {
        $this->email_body = $email_body;

        return $this;
    }
}
