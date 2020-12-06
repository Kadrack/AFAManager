<?php
// src/Service/UserTools.php
namespace App\Service;

use App\Entity\Club;
use App\Entity\Member;
use App\Entity\User;
use App\Entity\UserAccess;
use App\Entity\UserAuditTrail;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class Tools
 * @package App\Service
 */
class UserTools
{
    private EntityManagerInterface $entityManager;

    private bool $isDuplicate;

    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * UserTools constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager   = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param User $user
     * @param User $who
     * @param string $password
     * @param int|null $member_id
     * @return bool
     */
    public function newUser(User $user, User $who, string $password, ?int $member_id): bool
    {
        if ($this->checkDuplicate($user))
        {
            return false;
        }

        $user = $this->setPassword($user, $password);

        if (!is_null($member_id))
        {
            $member = $this->entityManager->getRepository(Member::class)->findOneBy(['member_id' => $member_id]);

            if (is_null($member))
            {
                return false;
            }

            $user->setUserMember($member);
            $user->setUserRealName(null);
            $user->setUserFirstname(null);
        }

        $user->setUserStatus(1);

        $auditTrail = new UserAuditTrail();

        $auditTrail->setUserAuditTrailAction(7);
        $auditTrail->setUserAuditTrailUser($user);
        $auditTrail->setUserAuditTrailWho($who);

        $this->entityManager->persist($auditTrail);

        $this->entityManager->flush();

        return true;
    }

    /**
     * @return bool
     */
    public function isDuplicate(): bool
    {
        return $this->isDuplicate;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function checkDuplicate(User $user): bool
    {
        if (is_null($this->entityManager->getRepository(User::class)->findOneBy(['login' => $user->getLogin()])))
        {
            $this->isDuplicate = false;

            return false;
        }
        else
        {
            $this->isDuplicate = true;

            return true;
        }
    }

    /**
     * @param User $user
     * @param string $password
     * @return User
     */
    public function setPassword(User $user, string $password): User
    {
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

        return $user;
    }

    /**
     * @param User $user
     * @param string|null $new_login
     * @param string|null $new_password
     * @param User|null $who
     * @return bool
     */
    public function updateMyAccount(User $user, ?string $new_login, ?string $new_password, ?User $who = null): bool
    {
        if (!is_null($new_login) && ($new_login != $user->getLogin()))
        {
            $this->changeLogin($user, $new_login);
        }

        if (!is_null($new_password))
        {
            $this->changePassword($user, $new_password, $who);
        }

        return true;
    }

    /**
     * @param User $user
     * @param string $new_login
     * @return bool
     */
    public function changeLogin(User $user, string $new_login): bool
    {
        $user->setLogin($new_login);

        $this->checkDuplicate($user);

        if (!$this->isDuplicate)
        {
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    /**
     * @param User $user
     * @param string $new_password1
     * @param string $new_password2
     * @param User|null $who
     * @return bool
     */
    public function changePassword(User $user, string $new_password1, string $new_password2, ?User $who = null): bool
    {
        if ($new_password1 != $new_password2)
        {
            return false;
        }

        $user->setPassword($this->passwordEncoder->encodePassword($user, $new_password1));

        $auditTrail = new UserAuditTrail();

        $auditTrail->setUserAuditTrailAction($who == null ? 4 : 6);
        $auditTrail->setUserAuditTrailUser($user);
        $auditTrail->setUserAuditTrailWho($who);

        $this->entityManager->persist($auditTrail);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param User $user
     * @param User $who
     * @return bool
     */
    public function reactivate(User $user, User $who): bool
    {
        $user->setUserStatus(1);

        $auditTrail = new UserAuditTrail();

        $auditTrail->setUserAuditTrailAction(5);
        $auditTrail->setUserAuditTrailUser($user);
        $auditTrail->setUserAuditTrailWho($who);

        $this->entityManager->persist($auditTrail);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param User $user
     * @param Club $club
     * @param User $who
     * @param string|null $password
     * @param int|null $member_id
     * @return bool
     */
    public function clubManagerAdd(User $user, Club $club, User $who, string $password, ?int $member_id): bool
    {
        if (is_null($this->entityManager->getRepository(User::class)->findOneBy(['user_member' => $member_id])))
        {
            $this->newUser($user, $who, $password, $member_id);
        }
        else
        {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['user_member' => $member_id]);
        }

        $userAccess = new UserAccess();

        $userAccess->setUserAccessClub($club);
        $userAccess->setUserAccessRole(["ROLE_CLUB"]);
        $userAccess->setUserAccessUser($user);

        $auditTrail = new UserAuditTrail();

        $auditTrail->setUserAuditTrailAction(8);
        $auditTrail->setUserAuditTrailClub($club);
        $auditTrail->setUserAuditTrailUser($user);
        $auditTrail->setUserAuditTrailWho($who);

        $this->entityManager->persist($auditTrail);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param User $user
     * @param Club $club
     * @param User $who
     * @return bool
     */
    public function clubManagerDelete(User $user, Club $club, User $who): bool
    {
        $access = $this->entityManager->getRepository(UserAccess::class)->findOneBy(['user_access_club' => $club, 'user_access_role' => '["ROLE_CLUB"]', 'user_access_user' => $user]);

        $this->entityManager->remove($access);

        $auditTrail = new UserAuditTrail();

        $auditTrail->setUserAuditTrailAction(9);
        $auditTrail->setUserAuditTrailClub($club);
        $auditTrail->setUserAuditTrailUser($user);
        $auditTrail->setUserAuditTrailWho($who);

        $this->entityManager->persist($auditTrail);
        $this->entityManager->flush();

        return true;
    }
}
