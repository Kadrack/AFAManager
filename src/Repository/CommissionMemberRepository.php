<?php
// src/Repository/CommissionMemberRepository.php
namespace App\Repository;

use App\Entity\CommissionMember;

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommissionMember::class);
    }
}
