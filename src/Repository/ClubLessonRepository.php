<?php
// src/Repository/ClubLessonRepository.php
namespace App\Repository;

use App\Entity\ClubLesson;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClubLesson|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClubLesson|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClubLesson[]    findAll()
 * @method ClubLesson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClubLessonRepository extends ServiceEntityRepository
{
    /**
     * ClubLessonRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClubLesson::class);
    }
}
