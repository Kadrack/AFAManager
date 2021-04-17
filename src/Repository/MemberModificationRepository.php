<?php
// src/Repository/MemberRepository.php
namespace App\Repository;

use App\Entity\MemberModification;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MemberModification|null find($id, $lockMode = null, $lockVersion = null)
 * @method MemberModification|null findOneBy(array $criteria, array $orderBy = null)
 * @method MemberModification[]    findAll()
 * @method MemberModification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberModificationRepository extends ServiceEntityRepository
{
    /**
     * MemberModificationRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MemberModification::class);
    }
}
