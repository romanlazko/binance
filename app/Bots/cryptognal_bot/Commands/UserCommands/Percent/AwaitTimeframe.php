<?php 

namespace App\Bots\cryptognal_bot\Commands\UserCommands\Percent;

use App\Bots\cryptognal_bot\Commands\UserCommands\MenuCommand;
use Romanlazko\Telegram\App\BotApi;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class AwaitTimeframe extends Command
{
    public static $command = 'await_timeframe';

    public static $usage = ['await_timeframe'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $this->getConversation()->update([
            'timeframe' => $updates->getInlineData()->getTimeframe(),
        ]);

        return $this->bot->executeCommand(CreatePercentSubscription::$command);
    }
}