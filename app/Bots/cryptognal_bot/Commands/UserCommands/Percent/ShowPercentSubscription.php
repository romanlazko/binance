<?php 

namespace App\Bots\cryptognal_bot\Commands\UserCommands\Percent;

use App\Bots\cryptognal_bot\Commands\UserCommands\MenuCommand;
use App\Bots\cryptognal_bot\Models\PercentSubscription;
use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class ShowPercentSubscription extends Command
{
    public static $command = 'show_subscriptions';

    public static $usage = ['show_subscriptions'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $percentSubscription = PercentSubscription::with('timeframe')->find($updates->getInlineData()->getPercentSubscriptionId());

        $buttons = BotApi::inlineKeyboard([
            [array("Delete", DeletePercentSubscription::$command, $percentSubscription->id)],
            [array("👈 Назад", MenuCommand::$command, '')]
        ], 'percent_subscription_id');

        $text = implode("\n", [
            "{$percentSubscription->timeframe->title} -> {$percentSubscription->percent}%"
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