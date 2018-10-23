<?php

namespace App\Repository;

use App\Entity\Weapon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Weapon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Weapon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Weapon[]    findAll()
 * @method Weapon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WeaponRepository extends AdminEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Weapon::class);
    }

//    /**
//     * @return Weapon[] Returns an array of Weapon objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Weapon
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function createFormQueryBuilder()
    {
        return $this->createQueryBuilder('weapon')->innerJoin('weapon.events', 'event');
    }
}