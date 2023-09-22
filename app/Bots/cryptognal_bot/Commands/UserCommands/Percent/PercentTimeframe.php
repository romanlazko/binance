<?php 

namespace App\Bots\cryptognal_bot\Commands\UserCommands\Percent;

use App\Bots\cryptognal_bot\Commands\UserCommands\MenuCommand;
use App\Bots\cryptognal_bot\Models\Timeframe;
use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class PercentTimeframe extends Command
{
    public static $command = 'percent_timeframe';

    public static $title = [
        'ru' => 'Таймфрейм',
        'en' => 'Timeframe'
    ];

    public static $usage = ['percent_timeframe'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $timeframes = Timeframe::all();

        $buttons = $timeframes->map(function (Timeframe $timeframe) {
            return [array($timeframe->title, AwaitTimeframe::$command, $timeframe->id)];
        })->toArray();

        $buttons = BotApi::inlineKeyboard([
            ...$buttons,
            [
                array("👈 Назад", CreatePercentSubscription::$command, ''),
                array(MenuCommand::getTitle('ru'), MenuCommand::$command, ''),
            ]
        ], 'timeframe');

        $data = [
            'text'          => "Choose timeframe:",
            'chat_id'       => $updates->getChat()->getId(),
            'parse_mode'    => "Markdown",
            'message_id'    => $updates->getCallbackQuery()?->getMessage()?->getMessageId(),
            'reply_markup'  => $buttons
        ];

        return BotApi::returnInline($data);
    }
}