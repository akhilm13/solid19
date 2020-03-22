<?php


namespace App\Service;


use App\Repository\VolunteerEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class VolunteerService
{

    private $volunteerRepository;

    public function __construct(VolunteerEntityRepository $volunteerRepository)
    {
        $this->volunteerRepository = $volunteerRepository;
    }

    public function authenticateVolunteer($email, $password){

        $isAuth = $this->volunteerRepository->checkPassword($email, $password);

        if ($isAuth !== false){
            return $isAuth;
        }

        return false;
    }

    public function checkToken($token)
    {
        return $this->volunteerRepository->checkToken($token);
    }

    public function getParameters($volunteerId)
    {
        $parameters = $this->volunteerRepository->getParameters($volunteerId);

        return $parameters;
    }

    /**
     * @param $id
     * @param $contact
     * @param $messageToShoppers
     * @param $messageToVolunteers
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveParameters($id, $contact, $messageToShoppers, $messageToVolunteers)
    {
        $volunteer = $this->volunteerRepository->find($id);

        if (!$volunteer){
            return false;
        }

        $volunteer->setPhone($contact);
        $volunteer->setMessageToOtherVolunteers($messageToVolunteers);
        $volunteer->setMessageToShoppers($messageToShoppers);

        $this->volunteerRepository->saveVolunteerEntity($volunteer);
        return true;
    }

    public function getMessageToVolunteers($id)
    {
        $volunteer = $this->volunteerRepository->find($id);
        if (!$volunteer){
            return null;
        }

        return $volunteer->getMessageToOtherVolunteers();
    }

    public function getMessageToShoppers($id)
    {
        $volunteer = $this->volunteerRepository->find($id);
        if (!$volunteer){
            return false;
        }

        return $volunteer->getMessageToShoppers();
    }
}