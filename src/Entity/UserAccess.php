<?php
// src/Entity/UserAccess.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="afamanager_user_access")
 * @ORM\Entity(repositoryClass="App\Repository\UserAccessRepository")
 */
class UserAccess
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private ?int $user_access_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $user_access_role;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="user_accesses", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="user_access_join_user", referencedColumnName="id")
     */
    private ?User $user_access_user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_accesses")
     * @ORM\JoinColumn(nullable=true, name="user_access_join_club", referencedColumnName="club_id")
     */
    private ?Club $user_access_club;

    public function getUserAccessId(): ?int
    {
        return $this->user_access_id;
    }

    public function setUserAccessId(int $user_access_id): self
    {
        $this->user_access_id = $user_access_id;

        return $this;
    }

    public function getUserAccessRole(): ?string
    {
        return $this->user_access_role;
    }

    public function setUserAccessRole(string $user_access_role): self
    {
        $this->user_access_role = $user_access_role;

        return $this;
    }

    public function getUserAccessUser(): ?User
    {
        return $this->user_access_user;
    }

    public function setUserAccessUser(?User $user_access_user): self
    {
        $this->user_access_user = $user_access_user;

        return $this;
    }

    public function getUserAccessClub(): ?Club
    {
        return $this->user_access_club;
    }

    public function setUserAccessClub(?Club $user_access_club): self
    {
        $this->user_access_club = $user_access_club;

        return $this;
    }
}
