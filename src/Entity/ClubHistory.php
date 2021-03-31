<?php
// src/Entity/ClubHistory.php
namespace App\Entity;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ClubHistory
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_club_history")
 * @ORM\Entity(repositoryClass="App\Repository\ClubHistoryRepository")
 */
class ClubHistory
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $club_history_id;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private DateTime $club_history_update;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private int $club_history_status;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $club_history_comment;

    /**
     * @var Club|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_histories", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, name="club_history_join_club", referencedColumnName="club_id")
     */
    private ?Club $club_history;

    /**
     * @return int
     */
    public function getClubHistoryId(): int
    {
        return $this->club_history_id;
    }

    /**
     * @return DateTime
     */
    public function getClubHistoryUpdate(): DateTime
    {
        return $this->club_history_update;
    }

    /**
     * @param DateTime $club_history_update
     * @return $this
     */
    public function setClubHistoryUpdate(DateTime $club_history_update): self
    {
        $this->club_history_update = $club_history_update;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubHistoryStatus(): ?int
    {
        return $this->club_history_status;
    }

    /**
     * @param int $club_history_status
     * @return $this
     */
    public function setClubHistoryStatus(int $club_history_status): self
    {
        $this->club_history_status = $club_history_status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubHistoryComment(): ?string
    {
        return $this->club_history_comment;
    }

    /**
     * @param string|null $club_history_comment
     * @return $this
     */
    public function setClubHistoryComment(?string $club_history_comment): self
    {
        $this->club_history_comment = $club_history_comment;

        return $this;
    }

    /**
     * @return Club|null
     */
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
