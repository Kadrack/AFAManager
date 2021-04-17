<?php
// src/Entity/UserAuditTrail.php
namespace App\Entity;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class UserAuditTrail
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_user_audit_trail")
 * @ORM\Entity(repositoryClass="App\Repository\UserAuditTrailRepository")
 */
class UserAuditTrail
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $user_audit_trail_id;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private DateTime $user_audit_trail_date;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $user_audit_trail_login;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private int $user_audit_trail_action;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="user_audit_trails", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="user_audit_trail_join_user_user", referencedColumnName="id")
     */
    private ?User $user_audit_trail_user;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="user_audit_whos")
     * @ORM\JoinColumn(nullable=true, name="user_audit_trail_join_user_who", referencedColumnName="id")
     */
    private ?User $user_audit_trail_who;

    /**
     * @var Club|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_user_audit_trails")
     * @ORM\JoinColumn(nullable=true, name="user_audit_trail_join_club", referencedColumnName="club_id")
     */
    private ?Club $user_audit_trail_club;

    /**
     * UserAuditTrail constructor.
     */
    public function __construct()
    {
        $this->setUserAuditTrailDate();
    }

    /**
     * @return int
     */
    public function getUserAuditTrailId(): int
    {
        return $this->user_audit_trail_id;
    }

    /**
     * @param int $user_audit_trail_id
     * @return $this
     */
    public function setUserAuditTrailId(int $user_audit_trail_id): self
    {
        $this->user_audit_trail_id = $user_audit_trail_id;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUserAuditTrailDate(): DateTime
    {
        return $this->user_audit_trail_date;
    }

    /**
     * @return $this
     */
    public function setUserAuditTrailDate(): self
    {
        $this->user_audit_trail_date = new DateTime('now');

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserAuditTrailLogin(): ?string
    {
        return $this->user_audit_trail_login;
    }

    /**
     * @param string $user_audit_trail_login
     * @return $this
     */
    public function setUserAuditTrailLogin(string $user_audit_trail_login): self
    {
        $this->user_audit_trail_login = $user_audit_trail_login;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserAuditTrailAction(): int
    {
        return $this->user_audit_trail_action;
    }

    /**
     * @param int $user_audit_trail_action
     * @return $this
     */
    public function setUserAuditTrailAction(int $user_audit_trail_action): self
    {
        $this->user_audit_trail_action = $user_audit_trail_action;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUserAuditTrailUser(): ?User
    {
        return $this->user_audit_trail_user;
    }

    /**
     * @param User|null $user_audit_trail_user
     * @return $this
     */
    public function setUserAuditTrailUser(?User $user_audit_trail_user): self
    {
        $this->user_audit_trail_user = $user_audit_trail_user;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUserAuditTrailWho(): ?User
    {
        return $this->user_audit_trail_who;
    }

    /**
     * @param User|null $user_audit_trail_who
     * @return $this
     */
    public function setUserAuditTrailWho(?User $user_audit_trail_who): self
    {
        $this->user_audit_trail_who = $user_audit_trail_who;

        return $this;
    }

    /**
     * @return Club|null
     */
    public function getUserAuditTrailClub(): ?Club
    {
        return $this->user_audit_trail_club;
    }

    /**
     * @param Club|null $user_audit_trail_club
     * @return $this
     */
    public function setUserAuditTrailClub(?Club $user_audit_trail_club): self
    {
        $this->user_audit_trail_club = $user_audit_trail_club;

        return $this;
    }
}
