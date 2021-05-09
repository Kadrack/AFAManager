<?php
// src/Repository/MemberPrintoutRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\Member;
use App\Entity\MemberLicence;
use App\Entity\MemberPrintout;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MemberPrintout|null find($id, $lockMode = null, $lockVersion = null)
 * @method MemberPrintout|null findOneBy(array $criteria, array $orderBy = null)
 * @method MemberPrintout[]    findAll()
 * @method MemberPrintout[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberPrintoutRepository extends ServiceEntityRepository
{
    /**
     * MemberPrintoutRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MemberPrintout::class);
    }

    /**
     * @param \App\Entity\Club|null $club
     * @return array|null
     */
    public function getStampForPrinting(?Club $club=null): ?array
    {
        $qb = $this->createQueryBuilder('p');

        $qb->select('m.member_id AS Id', 'm.member_firstname AS FirstName', 'm.member_name AS Name', 'l.member_licence_deadline AS Deadline', 'l.member_licence_update AS Date', 'l.member_licence_payment_value AS Payment', 'c.club_id AS ClubId', 'c.club_name AS ClubName', 'l.member_licence_id AS RenewId', 'l.member_licence_status AS Status', 'p.member_printout_id AS StampId')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('p.member_printout_licence', 'l.member_licence_id'))
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('l.member_licence_club', 'c.club_id'))
            ->where($qb->expr()->isNull('p.member_printout_done'));

        is_null($club) ?: $qb->andWhere($qb->expr()->eq('l.member_licence_club', $club->getClubId()));

        return $qb->orderBy('p.member_printout_creation', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
