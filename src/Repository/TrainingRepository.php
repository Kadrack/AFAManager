<?php
// src/Repository/TrainingRepository.php
namespace App\Repository;

use App\Entity\Training;
use App\Entity\TrainingSession;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Training|null find($id, $lockMode = null, $lockVersion = null)
 * @method Training|null findOneBy(array $criteria, array $orderBy = null)
 * @method Training[]    findAll()
 * @method Training[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainingRepository extends ServiceEntityRepository
{
    /**
     * TrainingRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Training::class);
    }

    /**
     * @param int $type
     * @return array|null
     */
    public function getActiveTrainings(int $type): ?array
    {
        $qb = $this->createQueryBuilder('t');

        return $qb->select('t.training_id', 's.training_session_date', 't.training_name', 't.training_total_sessions')
            ->join(TrainingSession::class, 's', 'WITH', $qb->expr()->eq('t.training_id', 's.training'))
            ->groupBy('t.training_id')
            ->where($qb->expr()->eq('t.training_type', $type))
            ->orderBy('s.training_session_date', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }
}
