<?php
// src/Repository/SecretariatSupporterRepository.php
namespace App\Repository;

use App\Entity\SecretariatSupporter;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SecretariatSupporter|null find($id, $lockMode = null, $lockVersion = null)
 * @method SecretariatSupporter|null findOneBy(array $criteria, array $orderBy = null)
 * @method SecretariatSupporter[]    findAll()
 * @method SecretariatSupporter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SecretariatSupporterRepository extends ServiceEntityRepository
{
    /**
     * SecretariatSupporterRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SecretariatSupporter::class);
    }
}
