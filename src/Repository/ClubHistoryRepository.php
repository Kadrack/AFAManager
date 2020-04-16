<?php
// src/Repository/ClubHistoryRepository.php
namespace App\Repository;

use App\Entity\ClubHistory;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClubHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClubHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClubHistory[]    findAll()
 * @method ClubHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClubHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClubHistory::class);
    }
}
