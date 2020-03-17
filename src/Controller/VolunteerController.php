<?php

namespace App\Controller;

use App\Repository\VolunteerEntityRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class VolunteerController
 * @package App\Controller
 * @Route("/volunteer"")
 */
class VolunteerController extends AbstractController
{
    private $volunteerRepository;

    public function __construct(VolunteerEntityRepository $volunteerEntityRepository)
    {
        $this->volunteerRepository = $volunteerEntityRepository;
    }

    /**
     * @Route("/create/new", name="createVolunteer", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function createVolunteer(Request $request)
    {

        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $phone = $data['phone'];
        $roadNumber = $data['roadNumber'];
        $roadName = $data['roadName'];
        $zip = $data['zip'];

        if (empty($email) || empty($phone) || empty($roadNumber) || empty($roadName) || empty(zip)) {
            throw new NotFoundHttpException('Missing mandatory parameters');
        }

        try {
            $this->volunteerRepository->saveNewVolunteer($email, $phone, $roadName, $roadNumber, $zip);
        } catch (ORMException $ORMException) {
            //todo Implement exception handling
        }

        return new JsonResponse(array(
            'status' => 'Volunteer Registered'
        ), Response::HTTP_CREATED);

    }
}
