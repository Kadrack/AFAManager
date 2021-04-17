<?php
// src/Repository/MemberLicenceRepository.php
namespace App\Repository;

use App\Entity\MemberLicence;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MemberLicence|null find($id, $lockMode = null, $lockVersion = null)
 * @method MemberLicence|null findOneBy(array $criteria, array $orderBy = null)
 * @method MemberLicence[]    findAll()
 * @method MemberLicence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberLicenceRepository extends ServiceEntityRepository
{
    /**
     * MemberLicenceRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MemberLicence::class);
    }
}
