<?php

namespace Chevli\TelegramBot\Monica\Service;

class Contacts extends AbstractApi
{
    public function getContacts($params = [])
    {
        $data = $this->getPaginatedContacts($params);
        return $data;
    }

    private function getPaginatedContacts($params = [], $page = 1)
    {
        $options = [
            'query' => [
                'limit' => 100,
                'page' => $page
            ]
        ];
        foreach ($params as $key => $param) {
            $options['query'][$key] = $param;
        }
        $response = $this->get("contacts", $options);
        $content = $response->getBody()->getContents();
        $responseArray = json_decode($content, true);
        $data = $responseArray['data'];
        if ($responseArray['links']['next'] != null) {
            $page++;
            $nextPageData = $this->getPaginatedContacts($params, $page);
            $data = array_merge($data, $nextPageData);
        }
        return $data;
    }
}