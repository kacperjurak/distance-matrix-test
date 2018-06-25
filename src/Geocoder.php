<?php
/**
 * Created by PhpStorm.
 * User: kacper
 * Date: 6/21/18
 * Time: 2:42 PM
 */

namespace B4B\Geotest;

use Geocoder\Query\GeocodeQuery;

class Geocoder
{

    public function find($search = '')
    {
        $geocoder = new \Geocoder\ProviderAggregator();
        $httpClient = new \Http\Adapter\Guzzle6\Client();

        $chainIp = new \Geocoder\Provider\Chain\Chain([
            new \Geocoder\Provider\FreeGeoIp\FreeGeoIp($httpClient),
            new \Geocoder\Provider\Nominatim\Nominatim($httpClient, 'https://nominatim.openstreetmap.org')
        ]);

        $chainAddr = new \Geocoder\Provider\Chain\Chain([
            new \Geocoder\Provider\ArcGISOnline\ArcGISOnline($httpClient),
            new \Geocoder\Provider\GoogleMaps\GoogleMaps($httpClient, 'pl_PL', Config::GOOGLE_API_KEY),
            new \Geocoder\Provider\Yandex\Yandex($httpClient)
        ]);

        if (empty($search)) {
            $geocoder->registerProvider($chainIp);
            $search = "213.192.71.226";
        } else {
            $geocoder->registerProvider($chainAddr);
        }

        //$geocoder = new \Geocoder\StatefulGeocoder($provider, 'pl');

        try {
            $result = $geocoder->geocodeQuery(GeocodeQuery::create($search));
        } catch (\Geocoder\Exception\CollectionIsEmpty $e) {
            return [];
        }

        return $result->all();
    }
}
