<?php

namespace App\Repository;

use App\Entity\RegistrationFormData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RegistrationFormData>
 *
 * @method RegistrationFormData|null find($id, $lockMode = null, $lockVersion = null)
 * @method RegistrationFormData|null findOneBy(array $criteria, array $orderBy = null)
 * @method RegistrationFormData[]    findAll()
 * @method RegistrationFormData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegistrationFormDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegistrationFormData::class);
    }

//    /**
//     * @return RegistrationFormData[] Returns an array of RegistrationFormData objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RegistrationFormData
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
