<?php
// src/Repository/MemberRepository.php
namespace App\Repository;

use App\Entity\UserAuditTrail;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserAuditTrail|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAuditTrail|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAuditTrail[]    findAll()
 * @method UserAuditTrail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAuditTrailRepository extends ServiceEntityRepository
{
    /**
     * UserAuditTrailRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAuditTrail::class);
    }
}
