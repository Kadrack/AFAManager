<?php
// src/Repository/GradeRepository.php
namespace App\Repository;

use App\Entity\GradeSession;
use App\Entity\Grade;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Grade|null find($id, $lockMode = null, $lockVersion = null)
 * @method Grade|null findOneBy(array $criteria, array $orderBy = null)
 * @method Grade[]    findAll()
 * @method Grade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradeRepository extends ServiceEntityRepository
{
    /**
     * GradeRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Grade::class);
    }

    /**
     * @param int $member_id
     * @return array|null
     */
    public function getGradeHistory(int $member_id): ?array
    {
        $qb = $this->createQueryBuilder('r');

        return $qb->select('r.grade_id AS Id', 'r.grade_rank AS Rank', 'r.grade_date AS Date', 'r.grade_status AS Result', 'r.grade_certificate AS Certificate', 'e.grade_session_type AS Type', 'e.grade_session_id AS Session')
            ->leftJoin(GradeSession::class, 'e', 'WITH', $qb->expr()->eq('r.grade_exam', 'e.grade_session_id'))
            ->where($qb->expr()->eq('r.grade_member', $member_id))
            ->orderBy('Rank', 'DESC')
            ->addOrderBy('e.grade_session_date', 'DESC')
            ->addOrderBy('Date', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }
}
