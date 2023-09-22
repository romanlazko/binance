<?php 

namespace App\Bots\cryptognal_bot\Commands\UserCommands;

use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class MenuCommand extends Command
{
    public static $command = '/menu';

    public static $title = [
        'ru' => '🏠 Главное меню',
        'en' => '🏠 Menu'
    ];

    public static $usage = ['/menu', 'menu', 'Главное меню', 'Menu'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $buttons = BotApi::inlineKeyboard([
            
            [array(Subscriptions::getTitle('en'), Subscriptions::$command, '')],
            [array(HelpCommand::getTitle('en'), HelpCommand::$command, '')],
        ]);

        $text = implode("\n", [
            "Hi 👋",
            "This is the main menu!"
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