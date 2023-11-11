<?php

namespace Chevli\TelegramBot\Monica\Telegram;

use Chevli\TelegramBot\Telegram\Chat;
use Chevli\TelegramBot\Monica\Service\Reminders as RemindersService;

class Reminders
{
    /**
     * @var Chat
     */
    protected $bot;

    /**
     * @var RemindersService
     */
    protected $reminders;

    public function __construct(Chat $chat) {
        $this->bot = $chat;
        $this->reminders = new RemindersService();
    }

    public function getReminders()
    {
        try {
            $reminders = $this->reminders->getReminders();
        } catch (\Exception $exception) {
            $this->bot->sendMessage($exception->getMessage());
            return;
        }
        $messages = [];
        foreach ($reminders as $reminder) {
            $organisedReminder = $this->organiseReminder($reminder);
            $messages[] = $this->getOrganisedReminderMessage($organisedReminder);
        }
        $message = implode("\n\n", $messages);
        $this->bot->sendMessage($message);
    }

    public function getBirthdays()
    {
        try {
            $reminders = $this->reminders->getReminders();
        } catch (\Exception $exception) {
            $this->bot->sendMessage($exception->getMessage());
            return;
        }

        $belatedBirthdays = [];
        $upcomingBirthdays = [];
        $todaysBirthdays = [];
        foreach ($reminders as $reminder) {
            $organisedReminder = $this->organiseReminder($reminder);
            if ($organisedReminder->birthdayIsToday) {
                $todaysBirthdays[] = $this->getOrganisedReminderMessage($organisedReminder);
            }
            if ($organisedReminder->birthdayUpcoming) {
                $upcomingBirthdays[] = $this->getOrganisedReminderMessage($organisedReminder);
            }
            if ($organisedReminder->birthdayBelated) {
                $belatedBirthdays[] = $this->getOrganisedReminderMessage($organisedReminder);
            }
        }

        if (count($todaysBirthdays)) {
            array_unshift($todaysBirthdays, "----- Today's Birthdays -----");
        }

        if (count($upcomingBirthdays)) {
            array_unshift($upcomingBirthdays, "\n----- Upcoming Birthdays -----");
        }

        if (count($belatedBirthdays)) {
            array_unshift($belatedBirthdays, "\n----- Belated Birthdays -----");
        }

        $messages = array_merge($todaysBirthdays, $upcomingBirthdays, $belatedBirthdays);

        if (!count($messages)) {

            return;
        }

        $message = implode("\n\n", $messages);
        $this->bot->sendMessage($message);
    }

    private function organiseReminder($reminder)
    {
        $data = new \stdClass();
        $data->name = $reminder['contact']['complete_name'] ?? null;

        // Birthdate & Age
        $currentDate = new \DateTime();
        $birthdate = $reminder['contact']['information']['birthdate']['date'];
        $yearUnknown = $reminder['contact']['information']['birthdate']['is_year_unknown'];
        $birthday = new \DateTime($birthdate);
        $data->birthDate = $birthday->format('jS F');
        $data->age = false;
        $data->newAge = false;
        $age = $currentDate->diff($birthday);
        $data->index = 0;

        // Working out the age
        if (!$yearUnknown) {
            $data->birthDate = $birthday->format('jS F Y');
            $years = $age->y;
            $data->age = $years ?? null;
        }

        // Is today
        $data->birthdayIsToday = false;
        if ($age->m == 0 && $age->d == 0)
        {
            $data->birthdayIsToday = true;
        }

        // Is birthday upcoming
        $data->birthdayUpcoming = false;
        if ($age->m >= 11 && $age->d >= 1) {
            $data->birthdayUpcoming = true;
            $data->index = $age->d;

            // Show what age the person is becoming.
            if ($data->age) {
                $data->newAge = $age->y + 1;
            }
        }

        // Is belated
        $data->birthdayBelated = false;
        if ($age->m == 0 && $age->d < 4 && $age->d != 0) {
            $data->birthdayBelated = true;
            $data->index = $age->d;
        }

        return $data;
    }

    private function getOrganisedReminderMessage($organisedReminder)
    {
        $ageText = "Age Unknown";
        if ($organisedReminder->age) {
            $ageText = $organisedReminder->age;
        }
        if ($organisedReminder->newAge) {
            $ageText .= " -> " . $organisedReminder->newAge;
        }

        return sprintf(
            "<b>%s</b>\n<b>Birthday:</b> %s (%s)",
            $organisedReminder->name,
            $organisedReminder->birthDate,
            $ageText
        );
    }
}
