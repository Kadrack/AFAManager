<?php
// src/Repository/TrainingAttendanceRepositorytory.php
namespace App\Repository;

use App\Entity\Member;
use App\Entity\MemberLicence;
use App\Entity\TrainingAttendance;
use App\Entity\TrainingSession;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrainingAttendance|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrainingAttendance|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrainingAttendance[]    findAll()
 * @method TrainingAttendance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainingAttendanceRepository extends ServiceEntityRepository
{
    /**
     * TrainingAttendanceRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainingAttendance::class);
    }

    /**
     * @param int $training_id
     * @return array|null
     */
    public function getPractitioners(int $training_id): ?array
    {
        $qb = $this->createQueryBuilder('a');

        return $qb->select('m.member_firstname', 'm.member_name', 'm.member_id', 'l.member_licence_deadline','a.training_attendance_unique', 'a.training_attendance_payment', 'a.training_attendance_payment_type')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('a.training_attendance_member', 'm.member_id'))
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_last_licence', 'l.member_licence_id'))
            ->where($qb->expr()->isNotNull('a.training_attendance_payment'))
            ->andWhere($qb->expr()->eq('a.training', $training_id))
            ->andWhere($qb->expr()->isNotNull('a.training_attendance_member'))
            ->orderBy('m.member_firstname', 'ASC')
            ->addOrderBy('m.member_name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param int $training_id
     * @return array|null
     */
    public function getPractitionersSessions(int $training_id): ?array
    {
        $qb = $this->createQueryBuilder('a');

        return $qb->select('s.training_session_date', 's.training_session_starting_hour', 's.training_session_duration', 'a.training_attendance_unique')
            ->join(TrainingSession::class, 's', 'WITH', $qb->expr()->eq('a.training_attendance_session', 's.training_session_id'))
            ->where($qb->expr()->eq('a.training', $training_id))
            ->andWhere($qb->expr()->isNotNull('a.training_attendance_member'))
            ->orderBy('s.training_session_date', 'ASC')
            ->addOrderBy('s.training_session_starting_hour', 'ASC')
            ->addOrderBy('s.training_session_duration', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param int $training_id
     * @return array|null
     */
    public function getForeignPractitioners(int $training_id): ?array
    {
        $qb = $this->createQueryBuilder('a');

        return $qb->where($qb->expr()->isNotNull('a.training_attendance_payment'))
            ->andWhere($qb->expr()->eq('a.training', $training_id))
            ->andWhere($qb->expr()->isNull('a.training_attendance_member'))
            ->orderBy('a.training_attendance_name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param int $training_id
     * @return array|null
     */
    public function getForeignPractitionersSessions(int $training_id): ?array
    {
        $qb = $this->createQueryBuilder('a');

        return $qb->select('s.training_session_date', 's.training_session_starting_hour', 's.training_session_duration', 'a.training_attendance_unique')
            ->join(TrainingSession::class, 's', 'WITH', $qb->expr()->eq('a.training_attendance_session', 's.training_session_id'))
            ->where($qb->expr()->eq('a.training', $training_id))
            ->andWhere($qb->expr()->isNull('a.training_attendance_member'))
            ->orderBy('s.training_session_date', 'ASC')
            ->addOrderBy('s.training_session_starting_hour', 'ASC')
            ->addOrderBy('s.training_session_duration', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param int $training_id
     * @return array|null
     */
    public function getPayments(int $training_id): ?array
    {
        $qb = $this->createQueryBuilder('a');

        return $qb->select('a.training_attendance_payment AS Payment', 'a.training_attendance_payment_type AS Type')
            ->where($qb->expr()->eq('a.training', $training_id))
            ->getQuery()
            ->getArrayResult();
    }
}
