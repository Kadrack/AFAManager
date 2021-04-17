<?php
// src/Entity/ClubDojo.php
namespace App\Entity;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ClubModificationLog
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_club_modification_log")
 * @ORM\Entity(repositoryClass="App\Repository\ClubModificationLogRepository")
 */
class ClubModificationLog
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $club_modification_log_id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $club_modification_log_action;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $club_modification_log_date;

    /**
     * @var Club|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="club_modification_log_join_club", referencedColumnName="club_id")
     */
    private ?Club $club_modification_log_club;

    /**
     * ClubModificationLog constructor.
     */
    public function __construct()
    {
        $this->club_modification_log_date = new DateTime('today');
    }

    public function getClubModificationLogId(): ?int
    {
        return $this->club_modification_log_id;
    }

    public function getClubModificationLogAction(): ?string
    {
        return $this->club_modification_log_action;
    }

    public function setClubModificationLogAction(?string $club_modification_log_action): self
    {
        $this->club_modification_log_action = $club_modification_log_action;

        return $this;
    }

    public function getClubModificationLogDate(): ?DateTime
    {
        return $this->club_modification_log_date;
    }

    public function setClubModificationLogDate(?DateTime $club_modification_log_date): self
    {
        $this->club_modification_log_date = $club_modification_log_date;

        return $this;
    }

    public function getClubModificationLogClub(): ?Club
    {
        return $this->club_modification_log_club;
    }

    public function setClubModificationLogClub(?Club $club_modification_log_club): self
    {
        $this->club_modification_log_club = $club_modification_log_club;

        return $this;
    }
}
