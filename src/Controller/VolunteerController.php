<?php

namespace App\Controller;

use App\Entity\VolunteerEntity;
use App\Repository\VolunteerEntityRepository;
use App\Service\ListsService;
use App\Service\VolunteerService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class VolunteerController
 * @package App\Controller
 * @Route("/volunteer")
 */
class VolunteerController extends AbstractController
{
    private $volunteerRepository;

    public function __construct(VolunteerEntityRepository $volunteerEntityRepository)
    {
        $this->volunteerRepository = $volunteerEntityRepository;
    }

    /**
     * @Route("/signup", name="volunteerSignUp", methods={"POST"})
     * @param Request $request
     * @param VolunteerService $volunteerService
     * @return JsonResponse
     */
    public function volunteerSignUpAction(Request $request, VolunteerService $volunteerService){

        $volunteerData = json_decode($request->getContent(), true);

        $email = $volunteerData['email'];
        $phone = $volunteerData['phone'];
        $password = $volunteerData['password'];

        if (empty($email) || empty($phone) || empty($password)){
            throw new NotFoundHttpException('Mandatory parameters not found');
        }

        try{
            $volunteerService->addNewVolunteeer($email, $password, $phone);
        }catch (Exception $exception){
            return new JsonResponse(array(
                'status' => $exception->getMessage()
            ), Response::HTTP_NOT_IMPLEMENTED);
        }

        return new JsonResponse(array(
            'status' => 'success'
        ), Response::HTTP_CREATED);
    }

