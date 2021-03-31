<?php
// src/Repository/CommissionMemberRepository.php
namespace App\Repository;

use App\Entity\CommissionMember;
use App\Entity\Member;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommissionMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommissionMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommissionMember[]    findAll()
 * @method CommissionMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommissionMemberRepository extends ServiceEntityRepository
{
    /**
     * CommissionMemberRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommissionMember::class);
    }

    /**
     * @param int $commission
     * @return array|null
     */
    public function getCommissionMembers(int $commission): ?array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->select('c.commission_member_id', 'm.member_id AS Id', 'm.member_firstname AS Firstname', 'm.member_name AS Name')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('c.commission_member', 'm.member_id'))
            ->where($qb->expr()->eq('c.commission', $commission))
            ->andWhere($qb->expr()->isNull('c.commission_member_date_out'))
            ->orderBy('m.member_firstname', 'ASC')
            ->addOrderBy('m.member_name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
