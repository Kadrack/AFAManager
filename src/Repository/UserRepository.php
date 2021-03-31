<?php

namespace App\Repository;

use App\Entity\Club;
use App\Entity\Member;
use App\Entity\User;
use App\Entity\UserAccess;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * UserRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return array|null
     */
    public function getClubManagerList(): ?array
    {
        $qb = $this->createQueryBuilder('u');

        return $qb->select('u.id AS UserId', 'u.login AS Login', 'u.user_firstname AS UserFirstname', 'u.user_real_name AS UserRealName', 'm.member_id AS LicenceId', 'm.member_firstname AS MemberFirstname', 'm.member_name AS MemberName', 'c.club_name AS ClubName', 'u.user_last_activity AS Activity', 'u.user_status AS Status')
            ->leftJoin(Member::class, 'm', 'WITH', $qb->expr()->eq('u.user_member', 'm.member_id'))
            ->leftJoin(UserAccess::class, 'a', 'WITH', $qb->expr()->eq('u.id', 'a.user_access_user'))
            ->leftJoin(Club::class, 'c', 'WITH', $qb->expr()->eq('a.user_access_club', 'c.club_id'))
            ->where($qb->expr()->isNotNull('a.user_access_club'))
            ->orderBy('u.roles', 'DESC')
            ->addOrderBy('u.login', 'ASC')
            ->addOrderBy('a.user_access_club', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array|null
     */
    public function getSecretariatAccessList(): ?array
    {
        $qb = $this->createQueryBuilder('u');

        return $qb->select('u.id AS UserId', 'u.login AS Login', 'u.user_firstname AS UserFirstname', 'u.user_real_name AS UserRealName', 'm.member_id AS LicenceId', 'm.member_firstname AS MemberFirstname', 'm.member_name AS MemberName', 'u.user_last_activity AS Activity', 'u.user_status AS Status')
            ->leftJoin(Member::class, 'm', 'WITH', $qb->expr()->eq('u.user_member', 'm.member_id'))
            ->leftJoin(UserAccess::class, 'a', 'WITH', $qb->expr()->eq('u.id', 'a.user_access_user'))
            ->where($qb->expr()->eq('a.user_access_role', $qb->expr()->literal('ROLE_SECRETARIAT')))
            ->orderBy('u.roles', 'DESC')
            ->addOrderBy('u.login', 'ASC')
            ->addOrderBy('a.user_access_club', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array|null
     */
    public function getLockedAccessList(): ?array
    {
        $qb = $this->createQueryBuilder('u');

        return $qb->select('u.id AS UserId', 'u.login AS Login', 'u.user_firstname AS UserFirstname', 'u.user_real_name AS UserRealName', 'm.member_id AS LicenceId', 'm.member_firstname AS MemberFirstname', 'm.member_name AS MemberName', 'c.club_name AS ClubName', 'u.user_last_activity AS Activity', 'u.user_status AS Status')
            ->leftJoin(Member::class, 'm', 'WITH', $qb->expr()->eq('u.user_member', 'm.member_id'))
            ->leftJoin(UserAccess::class, 'a', 'WITH', $qb->expr()->eq('u.id', 'a.user_access_user'))
            ->leftJoin(Club::class, 'c', 'WITH', $qb->expr()->eq('a.user_access_club', 'c.club_id'))
            ->where($qb->expr()->isNull('a.user_access_user'))
            ->orderBy('u.login', 'ASC')
            ->addOrderBy('a.user_access_club', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array|null
     */
    public function getCountActiveAccess(): ?array
    {
        $qb = $this->createQueryBuilder('u');

        return $qb->select($qb->expr()->count('u.id'))
            ->where($qb->expr()->lte('u.user_status', 4))
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @param UserInterface $user
     * @param string $newEncodedPassword
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
