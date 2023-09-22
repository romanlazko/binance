<?php 

namespace App\Bots\cryptognal_bot\Commands\UserCommands\Percent;

use App\Bots\cryptognal_bot\Commands\UserCommands\MenuCommand;
use App\Bots\cryptognal_bot\Models\PercentSubscription;
use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\DB;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class SavePercentSubscription extends Command
{
    public static $command = 'save_percent_subscription';

    public static $title = [
        'ru' => 'Сохранить',
        'en' => 'Save percent subscription'
    ];

    public static $usage = ['save_percent_subscription'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $notes = $this->getConversation()->notes;

        $fields = ['timeframe', 'percent'];

        foreach ($fields as $field) {
            if (empty($notes[$field])) {
                return BotApi::answerCallbackQuery([
                    'callback_query_id' => $updates->getCallbackQuery()->getId(),
                    'text'              => 'Пожалуйста, заполни все поля.',
                    'show_alert'        => true
                ]);
            }
        }

        PercentSubscription::updateOrCreate([
            'telegram_chat_id'  => DB::getChat($updates->getChat()->getId())->id,
            'timeframe_id'         => $notes['timeframe'],
            
        ],[
            'percent'           => $notes['percent'],
        ]);

        $this->getConversation()->clear();

        return $this->bot->executeCommand(PercentSubscriptions::$command);
    }
}