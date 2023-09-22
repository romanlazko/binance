<?php 

namespace App\Bots\cryptognal_bot\Commands\UserCommands\Percent;

use App\Bots\cryptognal_bot\Commands\UserCommands\MenuCommand;
use App\Bots\cryptognal_bot\Models\PercentSubscription;
use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\DB;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class PercentSubscriptions extends Command
{
    public static $command = 'percent_subscriptions';

    public static $title = [
        'ru' => 'Проценты подписки',
        'en' => 'Percent subscriptions'
    ];

    public static $usage = ['percent_subscriptions'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $this->getConversation()->clear();
        
        $percentSubscriptions = PercentSubscription::where('telegram_chat_id', DB::getChat($updates->getChat()->getId())->id)->with('timeframe')->get();
        
        $buttons = $percentSubscriptions->map(function (PercentSubscription $percentSubscription) {
            return [array("{$percentSubscription->timeframe->title} -> {$percentSubscription->percent}%", ShowPercentSubscription::$command, $percentSubscription->id)];
        })->toArray();

        $buttons = BotApi::inlineKeyboard([
            ...$buttons,
            [array("Create", CreatePercentSubscription::$command, '')],
            [array("👈 Назад", MenuCommand::$command, '')]
        ], 'percent_subscription_id');

        $text = implode("\n", [
            "Percent subscriptions:"
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