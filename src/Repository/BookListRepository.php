<?php

namespace App\Repository;

use App\Entity\BookList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BookList|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookList|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookList[]    findAll()
 * @method BookList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookList::class);
    }

    /**
     * This Method allow us to find public booklists based on the number of booklists
     *
     * @param integer $nbBooklists
     * @return array
     */
    public function findPublicBooklist(?int $nbBooklists): array
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->where('b.isPublic = 1')
            ->orderBy('b.createdAt', 'DESC');

        if($nbBooklists !== 0 || $nbBooklists !== null) {
            $queryBuilder->setMaxResults($nbBooklists);
        }

        return $queryBuilder->getQuery()
            ->getResult();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(BookList $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(BookList $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return BookList[] Returns an array of BookList objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BookList
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
