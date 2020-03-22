<?php

namespace App\Repository;

use App\Entity\VolunteerEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;

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
        $volunteer->setRoadNumber(intval($roadNumber));
        $volunteer->setRoadName($roadName);
        $volunteer->setZip($zip);

        $this->_em->persist($volunteer);
        $this->_em->flush();

    }

    /**
     * @param VolunteerEntity $volunteerEntity
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveVolunteerEntity(VolunteerEntity $volunteerEntity){

        $this->_em->persist($volunteerEntity);
        $this->_em->flush();

    }


    /**
     * @param $latitude
     * @param $longitude
     * @return mixed[]
     * @throws DBALException
     */
    public function findNearestVolunteers($latitude, $longitude)
    {
        $conn = $this->_em->getConnection();
        $sql = 'CALL getAllVolunteersWithinLocation(:lat, :lon)';

        $statement = $conn->prepare($sql);
        $statement->execute(array(':lat' => $latitude, ':lon' => $longitude));

        $volunteerIds = $statement->fetchAll();
        return $volunteerIds;
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
    public function checkPassword($email, $password)
    {
        $volunteer = $this->findOneBy(array('email' => $email));

        if ($volunteer){
            return $volunteer->getId();
        }

        return false;
    }

    public function checkToken($token)
    {
        $isFound = $this->findOneBy(array('token' => $token));
        if ($isFound){
            return true;
        }

        return false;
    }

    public function getParameters($volunteerId)
    {
        $volunteer = $this->find($volunteerId);

        if (!$volunteer){
            return array();
        }
        $contact = $volunteer->getPhone();
        $messageToShoppers = $volunteer->getMessageToShoppers();
        $messageToVolunteers = $volunteer->getMessageToOtherVolunteers();

        $parameters = array(
            'contact' => $contact,
            'messageToShoppers' => $messageToShoppers,
            'messageToVolunteers' => $messageToVolunteers
        );

        return $parameters;
    }
}
