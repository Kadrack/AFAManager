<?php
// src/Repository/MemberPrintoutRepository.php
namespace App\Repository;

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MemberPrintout::class);
    }
}
