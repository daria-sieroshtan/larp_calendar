<?php

namespace App\Repository;

use App\Entity\Genre;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Genre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Genre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Genre[]    findAll()
 * @method Genre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenreRepository extends AdminEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Genre::class);
    }

//    /**
//     * @return Genre[] Returns an array of Genre objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Genre
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function createFormQueryBuilder()
    {
        return
            $this->createQueryBuilder('genre')
            ->innerJoin('genre.subgenres', 'subgenre')
            ->innerJoin('subgenre.events', 'event');
    }

    public function getDynamicFilterOptions($params)
    {
        $qb = $this->setDynamicCriteria($params);

        return $qb
            ->select('genre.name', 'genre.id')
                ->groupby('genre.name','genre.id')
                ->getQuery()
                ->getArrayResult();
    }

    public function setDynamicCriteria($params)
    {
        $qb = $this->createQueryBuilder('genre')
            ->innerJoin('genre.subgenres', 'subgenre')
            ->innerJoin('subgenre.events', 'event');

        if (count($params['type'])>0) {
            $entityList = [];
            foreach ($params['type'] as $entity) {
                $entityList[] = $entity;
            }
            $qb->andWhere($qb->expr()->in('event.type', $entityList));
        }

        if (count($params['subgenre'])>0) {
            $entityList = [];
            foreach ($params['subgenre'] as $entity) {
                $entityList[] = $entity;
            }
            $qb->andWhere($qb->expr()->in('event.subgenre', $entityList));
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
