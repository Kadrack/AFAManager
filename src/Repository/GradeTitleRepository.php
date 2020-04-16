<?php
// src/Repository/GradeTitleRepository.php
namespace App\Repository;

use App\Entity\GradeTitle;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GradeTitle|null find($id, $lockMode = null, $lockVersion = null)
 * @method GradeTitle|null findOneBy(array $criteria, array $orderBy = null)
 * @method GradeTitle[]    findAll()
 * @method GradeTitle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradeTitleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GradeTitle::class);
    }
}
