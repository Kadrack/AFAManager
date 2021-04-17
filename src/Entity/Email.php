<?php
// src/Entity/Email.php
namespace App\Entity;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Email
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_email")
 * @ORM\Entity(repositoryClass="App\Repository\EmailRepository")
 */
class Email
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $email_id;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private DateTime $email_creation_date;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    private string $email_title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $email_from;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $email_to;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private string $email_body;

    /**
     * Email constructor.
     */
    public function __construct()
    {
        $this->email_creation_date = new DateTime('today');
    }

    /**
     * @return int
     */
    public function getEmailId(): int
    {
        return $this->email_id;
    }

    /**
     * @return DateTime
     */
    public function getEmailCreationDate(): DateTime
    {
        return $this->email_creation_date;
    }

    /**
     * @param DateTime $email_creation_date
     * @return $this
     */
    public function setEmailCreationDate(DateTime $email_creation_date): self
    {
        $this->email_creation_date = $email_creation_date;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmailTitle(): string
    {
        return $this->email_title;
    }

    /**
     * @param string $email_title
     * @return $this
     */
    public function setEmailTitle(string $email_title): self
    {
        $this->email_title = $email_title;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmailFrom(): string
    {
        return $this->email_from;
    }

    /**
     * @param string $email_from
     * @return $this
     */
    public function setEmailFrom(string $email_from): self
    {
        $this->email_from = $email_from;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmailTo(): string
    {
        return $this->email_to;
    }

    /**
     * @param string $email_to
     * @return $this
     */
    public function setEmailTo(string $email_to): self
    {
        $this->email_to = $email_to;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmailBody(): string
    {
        return $this->email_body;
    }

    /**
     * @param string $email_body
     * @return $this
     */
    public function setEmailBody(string $email_body): self
    {
        $this->email_body = $email_body;

        return $this;
    }
}
