<?php


namespace App\Service;


use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GeocodingService
{
    private const KEY = 'removed';
    private $baseUrl = 'http://www.mapquestapi.com/geocoding/v1/address?';

    /**
     * @param $street
     * @param $city
     * @param $postalCode
     * @param $country
     * @return array
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getLatitudeLongitude($street, $city, $postalCode, $country)
    {

        $coordinates = array();
        $url = $this->baseUrl
            . 'key=' . self::KEY
            . '&street=' . $street
            . '&city=' . $city
            . '&postalCode=' . $postalCode
            . '&country=' . $country;

        $client = HttpClient::create();

        $response = $client->request('GET', $url);

        $statusCode = $response->getStatusCode();

        if ($statusCode != 200) {
            return $coordinates;
        }

        $content = json_decode($response->getContent(), true);

        $latitude = $content['results'][0]['locations'][0]['latLng']['lat'];
        $longitude = $content['results'][0]['locations'][0]['latLng']['lng'];

        $coordinates =  array(
            'latitude' => $latitude,
            'longitude' => $longitude
        );

        return $coordinates;

    }
}