<?php
// src/Repository/ClubDojoRepositorytory.php
namespace App\Repository;

use App\Entity\ClubDojo;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClubDojo|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClubDojo|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClubDojo[]    findAll()
 * @method ClubDojo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClubDojoRepository extends ServiceEntityRepository
{
    /**
     * ClubDojoRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClubDojo::class);
    }
}
