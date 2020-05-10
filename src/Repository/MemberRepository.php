<?php
// src/Repository/MemberRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\Member;
use App\Entity\MemberLicence;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Member|null find($id, $lockMode = null, $lockVersion = null)
 * @method Member|null findOneBy(array $criteria, array $orderBy = null)
 * @method Member[]    findAll()
 * @method Member[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Member::class);
    }

    public function getClubActiveMembers(Club $club, string $today): ?array
    {
        $qb = $this->createQueryBuilder('m');
        return $qb->select('m.member_firstname AS FirstName', 'm.member_name AS Name', 'm.member_id AS Id', 'l.member_licence_deadline AS Deadline')
            ->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->where($qb->expr()->eq('l.member_licence_club', $club->getClubId()))
            ->andWhere($qb->expr()->gt('l.member_licence_deadline', "'".$today."'"))
            ->andWhere($qb->expr()->eq('l.member_licence_status', 1))
            ->orderBy('FirstName', 'ASC')
            ->addOrderBy('Name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function getClubInactiveMembers(Club $club, string $today): ?array
    {
        $qb = $this->createQueryBuilder('m');
        return $qb->select('m.member_firstname AS FirstName', 'm.member_name AS Name', 'm.member_id AS Id', 'l.member_licence_deadline AS Deadline', 'l.member_licence_id AS Licence')
            ->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->where($qb->expr()->eq('l.member_licence_club', $club->getClubId()))
            ->andWhere($qb->expr()->lte('l.member_licence_deadline', "'".$today."'"))
            ->andWhere($qb->expr()->eq('l.member_licence_status', 1))
            ->orderBy('FirstName', 'ASC')
            ->addOrderBy('Name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
