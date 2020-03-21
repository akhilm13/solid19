<?php


namespace App\Service;


use App\Entity\ListRequirements;
use App\Entity\Lists;
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

    public function getAllItemsInList($listId)
    {
        $listItems = array();
        $list = $this->listsRepository->find($listId);

        if (!$list){
            return $listItems;
        }

        $listItems = $this->listRequirementsRepository->findBy(array('listId' => $list));
        $listItems = $this->formatListItems($listItems);

        return $listItems;
    }

    public function getItemAndCheckToken($token, $itemId)
    {
        $item = $this->listRequirementsRepository->find($itemId);

        if (!$item) {
            return null;
        }

        if ($item->getShopperId() !== $token) {
            return false;
        }

        return $item;
    }

    /**
     * @param $listItemId
     * @param string $status
     * @param null $shopperId
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updateListItemStatus($listItemId, string $status, $shopperId = null)
    {
        $listItem = $this->listRequirementsRepository->find($listItemId);

        if ($status === 'required') {
            $listItem->setStatus(false);
            $listItem->setShopperId(null);
        } else {
            $listItem->setStatus(true);
            $listItem->setShopperId($shopperId);
        }

        $this->listRequirementsRepository->saveListItem($listItem);


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

    private function formatListItems($listItems)
    {
        $formattedListItems = array();
        /** @var ListRequirements $listItem */
        foreach ($listItems as $listItem){
            $formattedListItems[] = $listItem->toArray();
        }

        return $formattedListItems;
    }

    /**
     * @param $volunteerId
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createNewList($volunteerId)
    {
        $list = new Lists();
        $list->setVolunteerId($volunteerId);
        $this->listsRepository->saveList($list);
    }

    public function createNewListItem($listId, $quantity, $listItem)
    {
        $listItem = new ListRequirements();
        $listItem->setListId($listId);
        $listItem->setStatus(false);
        $listItem->setQuantity($quantity);
        $listItem->setListItem($listItem);

        $this->listRequirementsRepository->saveListItem($listItem);
    }

    public function getAllListsByVolunteerId($volunteerId)
    {
        $listsArray = $this->listsRepository->getListsByVolunteers(array($volunteerId));
        $listItems = $this->listRequirementsRepository->getAllListItemsInList($listsArray);
        return $listItems;

    }


}