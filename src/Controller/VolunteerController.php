<?php

namespace App\Controller;

use App\Entity\VolunteerEntity;
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
     * @return JsonResponse
     */
    public function volunteerSignUpAction(Request $request){

        $volunteerData = json_decode($request->getContent(), true);

        $email = $volunteerData['email'];
        $phone = $volunteerData['phone'];
        $password = $volunteerData['password'];

        if (empty($email) || empty($phone) || empty($password)){
            throw new NotFoundHttpException('Mandatory parameters not found');
        }

        $volunteer = new VolunteerEntity();
        $volunteer->setEmail($email);
        $volunteer->setPhone($phone);
        $volunteer->setPassword(password_hash($password, PASSWORD_BCRYPT));

        try{

            $this->volunteerRepository->saveVolunteerEntity($volunteer);
        }catch (ORMException $exception){
            return new JsonResponse(array(
                'status' => 'failed'
            ), Response::HTTP_NOT_IMPLEMENTED);
        }

        return new JsonResponse(array(
            'status' => 'success'
        ), Response::HTTP_CREATED);
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

        if (empty($email) || empty($phone) || empty($roadNumber) || empty($roadName) || empty($zip)) {
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
