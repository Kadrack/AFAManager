<?php
// src/Entity/User.php
namespace App\Entity;

use App\Repository\UserRepository;

use DateTimeInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private ?string $login;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $user_firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $user_real_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $roles;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTimeInterface $user_last_activity;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $user_status;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Member")
     * @ORM\JoinColumn(nullable=true, name="user_join_member", referencedColumnName="member_id")
     */
    private ?Member $user_member;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserAccess", mappedBy="user_access_user", orphanRemoval=true, cascade={"persist"})
     */
    private ?Collection $user_accesses;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserAuditTrail", mappedBy="user_audit_trail_user", orphanRemoval=true, cascade={"persist"})
     */
    private ?Collection $user_audit_trails;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserAuditTrail", mappedBy="user_audit_trail_who", orphanRemoval=true, cascade={"persist"})
     */
    private ?Collection $user_audit_whos;

    public function __construct()
    {
        $this->user_accesses     = new ArrayCollection();
        $this->user_audit_whos   = new ArrayCollection();
        $this->user_audit_trails = new ArrayCollection();
    }

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

    public function getUserFirstname(): ?string
    {
        return $this->user_firstname;
    }

    public function setUserFirstname(?string $user_firstname): self
    {
        $this->user_firstname = $user_firstname;

        return $this;
    }

    public function getUserRealName(): ?string
    {
        return $this->user_real_name;
    }

    public function setUserRealName(?string $user_real_name): self
    {
        $this->user_real_name = $user_real_name;

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
     * @return Collection|UserAccess[]
     */
    public function getUserAccesses(): Collection
    {
        return $this->user_accesses;
    }

    public function addUserAccesses(UserAccess $userAccess): self
    {
        if (!$this->user_accesses->contains($userAccess)) {
            $this->user_accesses[] = $userAccess;
            $userAccess->setUserAccessUser($this);
        }

        return $this;
    }

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
     * @return Collection|UserAuditTrail[]
     */
    public function getUserAuditTrails(): Collection
    {
        return $this->user_audit_trails;
    }

    public function addUserAuditTrails(UserAuditTrail $userAuditTrail): self
    {
        if (!$this->user_audit_trails->contains($userAuditTrail)) {
            $this->user_audit_trails[] = $userAuditTrail;
            $userAuditTrail->setUserAuditTrailUser($this);
        }

        return $this;
    }

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
     * @return Collection|UserAuditTrail[]
     */
    public function getUserAuditWhos(): Collection
    {
        return $this->user_audit_whos;
    }

    public function addUserAuditWhos(UserAuditTrail $userAuditTrail): self
    {
        if (!$this->user_audit_whos->contains($userAuditTrail)) {
            $this->user_audit_whos[] = $userAuditTrail;
            $userAuditTrail->setUserAuditTrailWho($this);
        }

        return $this;
    }

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
