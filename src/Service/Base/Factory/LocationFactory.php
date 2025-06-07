<?php

namespace Smug\Core\Service\Base\Factory;

/**
 * Class LocationFactory
 * @package Smug\Core\Service\Base\Factory
 */
class LocationFactory
{
    const BACKUP_LOCATION_DATA = [
        'lat' => 51.312801,
        'lang' => 9.481544
    ];

    /**
     * @param array $data
     * @return array
     */
    public static function getLocationData(array $data): array
    {
        
        $address = str_replace('-', '', $data['address']) . ' '. $data['city'] . ' ' . $data['zipCode'];
        $address = str_replace(" ", "+", $address);
        $search_url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($address) . "&format=json";
    
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$search_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_REFERER, $search_url);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    
        $result = curl_exec($ch);
    
        curl_close($ch);
    
        $decoded = json_decode($result, true);
        $lat = $decoded[0]["lat"];
        $lng = $decoded[0]["lon"];

        if (empty($lat) || empty($lng)) {
            $address = $data['city'] . ' ' . $data['zipCode'];
            $address = str_replace(" ", "+", $address);
            $search_url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($address) . "&format=json";
    
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL,$search_url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_REFERER, $search_url);
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        
            $result = curl_exec($ch);
        
            curl_close($ch);
        
            $decoded = json_decode($result, true);
            
            $lat = $decoded[0]["lat"];
            $lng = $decoded[0]["lon"];
        }

        return [
            'lang' => $lng,
            'lat' => $lat
        ];
    }
    /**
     * @param array $data
     * @return array
     */
    public static function getFeLocationData(array $data): array
    {
        $address = $data['city'];
        $address = str_replace(" ", "+", $address);
        $search_url = "https://nominatim.openstreetmap.org/search?q=" . $address . "&format=json";

        $httpOptions = [
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: Nominatim-Test"
            ]
        ];

        $streamContext = stream_context_create($httpOptions);
        $json = file_get_contents($search_url, false, $streamContext);

        $decoded = json_decode($json, true);
        $lat = $decoded[0]["lat"];
        $lng = $decoded[0]["lon"];
        
        return [
            'lang' => $lng,
            'lat' => $lat
        ];
    }
}
