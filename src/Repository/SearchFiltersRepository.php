<?php

namespace App\Repository;

use App\Entity\SearchFilters;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SearchFilters>
 *
 * @method SearchFilters|null find($id, $lockMode = null, $lockVersion = null)
 * @method SearchFilters|null findOneBy(array $criteria, array $orderBy = null)
 * @method SearchFilters[]    findAll()
 * @method SearchFilters[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchFiltersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SearchFilters::class);
    }

//    /**
//     * @return SearchFilters[] Returns an array of SearchFilters objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SearchFilters
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
