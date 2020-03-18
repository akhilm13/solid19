<?php

namespace App\Controller;

use App\Repository\ListRequirementsRepository;
use App\Repository\VolunteerEntityRepository;
use App\Service\GeocodingService;
use App\Service\ListsService;
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
     *
     * @Route("/getOrders/street/{street}/city/{city}/country/{country}/postal/{postal}", name="getOrders", methods={"GET"})
     * @param GeocodingService $geocodingService
     * @param ListsService $listsService
     * @param $street
     * @param $city
     * @param $country
     * @param $postal
     * @return JsonResponse
     * @throws DBALException
     */
    public function getOrders(GeocodingService $geocodingService, ListsService $listsService, $street, $city, $country, $postal)
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

        $nearestVolunteersList = $this->volunteerRepository->findNearestVolunteers($latitude,$longitude);
        $listArray = $listsService->getAllListsByVolunteers($nearestVolunteersList);

        if (empty($listArray)){
            return  new JsonResponse(array(
                'status' => 'No results found'
            ), Response::HTTP_NOT_FOUND);
        }
        return  new JsonResponse($listArray, Response::HTTP_OK);

    }

    /**
     * @Route("/updateStatus/{listItemId}", name="updateStatus", methods={"DELETE", "POST"})
     * @param Request $request
     * @param $listItemId
     * @param ListsService $listsService
     * @return JsonResponse
     */
    public function updateStatusOfListItem(Request $request, $listItemId, ListsService $listsService)
    {
        if ($request->getMethod() == "POST") {
            $listsService->updateListItemStatus($listItemId, 'required');
        } else {
            $listsService->updateListItemStatus($listItemId, 'serviced');
        }

        return new JsonResponse(array(
            'status' => 'updated'
        ), Response::HTTP_OK);
    }

    /**
     * @Route("/removeItem/{listItemId}")
     * @param $listItemId
     * @param ListsService $listsService
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteListItem($listItemId, ListsService $listsService)
    {
        $listsService->deleteItem($listItemId);
        return new JsonResponse(array(
            'status' => 'deleted'
        ), Response::HTTP_OK);
    }
}
