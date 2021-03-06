<?php

namespace App\Controller;

use App\Repository\ListRequirementsRepository;
use App\Repository\VolunteerEntityRepository;
use App\Service\GeocodingService;
use App\Service\ListsService;
use App\Service\VolunteerService;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ListOrdersController extends AbstractController
{
    private $volunteerRepository;
    private $listRequirementsRepository;

    public function __construct(VolunteerEntityRepository $volunteerRepository, ListRequirementsRepository $listRequirementsRepository)
    {
        $this->volunteerRepository = $volunteerRepository;
        $this->listRequirementsRepository = $listRequirementsRepository;
    }

    /**
     * @Route("/getOrders/lat/{latitude}/lon/{longitude}", name="getOrdersWithLatLon", methods={"GET"})
     * @param ListsService $listsService
     * @param $latitude
     * @param $longitude
     * @return mixed
     */
    public function getOrdersWithLatLon(ListsService $listsService, $latitude, $longitude)
    {
        return $this->getNearestListOrders($listsService, $latitude, $longitude);
    }


    /**
     * @Route("/getOrders/street/{street}/city/{city}/country/{country}/postal/{postal}", name="getOrdersWithAddress", methods={"GET"})
     * @param GeocodingService $geocodingService
     * @param ListsService $listsService
     * @param $street
     * @param $city
     * @param $country
     * @param $postal
     * @return JsonResponse
     */
    public function getOrdersWithAddress(GeocodingService $geocodingService, ListsService $listsService, $street, $city, $country, $postal)
    {

        //fixme implement catch
        try {
            $coordinates = $geocodingService->getLatitudeLongitude($street, $city, $postal, $country);
        } catch (ClientExceptionInterface $e) {
        } catch (RedirectionExceptionInterface $e) {
        } catch (ServerExceptionInterface $e) {
        } catch (TransportExceptionInterface $e) {
        }

        if (empty($coordinates)){
            return new JsonResponse(array(
                'status' => 'Could not find coordinates'
            ), Response::HTTP_NOT_FOUND);
        }

        $latitude = $coordinates['latitude'];
        $longitude = $coordinates['longitude'];


        return $this->getNearestListOrders($listsService, $latitude, $longitude);


    }

    /**
     * @param ListsService $listsService
     * @param $latitude
     * @param $longitude
     * @return mixed
     */
    private function getNearestListOrders(ListsService $listsService, $latitude, $longitude)
    {

        try {
            $nearestVolunteersList = $this->volunteerRepository->findNearestVolunteers($latitude, $longitude);
            $listArray = $listsService->getAllListsByVolunteers($nearestVolunteersList);
        } catch (DBALException $e) {
            $listArray = array();
        }

        if (empty($listArray)) {
            return new JsonResponse(array(
                'status' => 'No results found'
            ), Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse($listArray, Response::HTTP_FOUND);

    }

    /**
     * @Route("/updateStatus/{listItemId}/token/{token}", name="updateStatus", methods={"DELETE", "POST"})
     * @param Request $request
     * @param $listItemId
     * @param ListsService $listsService
     * @param $token
     * @return JsonResponse
     */
    public function updateStatusOfListItem(Request $request, $listItemId, ListsService $listsService, $token)
    {
        //fixme implement token and set current time

        if ($request->getMethod() == "POST") {
            $item = $listsService->getItemAndCheckToken($token, $listItemId);

            if ($item === null) {
                return new JsonResponse(array(
                    'status' => 'Item not found'
                ), Response::HTTP_NOT_FOUND);
            }

            if ($item === false) {
                return new JsonResponse(array(
                    'status' => 'Token not authorised'
                ), Response::HTTP_FORBIDDEN);
            }
            try {
                $listsService->updateListItemStatus($listItemId, 'required');
            } catch (ORMException $exception) {

            }
        } else {
            try {
                $listsService->updateListItemStatus($listItemId, 'serviced', $token);
            } catch (ORMException $e) {
            }
        }

        return new JsonResponse(array(
            'status' => 'updated'
        ), Response::HTTP_OK);
    }



    /**
     * @Route("/listItem/{listItemId}", name="getItem", methods={"GET"})
     * @param $listItemId
     * @return JsonResponse
     */
    public function getItem($listItemId)
    {

        $item = $this->listRequirementsRepository->find($listItemId);

        if (!$item) {
            return new JsonResponse(array(
                'status' => 'Item not found'
            ), Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($item->toArray(), Response::HTTP_FOUND);
    }


    /**
     * @Route("/lists/volunteer/{id}", name="getListsByVolunteer", methods={"GET"})
     * @param ListsService $listsService
     * @param $id
     * @return JsonResponse
     */
/*    public function getAllListByVolunteer(ListsService $listsService, $id)
    {
        $listItems = $listsService->getAllListsByVolunteerId($id);

        if (empty($lists)){
            return new JsonResponse(array(
                'status' => 'No Lists Found'
            ), Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($listItems, Response::HTTP_FOUND);
    }*/

    /**
     * @Route("/getMessage/{id}", name="getMessageToShoppers", methods={"GET"})
     * @param VolunteerService $volunteerService
     * @param $id
     * @return JsonResponse
     */
    public function getMessageToShoppers(VolunteerService $volunteerService, $id){

        $message = $volunteerService->getMessageToShoppers($id);

        return new JsonResponse(array(
            'message' => $message
        ), Response::HTTP_FOUND);


    }

    /**
     * @Route("/getStatus/item/{listItemId}", name="getItemStatus", methods={"GET"})
     * @param ListsService $listsService
     * @param $listItemId
     * @return JsonResponse
     */
    public function getListItemStatus(ListsService $listsService, $listItemId)
    {
        $status = $listsService->getItemStatus($listItemId);

        if ($status === null){
            return new JsonResponse(array(
                'status' => "List Item not Found"
            ), Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(array(
            'status' => $status
        ), Response::HTTP_FOUND);

    }


}
