<?php
// src/Repository/TrainingSessionRepositorytory.php
namespace App\Repository;

use App\Entity\TrainingSession;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrainingSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrainingSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrainingSession[]    findAll()
 * @method TrainingSession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainingSessionRepository extends ServiceEntityRepository
{
    /**
     * TrainingSessionRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainingSession::class);
    }

    /**
     * @param int $training_id
     * @return array|null
     */
    public function getTrainingSessions(int $training_id): ?array
    {
        $qb = $this->createQueryBuilder('s');

        return $qb->select('s.training_session_id', 's.training_session_date', 's.training_session_starting_hour', 's.training_session_ending_hour', 's.training_session_duration')//, $qb->expr()->count('a.training_attendance_payment'))
//            ->join(TrainingAttendance::class, 'a', 'WITH', $qb->expr()->eq('a.training_attendance_session', 's.training_session_id'))
            ->groupBy('s.training_session_id')
            ->where($qb->expr()->eq('s.training', $training_id))
            ->orderBy('s.training_session_date', 'ASC')
            ->addOrderBy('s.training_session_starting_hour', 'ASC')
            ->addOrderBy('s.training_session_duration', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
