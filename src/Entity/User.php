<?php
// src/Entity/User.php
namespace App\Entity;

use App\Repository\UserRepository;

use DateTimeInterface;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="afamanager_user")
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $roles;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $user_last_activity;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_status;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Member")
     * @ORM\JoinColumn(nullable=true, name="user_join_member", referencedColumnName="member_id")
     */
    private $user_member;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Club")
     * @ORM\JoinColumn(nullable=true, name="user_join_club", referencedColumnName="club_id")
     */
    private $user_club;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = json_decode($this->roles);
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        if ($this->getUserMember() != null)
        {
            $roles[] = 'ROLE_MEMBER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getUserLastActivity(): ?DateTimeInterface
    {
        return $this->user_last_activity;
    }

    public function setUserLastActivity(DateTimeInterface $user_last_activity): self
    {
        $this->user_last_activity = $user_last_activity;

        return $this;
    }

    public function getUserStatus(): ?int
    {
        return $this->user_status;
    }

    public function setUserStatus(int $user_status): self
    {
        $this->user_status = $user_status;

        return $this;
    }

    public function getUserMember(): ?Member
    {
        return $this->user_member;
    }

    public function setUserMember(?Member $user_member): self
    {
        $this->user_member = $user_member;

        return $this;
    }

    public function getUserClub(): ?Club
    {
        return $this->user_club;
    }

    public function setUserClub(?Club $user_club): self
    {
        $this->user_club = $user_club;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->login;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
