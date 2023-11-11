<?php

namespace Chevli\TelegramBot\Monica\Telegram;

use Chevli\TelegramBot\Telegram\Chat;
use Chevli\TelegramBot\Monica\Service\Contacts as ContactsService;

class Contacts
{
    const CONTACT_MESSAGE_SPLIT_BLOCK = 20;

    /**
     * @var Chat
     */
    protected $bot;

    /**
     * @var ContactsService
     */
    protected $contacts;

    public function __construct(Chat $chat) {
        $this->bot = $chat;
        $this->contacts = new ContactsService();
    }

    public function getContacts()
    {
        try {
            $contacts = $this->contacts->getContacts();
        } catch (\Exception $exception) {
            $this->bot->sendMessage($exception->getMessage());
            return;
        }
        $messages = [];
        $messageIndex = 0;
        foreach ($contacts as $i => $contact) {
            $organisedContact = $this->organiseContact($contact);
            $messages[$messageIndex][] = $this->getOrganisedContactMessage($organisedContact);
            if ($i % self::CONTACT_MESSAGE_SPLIT_BLOCK == (self::CONTACT_MESSAGE_SPLIT_BLOCK - 1)) {
                $messageIndex++;
            }
        }
        foreach ($messages as $messageIndex) {
            $message = implode("\n\n", $messageIndex);
            $this->bot->sendMessage($message);
        }
    }

    private function organiseContact($contact)
    {
        $data = new \stdClass();
        $data->name = $contact['complete_name'] ?? null;
        $data->id = $contact['id'] ?? null;
        $data->gender = $contact['gender_type'] ?? null;

        return $data;
    }

    private function getOrganisedContactMessage($contact)
    {
        return sprintf(
            "<b>%s</b>",
            $contact->name
        );
    }

}