    /**
     * @Route("/signin", name="signIn", methods={"POST"})
     * @param Request $request
     * @param VolunteerService $volunteerService
     * @return JsonResponse
     */
    public function signIn(Request $request, VolunteerService $volunteerService)
    {
        $creds = json_decode($request->getContent(), true);

        $email = $creds['email'];
        $password = $creds['password'];

        $token = $volunteerService->authenticateVolunteer($email, $password);

        if ($token !== false) {
            return new JsonResponse(array(
                'token' => $token
            ), Response::HTTP_OK);
        }

        return new JsonResponse(array(
                'status' => 'Not authorised')
            , Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/me/lists/id/{id}", name="getAllListsByVolunteer", methods={"GET"})
     * @param Request $request
     * @param VolunteerService $volunteerService
     * @param ListsService $listsService
     * @param $id
     * @return JsonResponse
     */
    public function getAllLists(Request $request, VolunteerService $volunteerService, ListsService $listsService, $id)
    {

        $isAuth = $volunteerService->checkToken($request);

        if (!$isAuth) {
            return new JsonResponse(array(
                'status' => 'Not Authorised'
            ), Response::HTTP_UNAUTHORIZED);
        }

        $listItems = $listsService->getAllListsByVolunteers(array($id));

        return new JsonResponse($listItems, Response::HTTP_FOUND);
    }

    /**
     * @Route("/list/addNew", name="addList", methods={"POST"})
     * @param Request $request
     * @param ListsService $listsService
     * @param VolunteerService $volunteerService
     * @return JsonResponse
     */
    public function addList(Request $request, ListsService $listsService, VolunteerService $volunteerService)
    {

        $isAuth = $volunteerService->checkToken($request);

        if (!$isAuth) {
            return new JsonResponse(array(
                'status' => 'Not Authorised'
            ), Response::HTTP_UNAUTHORIZED);
        }

        $listInfo = json_decode($request->getContent(), true);
        $volunteerId = $listInfo['id'];
        $listName = $listInfo['listName'];
        try {
            $listsService->createNewList($volunteerId, $listName);
        } catch (Exception $e) {
            return new JsonResponse(array(
                'status' => $e->getMessage()
            ), Response::HTTP_NOT_IMPLEMENTED);
        }

        return new JsonResponse(array(
            'status' => 'List Created'
        ), Response::HTTP_OK);
    }


    /**
     * @Route("/listItem/addNew", name="addListItem", methods={"POST"})
     * @param Request $request
     * @param ListsService $listsService
     * @param VolunteerService $volunteerService
     * @return JsonResponse
     */
    public function addListItem(Request $request, ListsService $listsService, VolunteerService $volunteerService)
    {

        $isAuth = $volunteerService->checkToken($request);

        if (!$isAuth) {
            return new JsonResponse(array(
                'status' => 'Not Authorised'
            ), Response::HTTP_UNAUTHORIZED);
        }

        $listContent = json_decode($request->getContent(), true);

        $listItemName = $listContent['listItem'];
        $listItemListId = $listContent['listId'];
        $listItemQuantity = $listContent['quantity'];

        try {
            $listsService->createNewListItem($listItemListId, $listItemQuantity, $listItemName);
        } catch (Exception $e) {
            return new JsonResponse(array(
                'status' => $e->getMessage()
            ), Response::HTTP_NOT_IMPLEMENTED);
        }

        return new JsonResponse(array(
            'status' => 'List Item Created'
        ), Response::HTTP_OK);

    }

    /**
     * @Route("/list/{listId}", name="getList", methods={"GET"})
     * @param Request $request
     * @param ListsService $listsService
     * @param VolunteerService $volunteerService
     * @param $listId
     * @return JsonResponse
     */
    public function getList(Request $request, ListsService $listsService, VolunteerService $volunteerService, $listId)
    {
        $listItems = $listsService->getAllItemsInList($listId);

        $isAuth = $volunteerService->checkToken($request);

        if (!$isAuth) {
            return new JsonResponse(array(
                'status' => 'Not Authorised'
            ), Response::HTTP_UNAUTHORIZED);
        }

        if (!$listItems) {
            return new JsonResponse(array(
                'status' => 'List not found'
            ), Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($listItems, Response::HTTP_FOUND);
    }

    /**
     * @Route("/params/{id}", name="getVolunteerParams", methods={"GET"})
     * @param Request $request
     * @param VolunteerService $volunteerService
     * @param $id
     * @param $token
     * @return JsonResponse
     */
    public function getVolunteerParams(Request $request, VolunteerService $volunteerService, $id)
    {
        $isAuth = $volunteerService->checkToken($request);

        if (!$isAuth) {
            return new JsonResponse(array(
                'status' => 'Not Authorised'
            ), Response::HTTP_UNAUTHORIZED);
        }
        $params = $volunteerService->getParameters($id);
        if (empty($params)) {

            return new JsonResponse(array(
                'status' => 'No volunteer found'
            ), Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($params, Response::HTTP_FOUND);
    }

    /**
     * @Route("/params/{id}/}", name="addParameters", methods={"POST"})
     * @param Request $request
     * @param VolunteerService $volunteerService
     * @param $id
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function addVolunteerParameters(Request $request, VolunteerService $volunteerService, $id)
    {

        $parameters = json_decode($request->getContent(), true);

        $isAuth = $volunteerService->checkToken($request);
        if (!$isAuth) {
            return new JsonResponse(array(
                'status' => 'Not Authorised'
            ), Response::HTTP_UNAUTHORIZED);
        }

        $contact = $parameters['contact'];
        $messageToShoppers = $parameters['messageToShoppers'];
        $messageToVolunteers = $parameters['messageToVolunteers'];

        $save = $volunteerService->saveParameters($id, $contact, $messageToShoppers, $messageToVolunteers);

        if (!$save) {
            return new JsonResponse(array(
                'status' => 'Volunteer not found'
            ), Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(array(
            'status' => 'Parameters saved'
        ), Response::HTTP_OK);

    }

    /**
     * @Route("/getMessage/{id}", name="getMessageToVolunteers", methods={"GET"})
     * @param VolunteerService $volunteerService
     * @param $id
     * @return JsonResponse
     */
    public function getMessageToVolunteers(VolunteerService $volunteerService, $id)
    {

        $message = $volunteerService->getMessageToVolunteers($id);

        if (!$message) {
            $message = "";
        }
        return new JsonResponse(array(
            'message' => $message
        ), Response ::HTTP_FOUND);

    }

    /**
     * @Route("/removeItem/{listItemId}", name="deleteListItem", methods={"DELETE"})
     * @param Request $request
     * @param $listItemId
     * @param VolunteerService $volunteerService
     * @param ListsService $listsService
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteListItem(Request $request, $listItemId, VolunteerService $volunteerService, ListsService $listsService)
    {
        $isAuth = $volunteerService->checkToken($request);

        if (!$isAuth) {
            return new JsonResponse(array(
                'status' => 'Not Authorised'
            ), Response::HTTP_UNAUTHORIZED);
        }

        $listsService->deleteItem($listItemId);
        return new JsonResponse(array(
            'status' => 'deleted'
        ), Response::HTTP_OK);
    }

}
