<?php

namespace App\Repository;

use App\Entity\Subgenre;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Subgenre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subgenre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subgenre[]    findAll()
 * @method Subgenre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubgenreRepository extends AdminEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Subgenre::class);
    }

//    /**
//     * @return Category[] Returns an array of Category objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function createFormQueryBuilder()
    {
        return $this->createQueryBuilder('subgenre')->innerJoin('subgenre.events', 'event')->orderBy('subgenre.name');
    }

    public function createDynamicFormQueryBuilder($startDate, $endDate)
    {
        return $this->createQueryBuilder('subgenre')
            ->innerJoin('subgenre.events', 'event')
            ->andWhere('event.startDate >= :startDate')
            ->andWhere('event.endDate >= :endDate')
            ->andWhere('event.status = :val')
            ->setParameters(array('startDate'=> $startDate, 'endDate'=> $endDate, 'val' => Event::STATUS_APPROVED))
            ->orderBy('subgenre.name');

    }

    public function getDynamicFilterOptions($params)
    {
        $qb = $this->setDynamicCriteria($params);

        return $qb
            ->select('subgenre.name', 'subgenre.id')
            ->groupby('subgenre.name','subgenre.id')
            ->getQuery()
            ->getArrayResult();
    }

    public function setDynamicCriteria($params)
    {
        $qb = $this->createQueryBuilder('subgenre')
            ->innerJoin('subgenre.events', 'event')
            ->innerJoin('subgenre.genre', 'genre');

        if (count($params['genre'])>0) {
            $entityList = [];
            foreach ($params['genre'] as $entity) {
                $entityList[] = $entity;
            }
            $qb->andWhere($qb->expr()->in('genre', $entityList));
        }

        if (count($params['type'])>0) {
            $entityList = [];
            foreach ($params['type'] as $entity) {
                $entityList[] = $entity;
            }
            $qb->andWhere($qb->expr()->in('event.type', $entityList));
        }

        $qb
            ->andWhere('event.startDate >= :start')
            ->andWhere('event.startDate <= :end')
            ->andWhere('event.status = :status')
            ->setParameters(
                [
                    'start'=> $params['periodStart'],
                    'end'=> $params['periodEnd'],
                    'status' => Event::STATUS_APPROVED,
                ]
            );

        return $qb;
    }
}
