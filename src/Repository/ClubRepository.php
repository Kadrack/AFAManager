<?php
// src/Repository/ClubRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\ClubHistory;
use App\Entity\MemberLicence;
use App\Entity\Member;

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

        return $qb->select('c.club_number AS Number', 'c.club_name AS Name','c.club_province AS Province')
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history_id', 'c.club_last_history'))
            ->where($qb->expr()->eq('h.club_history_status', 1))
            ->orderBy('c.club_province', 'ASC')
            ->addOrderBy('c.club_number', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function getInactiveClubs(): ?array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->select('c.club_number AS Number', 'c.club_name AS Name','c.club_province AS Province')
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history_id', 'c.club_last_history'))
            ->where($qb->expr()->neq('h.club_history_status', 1))
            ->orderBy('c.club_number', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function getClubsTotalMembers(): ?array
    {
        $qb = $this->createQueryBuilder('c');

        $today = new \DateTime('today');

        return $qb->select('c.club_number AS Number', 'c.club_name AS Name', 'c.club_province AS Province', $qb->expr()->count('(l.licence_club)').' AS Members')
                ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('l.licence_club', 'c.club_id'))
                ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_id', 'l.licence_member'))
                ->where($qb->expr()->gt('l.licence_deadline', "'".$today->format('Y-m-d')."'"))
                ->andWhere($qb->expr()->orX($qb->expr()->eq('l.licence_status', 1), $qb->expr()->eq('l.licence_status', 3)))
                //->groupBy('c.club_province')
                ->getQuery()
                ->getArrayResult();        
    }    
}
