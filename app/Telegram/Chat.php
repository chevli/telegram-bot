<?php

namespace Chevli\TelegramBot\Telegram;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Attributes\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class Chat
{
    const MAX_LENGTH = 4096;

    protected $nutgram;
    protected $chatId;

    public function __construct(Nutgram $nutgram, $chatId)
    {
        $this->nutgram = $nutgram;
        $this->chatId = $chatId;
    }

    public function getBot()
    {
        return $this->nutgram;
    }

    public function sendMessage($message, $keyboard = [])
    {
        $replyMarkup = InlineKeyboardMarkup::make();
        foreach ($keyboard as $row) {
            foreach ($row as $i => $item) {
                $button = InlineKeyboardButton::make($item, callback_data: 'type: ' . $i);
            }
            $replyMarkup->addRow($button);
        }

        $options = [];
        $options['chat_id'] = $this->chatId;
        $options['parse_mode'] = ParseMode::HTML;

        if (!empty($replyMarkup)) {
            $options['reply_markup'] = $replyMarkup;
        }

        if (strlen($message) < self::MAX_LENGTH) {
            $this->nutgram->sendMessage($message, $options);
            return;
        }

        $messages = str_split($message, self::MAX_LENGTH);
        foreach ($messages as $message) {
            $this->nutgram->sendMessage($message, $options);
        }
    }
}
