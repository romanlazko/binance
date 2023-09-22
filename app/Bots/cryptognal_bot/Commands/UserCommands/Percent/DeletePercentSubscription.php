<?php 

namespace App\Bots\cryptognal_bot\Commands\UserCommands\Percent;

use App\Bots\cryptognal_bot\Models\PercentSubscription;
use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class DeletePercentSubscription extends Command
{
    public static $command = 'delete_subscription';

    public static $usage = ['delete_subscription'];

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        PercentSubscription::find($updates->getInlineData()->getPercentSubscriptionId())->delete();

        return $this->bot->executeCommand(PercentSubscriptions::$command);

    }
}