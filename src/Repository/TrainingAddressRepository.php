<?php
// src/Repository/TrainingAddressRepositorytory.php
namespace App\Repository;

use App\Entity\TrainingAddress;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrainingAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrainingAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrainingAddress[]    findAll()
 * @method TrainingAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainingAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainingAddress::class);
    }
}
