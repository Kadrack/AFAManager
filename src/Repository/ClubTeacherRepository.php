<?php
// src/Repository/ClubTeacherRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\ClubHistory;
use App\Entity\ClubTeacher;
use App\Entity\Grade;
use App\Entity\GradeTitle;
use App\Entity\Member;

use App\Entity\MemberLicence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClubTeacher|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClubTeacher|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClubTeacher[]    findAll()
 * @method ClubTeacher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClubTeacherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClubTeacher::class);
    }

    public function getDojoChoStartingPractice(): ?array
    {
        $qb = $this->createQueryBuilder('t');

        return $qb->select('m.member_firstname AS Firstname', 'm.member_name AS Name', 'l.member_licence_medical_certificate AS Starting')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_id', 't.club_teacher_member'))
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_first_licence', 'l.member_licence_id'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('c.club_id', 't.club_teacher'))
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history_id', 'c.club_last_history'))
            ->where($qb->expr()->eq('t.club_teacher_title', 1))
            ->andWhere($qb->expr()->eq('h.club_history_status', 1))
            ->orderBy('Firstname', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function getAFATeachers(Club $club): ?array
    {
        $qb = $this->createQueryBuilder('t');

        return $qb->select('t.club_teacher_id AS Id', 't.club_teacher_title AS Title', 't.club_teacher_type AS Type', 'm.member_firstname AS Firstname', 'm.member_name AS Name', 'g.grade_rank AS Grade', 'm.member_id AS Licence', 'gt.grade_title_rank AS GradeTitleAikikai', 'gt.grade_title_rank AS GradeTitleAdeps')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_id', 't.club_teacher_member'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('m.member_last_grade', 'g.grade_id'))
            ->leftJoin(GradeTitle::class, 'gt', 'WITH', $qb->expr()->eq('m.member_id', 'gt.grade_title_member'))
            ->where($qb->expr()->IsNotNull('t.club_teacher_member'))
            ->andWhere($qb->expr()->eq('t.club_teacher', $club->getClubId()))
            ->orderBy('Title', 'ASC')
            ->addOrderBy('Firstname', 'ASC')
            ->addOrderBy('Name', 'ASC')
            ->addOrderBy('GradeTitleAikikai', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    public function getForeignTeachers(Club $club): ?array
    {
        $qb = $this->createQueryBuilder('t');

        return $qb->select('t.club_teacher_id AS Id', 't.club_teacher_title AS Title', 't.club_teacher_type AS Type', 't.club_teacher_firstname AS Firstname', 't.club_teacher_name AS Name', 't.club_teacher_grade AS Grade', 't.club_teacher_grade_title_aikikai AS GradeTitleAikikai', 't.club_teacher_grade_title_adeps AS GradeTitleAdeps')
            ->where($qb->expr()->IsNull('t.club_teacher_member'))
            ->andWhere($qb->expr()->eq('t.club_teacher', $club->getClubId()))
            ->orderBy('Title', 'ASC')
            ->addOrderBy('Grade', 'ASC')
            ->addOrderBy('Firstname', 'ASC')
            ->addOrderBy('Name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
