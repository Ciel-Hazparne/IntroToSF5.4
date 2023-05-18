<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function save(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByPriceRange($minPrice, $maxPrice)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.price >= :minPrice')
            ->setParameter('minPrice', $minPrice)
            ->andWhere('a.price <= :maxPrice')
            ->setParameter('maxPrice', $maxPrice)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findArticleByName($mot_cle){
        return $this->createQueryBuilder('a')
            ->where('a.name like :name')
            ->setParameter('name', '%'.$mot_cle.'%')
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
