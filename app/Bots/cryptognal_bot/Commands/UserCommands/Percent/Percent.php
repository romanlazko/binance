<?php 

namespace App\Bots\cryptognal_bot\Commands\UserCommands\Percent;

use App\Bots\cryptognal_bot\Commands\UserCommands\MenuCommand;
use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class Percent extends Command
{
    public static $command = 'percent';

    public static $title = [
        'ru' => 'Проценты',
        'en' => 'Percent'
    ];

    public static $usage = ['percent'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $updates->getFrom()->setExpectation(AwaitPercent::$expectation);
        
        $buttons = BotApi::inlineKeyboard([
            [
                array("👈 Назад", CreatePercentSubscription::$command, ''),
                array(MenuCommand::getTitle('ru'), MenuCommand::$command, '')
            ]
        ]);

        $data = [
            'text'          => "Укажи изменение в процентах, которые ты хочешь отслеживать:"."\n\n"."_Максимально_ *12* _символов_.",
            'chat_id'       => $updates->getChat()->getId(),
            'parse_mode'    => "Markdown",
            'reply_markup'  => $buttons,
            'message_id'    =>  $updates->getCallbackQuery()?->getMessage()?->getMessageId(),
        ];

        return BotApi::returnInline($data);
    }
}