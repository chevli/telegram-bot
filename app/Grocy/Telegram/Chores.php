<?php

namespace Chevli\TelegramBot\Grocy\Telegram;

use Chevli\TelegramBot\Telegram\Chat;
use Chevli\TelegramBot\Grocy\Service\Chores as ChoresService;

class Chores
{
    /**
     * @var Chat
     */
    protected $bot;

    /**
     * @var ChoresService
     */
    protected $chores;

    public function __construct(Chat $chat) {
        $this->bot = $chat;
        $this->chores = new ChoresService();
    }

    public function getChores()
    {
        try {
            $chores = $this->chores->getChores();
        } catch (\Exception $exception) {
            $this->bot->sendMessage($exception->getMessage());
            return;
        }
        $messages = [];
        $keyboard = [];
        foreach ($chores as $chore) {
            $organisedChore = $this->organiseSingleChore($chore);
            $organisedChoreMessage = $this->getOrganisedChoreMessage($organisedChore);
            $messages[] = $organisedChoreMessage;
            $keyboard[] = $this->getOrganisedChoreKeyValue($organisedChore);
        }
        $message = implode("\n\n", $messages);
        $this->bot->sendMessage($message, $keyboard);
    }

    private function organiseSingleChore($chore)
    {
        $lastTrackedTime = new \DateTime($chore['last_tracked_time']);
        $nextEstimatedExecutionTime = new \DateTime($chore['next_estimated_execution_time']);

        $lastTrackedTimeText = $lastTrackedTime->format("l, jS F Y");
        $nextEstimatedExecutionTimeText = $nextEstimatedExecutionTime->format("l, jS F Y");

        $organisedChore = new \stdClass();
        $organisedChore->chore = $chore;
        $organisedChore->lastTrackedTime = $lastTrackedTimeText;
        $organisedChore->nextTime = $nextEstimatedExecutionTimeText;

        return $organisedChore;
    }

    private function getOrganisedChoreMessage($organisedChore)
    {
        return sprintf(
            "<b>%s</b>\n\n<b>Last Completed:</b> %s\n<b>Next To do:</b> %s\n<b>By:</b> %s\n",
            $organisedChore->chore['chore_name'],
            $organisedChore->lastTrackedTime,
            $organisedChore->nextTime,
            $organisedChore->chore['next_execution_assigned_user']['display_name']
        );
    }

    private function getOrganisedChoreKeyValue($organisedChore)
    {
        return [
            $organisedChore->chore['chore_id'] => sprintf(
                "%s has done the %s",
                $organisedChore->chore['next_execution_assigned_user']['first_name'],
                $organisedChore->chore['chore_name']
            )
        ];
    }
}
