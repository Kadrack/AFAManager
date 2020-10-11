<?php
// src/Entity/UserAuditTrail.php
namespace App\Entity;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="afamanager_user_audit_trail")
 * @ORM\Entity(repositoryClass="App\Repository\UserAuditTrailRepository")
 */
class UserAuditTrail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $user_audit_trail_id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $user_audit_trail_date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $user_audit_trail_login;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_audit_trail_action;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="user_audit_trails", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="user_audit_trail_join_user_user", referencedColumnName="id")
     */
    private $user_audit_trail_user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="user_audit_whos")
     * @ORM\JoinColumn(nullable=true, name="user_audit_trail_join_user_who", referencedColumnName="id")
     */
    private $user_audit_trail_who;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_user_audit_trails")
     * @ORM\JoinColumn(nullable=true, name="user_audit_trail_join_club", referencedColumnName="club_id")
     */
    private $user_audit_trail_club;

    public function __construct()
    {
        $this->setUserAuditTrailDate();
    }

    public function getUserAuditTrailId(): ?int
    {
        return $this->user_audit_trail_id;
    }

    public function setUserAuditTrailId(int $user_audit_trail_id): self
    {
        $this->user_audit_trail_id = $user_audit_trail_id;

        return $this;
    }

    public function getUserAuditTrailDate(): ?DateTime
    {
        return $this->user_audit_trail_date;
    }

    public function setUserAuditTrailDate(): self
    {
        $this->user_audit_trail_date = new DateTime('now');

        return $this;
    }

    public function getUserAuditTrailLogin(): ?string
    {
        return $this->user_audit_trail_login;
    }

    public function setUserAuditTrailLogin(string $user_audit_trail_login): self
    {
        $this->user_audit_trail_login = $user_audit_trail_login;

        return $this;
    }

    public function getUserAuditTrailAction(): ?int
    {
        return $this->user_audit_trail_action;
    }

    public function setUserAuditTrailAction(int $user_audit_trail_action): self
    {
        $this->user_audit_trail_action = $user_audit_trail_action;

        return $this;
    }

    public function getUserAuditTrailUser(): ?User
    {
        return $this->user_audit_trail_user;
    }

    public function setUserAuditTrailUser(?User $user_audit_trail_user): self
    {
        $this->user_audit_trail_user = $user_audit_trail_user;

        return $this;
    }

    public function getUserAuditTrailWho(): ?User
    {
        return $this->user_audit_trail_who;
    }

    public function setUserAuditTrailWho(?User $user_audit_trail_who): self
    {
        $this->user_audit_trail_who = $user_audit_trail_who;

        return $this;
    }

    public function getUserAuditTrailClub(): ?Club
    {
        return $this->user_audit_trail_club;
    }

    public function setUserAuditTrailClub(?Club $user_audit_trail_club): self
    {
        $this->user_audit_trail_club = $user_audit_trail_club;

        return $this;
    }
}
