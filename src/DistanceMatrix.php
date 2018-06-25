<?php
/**
 * Created by PhpStorm.
 * User: kacper
 * Date: 6/21/18
 * Time: 4:18 PM
 */

namespace B4B\Geotest;

use Google_Client;

class DistanceMatrix
{
    public function findNearest($originLat, $originLon, $agents)
    {
        // create the Google client
        $client = new Google_Client();

        /**
         * Set your method for authentication. Depending on the API, This could be
         * directly with an access token, API key, or (recommended) using
         * Application Default Credentials.
         */
        //$client->useApplicationDefaultCredentials();
        $client->setApplicationName("Client_Library_Examples");
        $client->setDeveloperKey(Config::GOOGLE_API_KEY);
        //$client->addScope(Google_Service_Plus::PLUS_ME);

        // returns a Guzzle HTTP Client
        $httpClient = $client->authorize();

        // make an HTTP request
        //$response = $httpClient->get('https://maps.googleapis.com/maps/api/directions/json?origin=' . $originLat . ' ' . $originLon . '&destination=' . $destLat . ' ' . $destLon . '&departure_time=' . strtotime('tomorrow midnight'));

        $destString = '';
        foreach ($agents as $agent) {
            $destString .= $agent['lat'] . ' ' . $agent['lon'] . '|';
        }
        $destString = substr($destString, 0, -1);

        $response = $httpClient->get('https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $originLat . ' ' . $originLon . '&destinations=' . $destString . '&mode=driving&units=metric');

        return $response;
    }

}
