<?php
// src/Repository/GradeSessionRepository.php
namespace App\Repository;

use App\Entity\GradeSession;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GradeSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method GradeSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method GradeSession[]    findAll()
 * @method GradeSession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradeSessionRepository extends ServiceEntityRepository
{
    /**
     * GradeSessionRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GradeSession::class);
    }

    /**
     * @param string $today
     * @return array|null
     */
    public function getOpenSession(string $today): ?array
    {
        $qb = $this->createQueryBuilder('e');

        return $qb->select()
            ->where($qb->expr()->eq('e.grade_session_type', 1))
            ->andWhere($qb->expr()->gt("'".$today."'", 'e.grade_session_candidate_open'))
            ->andWhere($qb->expr()->lt("'".$today."'", 'e.grade_session_candidate_close'))
            ->getQuery()
            ->getResult();
    }
}
