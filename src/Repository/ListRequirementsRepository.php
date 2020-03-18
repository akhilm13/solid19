<?php

namespace App\Repository;

use App\Entity\ListRequirements;
use App\Entity\Lists;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * @method ListRequirements|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListRequirements|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListRequirements[]    findAll()
 * @method ListRequirements[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListRequirementsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListRequirements::class);
    }

    // /**
    //  * @return ListRequirements[] Returns an array of ListRequirements objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ListRequirements
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getAllListItemsInList($listsArray)
    {
        $arrayIDs = array();
        /** @var Lists $list */
        foreach ($listsArray as $list) {
            $arrayIDs[] = $list->getId();
        }
        $query = $this->createQueryBuilder('lr')
            ->leftJoin('lr.listId', 'l')
            ->addSelect('l')
            ->leftJoin('l.volunteerId', 'v')
            ->addSelect('v')
            ->andWhere('lr.listId IN (:listsArray)')
            ->setParameter(':listsArray', $arrayIDs)
            ->getQuery();

        $results = $query->getResult();


        return array_reduce($results,
            function ($list, $result) {
                $list[$result->getListId()->getId()]['orders'][] = array('product' => $result->getListItem(), 'quantity' => $result->getQuantity());
                $list[$result->getListId()->getId()]['volunteer'] = $result->getListId()->getVolunteerId()->getPhone();
                return $list;
            }, []
        );


    }

    /**
     * @param $listItemId
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteItem($listItemId)
    {
        $listItem = $this->find($listItemId);
        $this->_em->remove($listItem);
        $this->_em->flush();
    }
}
