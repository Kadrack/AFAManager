<?php
// src/Entity/Mail.php
namespace App\Entity;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Mail
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_mail")
 * @ORM\Entity(repositoryClass="App\Repository\MailRepository")
 */
class Mail
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $mail_id;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private DateTime $mail_creation_date;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    private string $mail_title;

    /**
     * @var bool|null
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $mail_priority;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $mail_from;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private string $mail_to;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $mail_cc;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $mail_bcc;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private string $mail_body;

    /**
     * Email constructor.
     */
    public function __construct()
    {
        $this->mail_creation_date = new DateTime('today');

        $this->mail_cc  = null;
        $this->mail_bcc = null;
    }

    /**
     * @return int
     */
    public function getMailId(): int
    {
        return $this->mail_id;
    }

    /**
     * @param int $mail_id
     * @return $this
     */
    public function setMailId(int $mail_id): Mail
    {
        $this->mail_id = $mail_id;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getMailCreationDate(): DateTime
    {
        return $this->mail_creation_date;
    }

    /**
     * @param DateTime $mail_creation_date
     * @return $this
     */
    public function setMailCreationDate(DateTime $mail_creation_date): self
    {
        $this->mail_creation_date = $mail_creation_date;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getMailPriority(): ?bool
    {
        return $this->mail_priority;
    }

    /**
     * @param bool|null $mail_priority
     * @return $this
     */
    public function setMailPriority(?bool $mail_priority): self
    {
        $this->mail_priority = $mail_priority;

        return $this;
    }

    /**
     * @return string
     */
    public function getMailTitle(): string
    {
        return $this->mail_title;
    }

    /**
     * @param string $mail_title
     * @return $this
     */
    public function setMailTitle(string $mail_title): self
    {
        $this->mail_title = $mail_title;

        return $this;
    }

    /**
     * @return string
     */
    public function getMailFrom(): string
    {
        return $this->mail_from;
    }

    /**
     * @param string $mail_from
     * @return $this
     */
    public function setMailFrom(string $mail_from): self
    {
        $this->mail_from = $mail_from;

        return $this;
    }

    /**
     * @return string
     */
    public function getMailTo(): string
    {
        return $this->mail_to;
    }

    /**
     * @param string $mail_to
     * @return $this
     */
    public function setMailTo(string $mail_to): self
    {
        $this->mail_to = $mail_to;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMailCc(): ?string
    {
        return $this->mail_cc;
    }

    /**
     * @param string|null $mail_cc
     * @return $this
     */
    public function setMailCc(?string $mail_cc): self
    {
        $this->mail_cc = $mail_cc;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMailBcc(): ?string
    {
        return $this->mail_bcc;
    }

    /**
     * @param string|null $mail_bcc
     * @return $this
     */
    public function setMailBcc(?string $mail_bcc): self
    {
        $this->mail_bcc = $mail_bcc;

        return $this;
    }

    /**
     * @return string
     */
    public function getMailBody(): string
    {
        return $this->mail_body;
    }

    /**
     * @param string $mail_body
     * @return $this
     */
    public function setMailBody(string $mail_body): self
    {
        $this->mail_body = $mail_body;

        return $this;
    }
}
