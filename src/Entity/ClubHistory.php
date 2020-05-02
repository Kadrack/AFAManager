<?php
// src/Entity/ClubHistory.php
namespace App\Entity;

use DateTimeInterface;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="afamanager_club_history")
 * @ORM\Entity(repositoryClass="App\Repository\ClubHistoryRepository")
 */
class ClubHistory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $club_history_id;

    /**
     * @ORM\Column(type="date")
     */
    private $club_history_update;

    /**
     * @ORM\Column(type="integer")
     */
    private $club_history_status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $club_history_comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_histories", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="club_history_join_club", referencedColumnName="club_id")
     */
    private $club_history;

    public function getClubHistoryId()
    {
        return $this->club_history_id;
    }

    public function getClubHistoryUpdate(): ?DateTimeInterface
    {
        return $this->club_history_update;
    }

    public function setClubHistoryUpdate(DateTimeInterface $club_history_update): self
    {
        $this->club_history_update = $club_history_update;

        return $this;
    }

    public function getClubHistoryStatus(): ?int
    {
        return $this->club_history_status;
    }

    public function setClubHistoryStatus(int $club_history_status): self
    {
        $this->club_history_status = $club_history_status;

        return $this;
    }

    public function getClubHistoryComment(): ?string
    {
        return $this->club_history_comment;
    }

    public function setClubHistoryComment(?string $club_history_comment): self
    {
        $this->club_history_comment = $club_history_comment;

        return $this;
    }

    public function getClubHistory(): ?Club
    {
        return $this->club_history;
    }

    public function setClubHistory(?Club $club_history): self
    {
        $this->club_history = $club_history;

        return $this;
    }
}
