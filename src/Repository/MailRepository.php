<?php
// src/Repository/MailRepository.php
namespace App\Repository;

use App\Entity\Mail;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Mail|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mail|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mail[]    findAll()
 * @method Mail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MailRepository extends ServiceEntityRepository
{
    /**
     * MailRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mail::class);
    }
}
