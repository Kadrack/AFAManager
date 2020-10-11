<?php
// src/Service/UserTools.php
namespace App\Service;

use App\Entity\Club;
use App\Entity\Member;
use App\Entity\User;
use App\Entity\UserAuditTrail;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class Tools
 * @package App\Service
 */
class UserTools
{
    private $entityManager;

    private $isDuplicate;

    private $passwordEncoder;

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
    public function newUser(User $user, User $who, string $password, ?int $member_id)
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
    public function isDuplicate()
    {
        return $this->isDuplicate;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function checkDuplicate(User $user)
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
    public function setPassword(User $user, string $password)
    {
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

        return $user;
    }

    /**
     * @param User $user
     * @param string $new_password
     * @param User|null $who
     * @return bool
     */
    public function changePassword(User $user, string $new_password, ?User $who = null)
    {
        $user->setPassword($this->passwordEncoder->encodePassword($user, $new_password));

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
    public function reactivate(User $user, User $who)
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
    public function clubManagerAdd(User $user, Club $club, User $who, string $password, ?int $member_id)
    {
        if ($user->getPassword() == "")
        {
            $this->newUser($user, $who, $password, $member_id);
        }

        $user->setUserClub($club);

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
    public function clubManagerDelete(User $user, Club $club, User $who)
    {
        $user->setUserClub(null);

        $this->entityManager->flush();

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
