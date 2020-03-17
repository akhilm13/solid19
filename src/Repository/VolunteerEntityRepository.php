<?php

namespace App\Repository;

use App\Entity\VolunteerEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * @method VolunteerEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method VolunteerEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method VolunteerEntity[]    findAll()
 * @method VolunteerEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VolunteerEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VolunteerEntity::class);
    }

    /**
     * @param $email
     * @param $phone
     * @param $roadNumber
     * @param $roadName
     * @param $zip
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveNewVolunteer($email, $phone, $roadNumber, $roadName, $zip){

        $volunteer = new VolunteerEntity();
        $volunteer->setEmail($email);
        $volunteer->setPhone($phone);
        $volunteer->setRoadNumber($roadNumber);
        $volunteer->setRoadName($roadName);
        $volunteer->setZip($zip);

        $this->_em->persist($volunteer);
        $this->_em->flush();

    }

    // /**
    //  * @return VolunteerEntity[] Returns an array of VolunteerEntity objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VolunteerEntity
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
