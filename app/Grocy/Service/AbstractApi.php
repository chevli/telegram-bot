<?php

namespace Chevli\TelegramBot\Grocy\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

abstract class AbstractApi
{

    protected $grocyUrl;
    protected $grocyApiKey;

    public function __construct()
    {
        $this->grocyUrl = getenv('grocy_url') ?? false;
        $this->grocyApiKey = getenv('grocy_api_key') ?? false;
    }

    public function getClient()
    {
        if (!$this->grocyUrl) {
            throw new \Exception("URL for grocy not set as environment variable.");
        }

        if (!$this->grocyApiKey) {
            throw new \Exception("API Key for Grocy not set as environment variable.");
        }

        $headers = [
            "GROCY-API-KEY" => $this->grocyApiKey
        ];

        return new Client([
            'base_uri' => $this->grocyUrl,
            'headers' => $headers
        ]);
    }

    public function get($path, $options = [])
    {
        try {
            $response = $this->getClient()->get($path, $options);
            return $response;
        } catch (ClientException $exception) {
            throw new \Exception("HTTP Issue: " . $exception->getMessage());
        }
    }
}
