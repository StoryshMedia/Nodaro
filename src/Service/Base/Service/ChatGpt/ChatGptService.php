<?php

namespace Smug\Core\Service\Base\Service\ChatGpt;

class ChatGptService
{
    const API_KEY = '';

    const textURL = "https://api.openai.com/v1/completions";
    const imageURL =  "https://api.openai.com/v1/images/generations";


    private static function initialize($requestType = "text" || "image")
    {
        $curl = curl_init();

        if ($requestType === 'image')
            curl_setopt($curl, CURLOPT_URL, self::imageURL);
        if ($requestType === 'text')
            curl_setopt($curl, CURLOPT_URL, self::textURL);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);

        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer " . self::API_KEY
        );

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        return $curl;
    }

    // returns text
    public static function createTextRequest($prompt, $model = 'gpt-3.5-turbo-instruct', $temperature = 0.5, $maxTokens = 1000)
    {
        $curl = self::initialize('text');

        $data = [];

        $data["model"] = $model;
        $data["prompt"] = $prompt;
        $data["temperature"] = $temperature;
        $data["max_tokens"] = $maxTokens;

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($curl);
        $response = json_decode($response, true);
        
        return $response['choices'][0]['text'] ?? -1; // return text or -1 if error
    }
}
