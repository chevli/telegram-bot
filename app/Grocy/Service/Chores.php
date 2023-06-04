<?php

namespace Chevli\TelegramBot\Grocy\Service;

class Chores extends AbstractApi
{
    public function getChores()
    {
        $response = $this->get('chores');
        $content = $response->getBody()->getContents();
        return json_decode($content, true);
    }
}
