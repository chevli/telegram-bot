<?php

namespace Chevli\TelegramBot\Monica\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

abstract class AbstractApi
{
    protected $monicaUrl;
    protected $monicaToken;

    public function __construct()
    {
        $this->monicaUrl = getenv('monica_url') ?? false;
        $this->monicaToken = getenv('monica_token') ?? false;
    }

    public function getClient()
    {
        if (!$this->monicaUrl) {
            throw new \Exception("URL for monica hq not set as environment variable.");
        }

        if (!$this->monicaToken) {
            throw new \Exception("Token for monica hq not set as environment variable.");
        }

        $headers = [
            'Authorization' => 'Bearer ' . $this->monicaToken
        ];

        return new Client([
            'base_uri' => $this->monicaUrl,
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

    /**
     * @param string $content
     * @return array
     * @throws \Exception
     */
    protected function parseJson(string $content) : array
    {
        $responseArray = json_decode($content, true);
        if (json_last_error() == JSON_ERROR_NONE) {
            return $responseArray;
        }
        if (json_last_error() == JSON_ERROR_SYNTAX) {
            throw new \Exception("Unable to parse the JSON. Must have failed to authenticate");
        }
        throw new \Exception(json_last_error_msg());
    }
}
