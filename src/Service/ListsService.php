<?php


namespace App\Service;


use App\Repository\ListRequirementsRepository;
use App\Repository\ListsRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class ListsService
{
    private $listRequirementsRepository;
    private $listsRepository;

    public function __construct(ListsRepository $listsRepository, ListRequirementsRepository $listRequirementsRepository)
    {
        $this->listRequirementsRepository = $listRequirementsRepository;
        $this->listsRepository = $listsRepository;
    }


    public function getAllListsByVolunteers($volunteers)
    {

        $listsArray = $this->listsRepository->getListsByVolunteers($volunteers);

        $listItems = $this->listRequirementsRepository->getAllListItemsInList($listsArray);

        return $listItems;
    }

    private function createJSONResponse($listItems)
    {
        //fixme implement this
        $jsonArray = array();
        return $jsonArray;
    }

    public function updateListItemStatus($listItemId, string $status)
    {
        $listItem = $this->listRequirementsRepository->find($listItemId);

        if ($status === 'required') {
            $listItem->setStatus(false);
        } else {
            $listItem->setStatus(true);
        }
    }

    /**
     * @param $listItemId
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteItem($listItemId)
    {
        $lisItem = $this->listRequirementsRepository->deleteItem($listItemId);
    }
}