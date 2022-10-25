<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Tag;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function save(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    /**
    * @return Post[] Returns an array of Post objects
    */
   public function findByTagAndName($tag,$name): array{

        $tag=$this->getEntityManager()->getRepository(Tag::class)->findOneBy(['name' => $tag]);
        // tag is a relation, so we need to use the relation name to get the tag from the query build
        if ($name != '' and $tag != null) {
            return $this->createQueryBuilder('p')
                ->andWhere('p.title = :title')
                ->setParameter('title', $name)
                ->join('p.Tag', 't')
                ->andWhere('t.id = :tag')
                ->setParameter('tag', $tag->getId())
                ->getQuery()
                ->getResult()
            ;
        }elseif ($name != '' and $tag == null) {
            return $this->createQueryBuilder('p')
                ->andWhere('p.title = :title')
                ->setParameter('title', $name)
                ->getQuery()
                ->getResult()
            ;
        }elseif ($name == '' and $tag != null) {
            return $this->createQueryBuilder('p')
                ->join('p.Tag', 't')
                ->andWhere('t.id = :tag')
                ->setParameter('tag', $tag->getId())
                ->getQuery()
                ->getResult()
            ;
        }else {
            //findAll()
            return $this->createQueryBuilder('p')
                ->getQuery()
                ->getResult()
            ;

        }
        
    }
        
}
