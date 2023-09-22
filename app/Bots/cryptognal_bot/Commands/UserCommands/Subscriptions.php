<?php 

namespace App\Bots\cryptognal_bot\Commands\UserCommands;

use App\Bots\cryptognal_bot\Commands\UserCommands\Percent\PercentSubscriptions;
use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class Subscriptions extends Command
{
    public static $command = 'subscriptions';

    public static $title = [
        'ru' => 'Подписки',
        'en' => 'Subscriptions'
    ];

    public static $usage = ['subscriptions'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $buttons = BotApi::inlineKeyboard([
            [array(PercentSubscriptions::getTitle('en'), PercentSubscriptions::$command, '')],
        ]);

        $text = implode("\n", [
            "Here is a list of yours subscriptions"
        ]);

        $data = [
            'text'          =>  $text,
            'chat_id'       =>  $updates->getChat()->getId(),
            'reply_markup'  =>  $buttons,
            'parse_mode'    =>  'Markdown',
            'message_id'    =>  $updates->getCallbackQuery()?->getMessage()->getMessageId(),
        ];

        return BotApi::returnInline($data);
    }
}