<?php

namespace Chevli\TelegramBot\Monica\Service;

class Reminders extends AbstractApi
{
    public function getReminders($page = 1)
    {
        $data = $this->getPaginatedReminders();
        return $data;
    }

    private function getPaginatedReminders($page = 1)
    {
        $options = [
            'query' => [
                'limit' => 100,
                'page' => $page
            ]
        ];
        $response = $this->get("reminders", $options);
        $content = $response->getBody()->getContents();
        $responseArray = json_decode($content, true);
        $data = $responseArray['data'];
        if ($responseArray['links']['next'] != null) {
            $page++;
            $nextPageData = $this->getPaginatedReminders($page);
            $data = array_merge($data, $nextPageData);
        }
        return $data;
    }
}