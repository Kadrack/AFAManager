<?php
// src/Repository/MemberRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\Grade;
use App\Entity\Member;
use App\Entity\MemberLicence;
use App\Entity\MemberModification;
use App\Entity\Training;
use App\Entity\TrainingAttendance;
use App\Entity\TrainingSession;
use App\Entity\User;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

/**
 * @method Member|null find($id, $lockMode = null, $lockVersion = null)
 * @method Member|null findOneBy(array $criteria, array $orderBy = null)
 * @method Member[]    findAll()
 * @method Member[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Member::class);
    }

    public function getMemberAttendances(int $member_id): ?array
    {
        $qb = $this->createQueryBuilder('m');

        return $qb->select('t.training_id AS Id', 't.training_name AS Name', 's.training_session_date AS Date', 'sum(s.training_session_duration) AS Duration')
            ->join(TrainingAttendance::class, 'a', 'WITH', $qb->expr()->eq('m.member_id', 'a.training_attendance_member'))
            ->join(TrainingSession::class, 's', 'WITH', $qb->expr()->eq('a.training_attendance_session', 's.training_session_id'))
            ->join(Training::class, 't', 'WITH', $qb->expr()->eq('a.training', 't.training_id'))
            ->where($qb->expr()->eq('m.member_id', $member_id))
            ->groupBy('Id')
            ->orderBy('Date', 'DESC')
            ->getQuery()
            ->getArrayResult();

    }

    public function getClubActiveMembers(Club $club, string $today): ?array
    {
        $qb = $this->createQueryBuilder('m');

        return $qb->select('m.member_id AS Id', 'm.member_firstname AS FirstName', 'm.member_name AS Name', 'g.grade_rank AS Grade', 'l.member_licence_deadline AS Deadline', 'u.id AS User')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('m.member_last_grade', 'g.grade_id'))
            ->leftJoin(User::class, 'u', 'WITH', $qb->expr()->eq('m.member_id', 'u.user_member'))
            ->where($qb->expr()->eq('l.member_licence_club', $club->getClubId()))
            ->andWhere($qb->expr()->gt('l.member_licence_deadline', "'".$today."'"))
            ->andWhere($qb->expr()->eq('l.member_licence_status', 1))
            ->orderBy('FirstName', 'ASC')
            ->addOrderBy('Name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function getClubInactiveMembers(Club $club, string $today): ?array
    {
        $qb = $this->createQueryBuilder('m');

        return $qb->select('m.member_firstname AS FirstName', 'm.member_name AS Name', 'm.member_id AS Id', 'l.member_licence_deadline AS Deadline', 'l.member_licence_id AS Licence')
            ->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->where($qb->expr()->eq('l.member_licence_club', $club->getClubId()))
            ->andWhere($qb->expr()->lte('l.member_licence_deadline', "'".$today."'"))
            ->andWhere($qb->expr()->eq('l.member_licence_status', 1))
            ->orderBy('FirstName', 'ASC')
            ->addOrderBy('Name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function getMemberModification(): ?array
    {
        $qb = $this->createQueryBuilder('m');

        return $qb->select('m.member_id AS Member', 'm.member_firstname AS FirstName', 'm.member_name AS Name', 'l.member_modification_firstname AS NewFirstName', 'l.member_modification_name AS NewName', 'l.member_modification_birthday AS Birthday', 'l.member_modification_address AS Address', 'l.member_modification_zip AS Zip', 'l.member_modification_city AS City', 'l.member_modification_country AS Country', 'l.member_modification_email AS Email')
            ->join(MemberModification::class, 'l', 'WITH', $qb->expr()->eq('m.member_modification', 'l.member_modification_id'))
            ->where($qb->expr()->isNotNull('m.member_modification'))
            ->getQuery()
            ->getArrayResult();
    }

    public function getClubRenewForms(Club $club, string $start, string $end): ?array
    {
        $qb = $this->createQueryBuilder('m');

        return $qb->select('m.member_id AS Id', 'm.member_firstname AS FirstName', 'm.member_name AS Name', 'm.member_sex AS Sex', 'm.member_address AS Address', 'm.member_zip AS Zip', 'm.member_city AS City', 'm.member_country AS Country', 'm.member_phone AS Phone', 'm.member_birthday AS Birthday', 'm.member_email AS Email',  'g.grade_rank AS Grade', 'l.member_licence_deadline AS Deadline')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('m.member_last_grade', 'g.grade_id'))
            ->where($qb->expr()->eq('l.member_licence_club', $club->getClubId()))
            ->andWhere($qb->expr()->gte('l.member_licence_deadline', "'".$start."'"))
            ->andWhere($qb->expr()->lte('l.member_licence_deadline', "'".$end."'"))
            ->andWhere($qb->expr()->eq('l.member_licence_status', 1))
            ->orderBy('FirstName', 'ASC')
            ->addOrderBy('Name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function getFullSearchMembers(string $search): ?array
    {
        $qb = $this->createQueryBuilder('m');

        $qb->select('m.member_id AS Id', 'm.member_firstname AS FirstName', 'm.member_name AS Name', 'm.member_birthday AS Birthday', 'l.member_licence_deadline AS Deadline', 'c.club_name AS Club', 'c.club_id AS ClubId')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_last_licence', 'l.member_licence_id'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('m.member_actual_club', 'c.club_id'));

        if (ctype_digit($search))
        {
            $qb->where($qb->expr()->eq('m.member_id', $search));
        }
        else
        {
            $qb->where($qb->expr()->like('m.member_name', "'%".$search."%'"));
        }

        return $qb->orderBy('FirstName', 'ASC')
            ->addOrderBy('ClubId', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
