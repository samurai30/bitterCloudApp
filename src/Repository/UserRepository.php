<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findAllWithMoreThan5(){
           return $this->getFindAll()
            ->getQuery()
            ->getResult();
    }

    public function findAllWith5ExecpUser(User $user){

        return $this->getFindAll()
            ->andHaving('u != :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    private function getFindAll() : QueryBuilder{
        $qb = $this->createQueryBuilder('u');
        return $qb->select('u')
            ->innerJoin('u.posts', 'mp')
            ->groupBy('u')
            ->having('count(mp)>1');
    }
    public function findByName($query){
        return $this->createQueryBuilder('c')
            ->andWhere('c.username like :query')
            ->setParameter('query', '%'.$query.'%')
            ->getQuery()
            ->getResult();
    }
}
