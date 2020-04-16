<?php
// src/Repository/GradeDanRepository.php
namespace App\Repository;

use App\Entity\GradeSession;
use App\Entity\GradeDan;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GradeDan|null find($id, $lockMode = null, $lockVersion = null)
 * @method GradeDan|null findOneBy(array $criteria, array $orderBy = null)
 * @method GradeDan[]    findAll()
 * @method GradeDan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradeDanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GradeDan::class);
    }

    public function getGradeDanHistory(int $member_id): ?array
    {
        $qb = $this->createQueryBuilder('r');

        return $qb->select('r.grade_dan_id AS Id', 'r.grade_dan_rank AS Rank', 'e.grade_session_date AS Date', 'r.grade_dan_status AS Result', 'r.grade_dan_certificate AS Certificate', 'e.grade_session_type AS Type', 'e.grade_session_id AS Session')
            ->join(GradeSession::class, 'e', 'WITH', $qb->expr()->eq('r.grade_dan_exam', 'e.grade_session_id'))
            ->where($qb->expr()->eq('r.grade_dan_member', $member_id))
            ->orderBy('e.grade_session_date', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }
}
