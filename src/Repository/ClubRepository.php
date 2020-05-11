<?php
// src/Repository/ClubRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\ClubHistory;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Club|null find($id, $lockMode = null, $lockVersion = null)
 * @method Club|null findOneBy(array $criteria, array $orderBy = null)
 * @method Club[]    findAll()
 * @method Club[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClubRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Club::class);
    }

    public function getActiveClubs(): ?array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->select('c.club_id AS Id', 'c.club_name AS Name','c.club_province AS Province')
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history_id', 'c.club_last_history'))
            ->where($qb->expr()->eq('h.club_history_status', 1))
            ->orderBy('c.club_province', 'ASC')
            ->addOrderBy('c.club_id', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function getInactiveClubs(): ?array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->select('c.club_id AS Id', 'c.club_name AS Name','c.club_province AS Province')
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history_id', 'c.club_last_history'))
            ->where($qb->expr()->neq('h.club_history_status', 1))
            ->orderBy('c.club_id', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
