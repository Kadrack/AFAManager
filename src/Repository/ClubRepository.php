<?php
// src/Repository/ClubRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\ClubDojo;
use App\Entity\ClubHistory;
use App\Entity\ClubTeacher;
use App\Entity\Member;
use App\Entity\MemberLicence;

use DateTime;

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
    /**
     * ClubRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Club::class);
    }

    /**
     * @return array|null
     */
    public function getCreationDateList(): ?array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->select('c.club_id AS Id', 'c.club_name AS Name', 'c.club_creation AS Creation', 'h.club_history_update AS Affiliation')
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history_id', 'c.club_last_history'))
            ->where($qb->expr()->eq('h.club_history_status', 1))
            ->orderBy('c.club_name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array|null
     */
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

    /**
     * @return array|null
     */
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

    /**
     * @return array|null
     */
    public function getActiveClubsInformations(): ?array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->select('c.club_id AS Id', 'c.club_name AS Name','c.club_province AS Province', 'c.club_zip AS Zip', 'c.club_city AS City', 'c.club_address AS Address', 'd.club_dojo_zip AS ZipDojo', 'd.club_dojo_city AS CityDojo', 'd.club_dojo_street AS AddressDojo')
            ->join(ClubDojo::class, 'd', 'WITH', $qb->expr()->eq('d.club_dojo_club', 'c.club_id'))
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history_id', 'c.club_last_history'))
            ->where($qb->expr()->eq('h.club_history_status', 1))
            ->orderBy('c.club_province', 'ASC')
            ->addOrderBy('c.club_id', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array|null
     */
    public function getClubsListIAF(): ?array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->select('c.club_name AS Title', 'd.club_dojo_street AS Street', 'd.club_dojo_zip AS Zip', 'd.club_dojo_city AS City', 'c.club_url AS Website')
            ->join(ClubDojo::class, 'd', 'WITH', $qb->expr()->eq('d.club_dojo_club', 'c.club_id'))
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history_id', 'c.club_last_history'))
            ->where($qb->expr()->eq('h.club_history_status', 1))
            ->addOrderBy('c.club_id', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param int $list
     * @return array|null
     */
    public function getClubsMailsList(int $list): ?array
    {
        $qb = $this->createQueryBuilder('c');

        return match ($list)
        {
            1 => $qb->select('c.club_email_contact AS Mail')->distinct(true)
                ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history_id', 'c.club_last_history'))
                ->where($qb->expr()->eq('h.club_history_status', 1))
                ->getQuery()
                ->getArrayResult(),
            2 => $qb->select('m.member_email AS Mail')->distinct(true)
                ->join(ClubTeacher::class, 't', 'WITH', $qb->expr()->eq('t.club_teacher', 'c.club_id'))
                ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_id', 't.club_teacher_member'))
                ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history_id', 'c.club_last_history'))
                ->where($qb->expr()->eq('h.club_history_status', 1))
                ->andWhere($qb->expr()->eq('t.club_teacher_title', 1))
                ->getQuery()
                ->getArrayResult(),
            default => array(),
        };
    }

    /**
     * @param DateTime $referenceDate
     * @return array|null
     */
    public function getProvinceTeachersTotal(DateTime $referenceDate): ?array
    {
        $deadline = date('Y-m-d', $referenceDate->getTimestamp());

        $deadline_high = date('Y-m-d', strtotime('+1 year', $referenceDate->getTimestamp()));

        $qb = $this->createQueryBuilder('c');

        return $qb->select('c.club_province AS Province', 'count(m.member_id) AS Total')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_actual_club', 'c.club_id'))
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->leftJoin(ClubTeacher::class, 't', 'WITH', $qb->expr()->eq('t.club_teacher_member', 'm.member_id'))
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history', 'm.member_actual_club'))
            ->where($qb->expr()->gt('l.member_licence_deadline', "'".$deadline."'"))
            ->andWhere($qb->expr()->lte('l.member_licence_deadline', "'".$deadline_high."'"))
            ->andWhere($qb->expr()->eq('h.club_history_status', 1))
            ->andWhere($qb->expr()->isNotNull('t.club_teacher_id'))
            ->groupBy('c.club_province')
            ->orderBy('Province', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param DateTime $referenceDate
     * @return array|null
     */
    public function getProvinceMembersTotal(DateTime $referenceDate): ?array
    {
        $deadline = date('Y-m-d', $referenceDate->getTimestamp());

        $deadline_high = date('Y-m-d', strtotime('+1 year', $referenceDate->getTimestamp()));

        $qb = $this->createQueryBuilder('c');

        return $qb->select('c.club_province AS Province', 'm.member_sex AS Sex', 'count(m.member_id) AS Total')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_actual_club', 'c.club_id'))
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->leftJoin(ClubTeacher::class, 't', 'WITH', $qb->expr()->eq('t.club_teacher_member', 'm.member_id'))
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history', 'm.member_actual_club'))
            ->where($qb->expr()->gt('l.member_licence_deadline', "'".$deadline."'"))
            ->andWhere($qb->expr()->lte('l.member_licence_deadline', "'".$deadline_high."'"))
            ->andWhere($qb->expr()->eq('h.club_history_status', 1))
            ->andWhere($qb->expr()->isNull('t.club_teacher_id'))
            ->groupBy('c.club_province')
            ->addGroupBy('m.member_sex')
            ->orderBy('Province', 'ASC')
            ->addOrderBy('Sex', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param DateTime|null $referenceDate
     * @return array|null
     */
    public function getProvinceMembersCount(?DateTime $referenceDate = null): ?array
    {
        $result = array();

        $deadline = date('Y-m-d', $referenceDate->getTimestamp());

        $deadline_high = date('Y-m-d', strtotime('+1 year', $referenceDate->getTimestamp()));

        $limit[0] = date('Y-m-d', strtotime('-0 years', $referenceDate->getTimestamp()));
        $limit[1] = date('Y-m-d', strtotime('-6 years', $referenceDate->getTimestamp()));
        $limit[2] = date('Y-m-d', strtotime('-12 years', $referenceDate->getTimestamp()));
        $limit[3] = date('Y-m-d', strtotime('-18 years', $referenceDate->getTimestamp()));
        $limit[4] = date('Y-m-d', strtotime('-25 years', $referenceDate->getTimestamp()));
        $limit[5] = date('Y-m-d', strtotime('-35 years', $referenceDate->getTimestamp()));

        $qb = $this->createQueryBuilder('c');

        for ($i = 0; $i < count($limit)-1; $i++)
        {
            $result[] = $qb->select('c.club_province AS Province', 'm.member_sex AS Sex', 'count(m.member_id) AS Total')
                ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_actual_club', 'c.club_id'))
                ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
                ->leftJoin(ClubTeacher::class, 't', 'WITH', $qb->expr()->eq('t.club_teacher_member', 'm.member_id'))
                ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history', 'm.member_actual_club'))
                ->where($qb->expr()->gt('l.member_licence_deadline', "'".$deadline."'"))
                ->andWhere($qb->expr()->lte('l.member_licence_deadline', "'".$deadline_high."'"))
                ->andWhere($qb->expr()->eq('h.club_history_status', 1))
                ->andWhere($qb->expr()->between('m.member_birthday', "'".$limit[$i+1]."'", "'".$limit[$i]."'"))
                ->groupBy('c.club_province')
                ->addGroupBy('m.member_sex')
                ->orderBy('Province', 'ASC')
                ->addOrderBy('Sex', 'ASC')
                ->getQuery()
                ->getArrayResult();

            $qb = $this->createQueryBuilder('c');
        }

        $result[] = $qb->select('c.club_province AS Province', 'm.member_sex AS Sex', 'count(m.member_id) AS Total')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_actual_club', 'c.club_id'))
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->leftJoin(ClubTeacher::class, 't', 'WITH', $qb->expr()->eq('t.club_teacher_member', 'm.member_id'))
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history', 'm.member_actual_club'))
            ->where($qb->expr()->gt('l.member_licence_deadline', "'".$deadline."'"))
            ->andWhere($qb->expr()->lte('l.member_licence_deadline', "'".$deadline_high."'"))
            ->andWhere($qb->expr()->eq('h.club_history_status', 1))
            ->andWhere($qb->expr()->lte('m.member_birthday', "'".$limit[count($limit)-1]."'"))
            ->groupBy('c.club_province')
            ->addGroupBy('m.member_sex')
            ->orderBy('Province', 'ASC')
            ->addOrderBy('Sex', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return $result;
    }

    /**
     * @param int $province
     * @param DateTime $referenceDate
     * @return array|null
     */
    public function getClubMembersCount(int $province, DateTime $referenceDate): ?array
    {
        $result = array();

        $deadline = date('Y-m-d', $referenceDate->getTimestamp());

        $deadline_high = date('Y-m-d', strtotime('+1 year', $referenceDate->getTimestamp()));

        $limit[0] = date('Y-m-d', strtotime('-0 years', $referenceDate->getTimestamp()));
        $limit[1] = date('Y-m-d', strtotime('-6 years', $referenceDate->getTimestamp()));
        $limit[2] = date('Y-m-d', strtotime('-12 years', $referenceDate->getTimestamp()));
        $limit[3] = date('Y-m-d', strtotime('-18 years', $referenceDate->getTimestamp()));
        $limit[4] = date('Y-m-d', strtotime('-25 years', $referenceDate->getTimestamp()));
        $limit[5] = date('Y-m-d', strtotime('-35 years', $referenceDate->getTimestamp()));

        $qb = $this->createQueryBuilder('c');

        $result['Clubs'] = $qb->select('c.club_id AS Id', 'c.club_name AS Name')
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history_id', 'c.club_last_history'))
            ->where($qb->expr()->eq('h.club_history_status', 1))
            ->andWhere($qb->expr()->eq('c.club_province', $province))
            ->orderBy('c.club_province', 'ASC')
            ->addOrderBy('c.club_id', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $qb = $this->createQueryBuilder('c');

        for ($i = 0; $i < count($limit)-1; $i++)
        {
            $result['Details'][] = $qb->select('c.club_id AS Id', 'c.club_name AS Name', 'm.member_sex AS Sex', 'count(m.member_id) AS Total')
                ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_actual_club', 'c.club_id'))
                ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
                ->leftJoin(ClubTeacher::class, 't', 'WITH', $qb->expr()->eq('t.club_teacher_member', 'm.member_id'))
                ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history', 'm.member_actual_club'))
                ->where($qb->expr()->gt('l.member_licence_deadline', "'".$deadline."'"))
                ->andWhere($qb->expr()->lte('l.member_licence_deadline', "'".$deadline_high."'"))
                ->andWhere($qb->expr()->eq('h.club_history_status', 1))
                ->andWhere($qb->expr()->between('m.member_birthday', "'".$limit[$i+1]."'", "'".$limit[$i]."'"))
                ->andWhere($qb->expr()->eq('c.club_province', $province))
                ->groupBy('c.club_id')
                ->addGroupBy('m.member_sex')
                ->orderBy('Id', 'ASC')
                ->getQuery()
                ->getArrayResult();

            $qb = $this->createQueryBuilder('c');
        }

        $result['Details'][] = $qb->select('c.club_id AS Id', 'c.club_name AS Name', 'm.member_sex AS Sex', 'count(m.member_id) AS Total')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_actual_club', 'c.club_id'))
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->leftJoin(ClubTeacher::class, 't', 'WITH', $qb->expr()->eq('t.club_teacher_member', 'm.member_id'))
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history', 'm.member_actual_club'))
            ->where($qb->expr()->gt('l.member_licence_deadline', "'".$deadline."'"))
            ->andWhere($qb->expr()->lte('l.member_licence_deadline', "'".$deadline_high."'"))
            ->andWhere($qb->expr()->eq('h.club_history_status', 1))
            ->andWhere($qb->expr()->lte('m.member_birthday', "'".$limit[count($limit)-1]."'"))
            ->andWhere($qb->expr()->eq('c.club_province', $province))
            ->groupBy('c.club_id')
            ->addGroupBy('m.member_sex')
            ->orderBy('Id', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return $result;
    }
}
