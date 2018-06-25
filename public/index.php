<?php
/**
 * Created by PhpStorm.
 * User: kacper
 * Date: 6/21/18
 * Time: 2:51 PM
 */

require_once '../vendor/autoload.php';

use B4B\Geotest\Geocoder;

$search = empty($argv[1]) ? '' : $argv[1];

$geocoder = new Geocoder();
$geocoderResults = $geocoder->find($search);

$distanceMatrix = new \B4B\Geotest\DistanceMatrix();

$agent = new \B4B\Geotest\Agent();

$distance = 20;

echo "\r\nAgenci znalezieni w promieniu " . $distance . " km dla " . (empty($search) ? 'adresu IP' : 'wszystkich wyników wyszukiwania frazy "' . $search . '"') . "\r\nZnaleziono: " . sizeof($geocoderResults) . " współrzędnych\r\n\r\n";

foreach ($geocoderResults as $geoResult) {
    echo "adres: \t" .
        (empty($geoResult->getPostalCode()) ? 'brak kodu' : $geoResult->getPostalCode()) . ' ' .
        (empty($geoResult->getLocality()) ? 'brak miasta' : $geoResult->getLocality()) . ', ' .
        (empty($geoResult->getStreetName()) ? 'brak nazwy ulicy' : $geoResult->getStreetName()) . "\r\n" .
        "lat: \t" . $geoResult->getCoordinates()->getLatitude() . "\r\n" .
        "lon: \t" . $geoResult->getCoordinates()->getLongitude() . "\r\n",
        "prov: \t" . $geoResult->getProvidedBy() . "\r\n";

    $nearestAgents = $agent->getNearests($geoResult->getCoordinates()->getLatitude(), $geoResult->getCoordinates()->getLongitude(), $distance);

    echo 'Agenci: ';

    if (!empty($nearestAgents)) {
        echo "\r\n";

        $distanceMatrixResult = $distanceMatrix->findNearest($geoResult->getCoordinates()->getLatitude(), $geoResult->getCoordinates()->getLongitude(), $nearestAgents);
        $json = json_decode($distanceMatrixResult->getBody()->read($distanceMatrixResult->getBody()->getSize()), true);

        foreach ($nearestAgents as $i => $nearestAgent) {
            echo "\t- " . $nearestAgent['name'] . ', ' . $json['destination_addresses'][$i] . ' - ' . $json['rows'][0]['elements'][$i]['distance']['text'] . ' (' . $json['rows'][0]['elements'][$i]['duration']['text'] . ")\r\n";
        }
    } else {
        echo "BRAK\r\n";
    }

    echo "\r\n";
}

echo "------------------------------------------------------\r\n\r\n";
