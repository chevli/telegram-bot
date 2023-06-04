<?php

require "vendor/autoload.php";

use SergiX44\Nutgram\Nutgram;

$telegramBotToken = getenv("telegram_token");
$telegramChatId = getenv("telegram_chat_id");

$bot = new Nutgram($telegramBotToken);
$chat = new \Chevli\TelegramBot\Telegram\Chat($bot, $telegramChatId);


$bot->onCommand('start', function(Nutgram $bot) {
    $bot->sendMessage("You may ask me...");
});

$chores = new \Chevli\TelegramBot\Grocy\Telegram\Chores($chat);
$bot->onCommand('chores', [$chores, 'getChores']);

$reminders = new \Chevli\TelegramBot\Monica\Telegram\Reminders($chat);
$bot->onCommand('reminders', [$reminders, 'getReminders']);
$bot->onCommand('birthdays', [$reminders, 'getBirthdays']);

$chat->sendMessage("I'm ready");

$bot->run();