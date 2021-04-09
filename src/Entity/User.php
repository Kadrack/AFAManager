<?php
// src/Entity/User.php
namespace App\Entity;

use App\Repository\UserRepository;

use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @package App\Entity
 *
 * @ORM\Table(name="afamanager_user")
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private string $login;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $user_firstname;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $user_real_name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $roles;

    /**
     * @var string The hashed password
     *
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTime $user_last_activity;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private int $user_status;

    /**
     * @var Member|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Member")
     * @ORM\JoinColumn(nullable=true, name="user_join_member", referencedColumnName="member_id")
     */
    private ?Member $user_member;

    /**
     * @var Collection|ArrayCollection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\UserAccess", mappedBy="user_access_user", orphanRemoval=true, cascade={"persist"})
     */
    private Collection|ArrayCollection|null $user_accesses;

    /**
     * @var Collection|ArrayCollection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\UserAuditTrail", mappedBy="user_audit_trail_user", orphanRemoval=true, cascade={"persist"})
     */
    private Collection|ArrayCollection|null $user_audit_trails;

    /**
     * @var Collection|ArrayCollection|null
     *
     * @ORM\OneToMany(targetEntity="App\Entity\UserAuditTrail", mappedBy="user_audit_trail_who", orphanRemoval=true, cascade={"persist"})
     */
    private Collection|ArrayCollection|null $user_audit_whos;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->user_accesses     = new ArrayCollection();
        $this->user_audit_whos   = new ArrayCollection();
        $this->user_audit_trails = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return $this
     */
    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles()
    {
        $roles = json_decode($this->roles);
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        if ($this->getUserMember() != null)
        {
            $roles[] = 'ROLE_MEMBER';
        }

        foreach ($this->getUserAccesses() as $access)
        {
            if (($access->getUserAccessRole() == 'ROLE_CLUB') || ($access->getUserAccessRole() == 'ROLE_TEACHER'))
            {
                $roles[] = $access->getUserAccessRole().'_'.$access->getUserAccessClub()->getClubId();
            }

            $roles[] = $access->getUserAccessRole();
        }

        return array_unique($roles);
    }

    /**
     * @param array|null $roles
     * @return $this
     */
    public function setRoles(?array $roles): self
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

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserFirstname(): ?string
    {
        return $this->user_firstname;
    }

    /**
     * @param string|null $user_firstname
     * @return $this
     */
    public function setUserFirstname(?string $user_firstname): self
    {
        $this->user_firstname = $user_firstname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserRealName(): ?string
    {
        return $this->user_real_name;
    }

    /**
     * @param string|null $user_real_name
     * @return $this
     */
    public function setUserRealName(?string $user_real_name): self
    {
        $this->user_real_name = $user_real_name;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getUserLastActivity(): ?DateTime
    {
        return $this->user_last_activity;
    }

    /**
     * @param DateTime $user_last_activity
     * @return $this
     */
    public function setUserLastActivity(DateTime $user_last_activity): self
    {
        $this->user_last_activity = $user_last_activity;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserStatus(): ?int
    {
        return $this->user_status;
    }

    /**
     * @param int $user_status
     * @return $this
     */
    public function setUserStatus(int $user_status): self
    {
        $this->user_status = $user_status;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getUserMember(): ?Member
    {
        return $this->user_member;
    }

    /**
     * @param Member|null $user_member
     * @return $this
     */
    public function setUserMember(?Member $user_member): self
    {
        $this->user_member = $user_member;

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

    /**
     * @return Collection
     */
    public function getUserAccesses(): Collection
    {
        return $this->user_accesses;
    }

    /**
     * @param UserAccess $userAccess
     * @return $this
     */
    public function addUserAccesses(UserAccess $userAccess): self
    {
        if (!$this->user_accesses->contains($userAccess)) {
            $this->user_accesses[] = $userAccess;
            $userAccess->setUserAccessUser($this);
        }

        return $this;
    }

    /**
     * @param UserAccess $userAccess
     * @return $this
     */
    public function removeUserAccesses(UserAccess $userAccess): self
    {
        if ($this->user_accesses->contains($userAccess)) {
            $this->user_accesses->removeElement($userAccess);
            // set the owning side to null (unless already changed)
            if ($userAccess->getUserAccessUser() === $this) {
                $userAccess->setUserAccessUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getUserAuditTrails(): Collection
    {
        return $this->user_audit_trails;
    }

    /**
     * @param UserAuditTrail $userAuditTrail
     * @return $this
     */
    public function addUserAuditTrails(UserAuditTrail $userAuditTrail): self
    {
        if (!$this->user_audit_trails->contains($userAuditTrail)) {
            $this->user_audit_trails[] = $userAuditTrail;
            $userAuditTrail->setUserAuditTrailUser($this);
        }

        return $this;
    }

    /**
     * @param UserAuditTrail $userAuditTrail
     * @return $this
     */
    public function removeUserAuditTrails(UserAuditTrail $userAuditTrail): self
    {
        if ($this->user_audit_trails->contains($userAuditTrail)) {
            $this->user_audit_trails->removeElement($userAuditTrail);
            // set the owning side to null (unless already changed)
            if ($userAuditTrail->getUserAuditTrailUser() === $this) {
                $userAuditTrail->setUserAuditTrailUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getUserAuditWhos(): Collection
    {
        return $this->user_audit_whos;
    }

    /**
     * @param UserAuditTrail $userAuditTrail
     * @return $this
     */
    public function addUserAuditWhos(UserAuditTrail $userAuditTrail): self
    {
        if (!$this->user_audit_whos->contains($userAuditTrail)) {
            $this->user_audit_whos[] = $userAuditTrail;
            $userAuditTrail->setUserAuditTrailWho($this);
        }

        return $this;
    }

    /**
     * @param UserAuditTrail $userAuditTrail
     * @return $this
     */
    public function removeUserAuditWhos(UserAuditTrail $userAuditTrail): self
    {
        if ($this->user_audit_whos->contains($userAuditTrail)) {
            $this->user_audit_whos->removeElement($userAuditTrail);
            // set the owning side to null (unless already changed)
            if ($userAuditTrail->getUserAuditTrailWho() === $this) {
                $userAuditTrail->setUserAuditTrailWho(null);
            }
        }

        return $this;
    }
}
