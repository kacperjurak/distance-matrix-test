<?php
/**
 * Created by PhpStorm.
 * User: kacper
 * Date: 6/22/18
 * Time: 8:19 AM
 */

namespace B4B\Geotest;


class Agent
{
    const RADIUS = 6371;

    private $agents = [
        ['name' => 'Koło Koła', 'lat' => 52.197824, 'lon' => 18.635829], //Koło
        ['name' => 'Blisko 3city', 'lat' => 54.44293, 'lon' => 18.56273], //Sopot
        ['name' => 'Blisko 3city', 'lat' => 54.34293, 'lon' => 18.53273], //Sopot
    ];

    public function getAll()
    {
        return $this->agents;
    }

    public function getNearests($lat, $lon, $distance)
    {
        // https://coderwall.com/p/otkscg/geographic-searches-within-a-certain-distance

        // we'll want everything within, say, 10km distance
        //$distance = 100;

        // earth's radius in km = ~6371
        //$radius = 6371;

        // latitude boundaries
        $maxLat = $lat + rad2deg($distance / self::RADIUS);
        $minLat = $lat - rad2deg($distance / self::RADIUS);

        // longitude boundaries (longitude gets smaller when latitude increases)
        $maxLon = $lon + rad2deg($distance / self::RADIUS / cos(deg2rad($lat)));
        $minLon = $lon - rad2deg($distance / self::RADIUS / cos(deg2rad($lat)));

        $results = [];
        foreach ($this->getAll() as $agent) {
            if ((($minLat <= $agent['lat']) && ($agent['lat'] <= $maxLat)) && (($minLon <= $agent['lon']) && ($agent['lon'] <= $maxLon))) {
                array_push($results, $agent);
            }
        }

        // weed out all results that turn out to be too far
        foreach ($results as $i => $result) {
            $resultDistance = $this->distance($lat, $lon, $result['lat'], $result['lon']);
            if ($resultDistance > $distance) {
                unset($results[$i]);
            }
        }

        return $results;
    }

    private function distance($lat1, $lng1, $lat2, $lng2)
    {
        // convert latitude/longitude degrees for both coordinates
        // to radians: radian = degree * π / 180
        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        // calculate great-circle distance
        $distance = acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($lng1 - $lng2));

        // distance in human-readable format:
        // earth's radius in km = ~6371
        return self::RADIUS * $distance;
    }

}