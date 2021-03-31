<?php
// src/Entity/UserAccess.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class UserAccess
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_user_access")
 * @ORM\Entity(repositoryClass="App\Repository\UserAccessRepository")
 */
class UserAccess
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private int $user_access_id;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $user_access_role;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="user_accesses", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, name="user_access_join_user", referencedColumnName="id")
     */
    private ?User $user_access_user;

    /**
     * @var Club|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="club_accesses")
     * @ORM\JoinColumn(nullable=true, name="user_access_join_club", referencedColumnName="club_id")
     */
    private ?Club $user_access_club;

    /**
     * @return int
     */
    public function getUserAccessId(): int
    {
        return $this->user_access_id;
    }

    /**
     * @param int $user_access_id
     * @return $this
     */
    public function setUserAccessId(int $user_access_id): self
    {
        $this->user_access_id = $user_access_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserAccessRole(): ?string
    {
        return $this->user_access_role;
    }

    /**
     * @param string|null $user_access_role
     * @return $this
     */
    public function setUserAccessRole(?string $user_access_role): self
    {
        $this->user_access_role = $user_access_role;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUserAccessUser(): ?User
    {
        return $this->user_access_user;
    }

    /**
     * @param User|null $user_access_user
     * @return $this
     */
    public function setUserAccessUser(?User $user_access_user): self
    {
        $this->user_access_user = $user_access_user;

        return $this;
    }

    /**
     * @return Club|null
     */
    public function getUserAccessClub(): ?Club
    {
        return $this->user_access_club;
    }

    /**
     * @param Club|null $user_access_club
     * @return $this
     */
    public function setUserAccessClub(?Club $user_access_club): self
    {
        $this->user_access_club = $user_access_club;

        return $this;
    }
}
