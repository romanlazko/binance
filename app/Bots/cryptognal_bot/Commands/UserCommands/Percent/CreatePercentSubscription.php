<?php 

namespace App\Bots\cryptognal_bot\Commands\UserCommands\Percent;

use App\Bots\cryptognal_bot\Commands\UserCommands\MenuCommand;
use App\Bots\cryptognal_bot\Models\PercentSubscription;
use App\Bots\cryptognal_bot\Models\Timeframe;
use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class CreatePercentSubscription extends Command
{
    public static $command = 'create_percent_subscription';

    public static $title = [
        'ru' => 'Создать подписку',
        'en' => 'Create percent subscription'
    ];

    public static $usage = ['create_percent_subscription'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $notes = function ($key) {
            return $this->getConversation()->notes[$key] ?? null;
        };

        $buttons = BotApi::inlineKeyboard([
            [array(PercentTimeframe::getTitle('en'). ": ". Timeframe::find($notes('timeframe'))?->title, PercentTimeframe::$command, '')],
            [array(Percent::getTitle('en'). ": ". $notes('percent'), Percent::$command, '')],
            [array(SavePercentSubscription::getTitle('en'), SavePercentSubscription::$command, '')],
            [array("👈 Назад", MenuCommand::$command, '')]
        ]);

        $text = implode("\n", [
            "Create percent subscription:"
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