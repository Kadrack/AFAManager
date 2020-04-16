<?php
// src/Repository/GradeKyuRepository.php
namespace App\Repository;

use App\Entity\GradeKyu;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GradeKyu|null find($id, $lockMode = null, $lockVersion = null)
 * @method GradeKyu|null findOneBy(array $criteria, array $orderBy = null)
 * @method GradeKyu[]    findAll()
 * @method GradeKyu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradeKyuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GradeKyu::class);
    }
}
