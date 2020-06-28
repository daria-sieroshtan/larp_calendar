<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Event::class);
    }

//    /**
//     * @return Event[] Returns an array of Event objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findLatest($numberOfItems)
    {
        return $this->createQueryBuilder('event')
            ->andWhere('event.status = :val')
            ->setParameter('val', Event::STATUS_APPROVED)
            ->orderBy('event.publishDate', 'DESC')
            ->setMaxResults($numberOfItems)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function findListForPagination()
    {
        return $this->createQueryBuilder('event')
            ->andWhere('event.startDate >= :today')
            ->andWhere('event.status = :val')
            ->setParameters(array('today'=> date('Y-m-d'), 'val' => Event::STATUS_APPROVED))
            ->orderBy('event.startDate', 'ASC')
            ->getQuery();
    }

    /**
     * @param $limit
     * @param $offset
     */
    public function findListAdmin($limit, $offset)
    {
        return $this->createQueryBuilder('event')
            ->orderBy('event.startDate', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return integer
     */
    public function countItemsAdmin()
    {
        return $this->createQueryBuilder('event')
            ->select('count(event.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param $limit
     * @param $offset
     */
    public function findListAdminToApprove($limit, $offset)
    {
        return $this->createQueryBuilder('event')
            ->andWhere('event.status = :val')
            ->setParameter('val', Event::STATUS_PENDING)
            ->orderBy('event.startDate', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return integer
     */
    public function countItemsAdminToApprove()
    {
        return $this->createQueryBuilder('event')
            ->select('count(event.id)')
            ->andWhere('event.status = :val')
            ->setParameter('val', Event::STATUS_PENDING)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param $limit
     * @param $offset
     * @param $params
     */
    public function findListByCriterias($params)
    {
        $qb = $this->setCriterias($params);

        return $qb
            ->orderBy('event.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return integer
     */
    public function countItemsByCriterias($params)
    {
        $qb = $this->setCriterias($params);

        return $qb
            ->select('count(event.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function setCriterias($params)
    {
        $qb = $this->createQueryBuilder('event')
            ->innerJoin('event.subgenre', 'subgenre')
            ->innerJoin('subgenre.genre', 'genre')
            ->leftJoin('event.weapons', 'weapon');

        if (count($params['type'])>0) {
            $entityList = [];
            foreach ($params['type'] as $entity) {
                $entityList[] = $entity->getId();
            }
            $qb->andWhere($qb->expr()->in('event.type', $entityList));
        }

        if (count($params['region'])>0) {
            $entityList = [];
            foreach ($params['region'] as $entity) {
                $entityList[] = $entity->getId();
            }
            $qb->andWhere($qb->expr()->in('event.region', $entityList));
        }

        if (count($params['genre'])>0) {
            $entityList = [];
            foreach ($params['genre'] as $entity) {
                $entityList[] = $entity->getId();
            }
            dump($entityList);
            $qb->andWhere($qb->expr()->in('genre', $entityList));
        }

        if (count($params['subgenre'])>0) {
            $entityList = [];
            foreach ($params['subgenre'] as $entity) {
                $entityList[] = $entity->getId();
            }
            $qb->andWhere($qb->expr()->in('event.subgenre', $entityList));
        }

        if (count($params['settlement'])>0) {
            $entityList = [];
            foreach ($params['settlement'] as $entity) {
                $entityList[] = $entity->getId();
            }
            $qb->andWhere($qb->expr()->in('event.settlement', $entityList));
        }

        if (count($params['weapons'])>0) {
            $entityList = [];
            foreach ($params['weapons'] as $entity) {
                $entityList[] = $entity->getId();
            }
            $qb->andWhere($qb->expr()->in('weapon', $entityList));
        }

        if ($params['priceMax']) {
            $qb->andWhere($qb->expr()->lte('event.priceMin', $params['priceMax']));
        }

        if ($params['keywords']) {
            $words = explode(" ", $params['keywords']);

            $conditions = [];
            foreach ($words as $word) {
                $conditions[] = $qb->expr()->like('event.name', $qb->expr()->literal('%' . $word . '%'));
                $conditions[] = $qb->expr()->like('event.description', $qb->expr()->literal('%' . $word . '%'));
            }
            $qb->andWhere(
                call_user_func_array(array($qb->expr(), 'orX'), $conditions)
            );
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

    /**
     * @param $limit
     * @param $offset
     * @param $params
     */
    public function findListByDynamicCriterias($params)
    {
        $qb = $this->setDynamicCriterias($params);

        return $qb
            ->orderBy('event.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function setDynamicCriterias($params)
    {
        $qb = $this->createQueryBuilder('event')
            ->innerJoin('event.subgenre', 'subgenre')
            ->innerJoin('subgenre.genre', 'genre')
            ->leftJoin('event.weapons', 'weapon');

        if (count($params['type'])>0) {
            $entityList = [];
            foreach ($params['type'] as $entity) {
                $entityList[] = $entity;
            }
            $qb->andWhere($qb->expr()->in('event.type', $entityList));
        }


        if (count($params['genre'])>0) {
            $entityList = [];
            foreach ($params['genre'] as $entity) {
                $entityList[] = $entity;
            }
            $qb->andWhere($qb->expr()->in('genre', $entityList));
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

    public function getMinDate($params)
    {
        $qb = $this->setDynamicCriteriaDate($params);

        return $qb
            ->select('min(event.endDate)')
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function getMaxDate($params)
    {
        $qb = $this->setDynamicCriteriaDate($params);

        return $qb
            ->select('max(event.endDate)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function setDynamicCriteriaDate($params)
    {
        $qb = $this->createQueryBuilder('event')
            ->innerJoin('event.subgenre', 'subgenre')
            ->innerJoin('subgenre.genre', 'genre');

        if (count($params['type'])>0) {
            $entityList = [];
            foreach ($params['type'] as $entity) {
                $entityList[] = $entity;
            }
            $qb->andWhere($qb->expr()->in('event.type', $entityList));
        }


        if (count($params['genre'])>0) {
            $entityList = [];
            foreach ($params['genre'] as $entity) {
                $entityList[] = $entity;
            }
            $qb->andWhere($qb->expr()->in('genre', $entityList));
        }

        if (count($params['subgenre'])>0) {
            $entityList = [];
            foreach ($params['subgenre'] as $entity) {
                $entityList[] = $entity;
            }
            $qb->andWhere($qb->expr()->in('event.subgenre', $entityList));
        }

        $qb

            ->andWhere('event.status = :status')
            ->setParameter('status', Event::STATUS_APPROVED);

        return $qb;
    }
}
