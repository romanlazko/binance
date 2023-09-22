<?php 

namespace App\Bots\cryptognal_bot\Commands\UserCommands\Percent;

use Romanlazko\Telegram\App\Commands\Command;
use Romanlazko\Telegram\App\Entities\Response;
use Romanlazko\Telegram\App\Entities\Update;

class AwaitPercent extends Command
{
    public static $expectation = 'await_percent';

    public static $pattern = '/^await_percent$/';

    protected $enabled = true;

    public function execute(Update $updates): Response
    {
        $text = $updates->getMessage()?->getText();

        if ($text === null) {
            $this->handleError("*Пришлите пожалуйста проценты в виде текстового сообщения.*");
            return $this->bot->executeCommand(Percent::$command);
        }

        if (!is_numeric($text)){
            $this->handleError("*Проценты должны быть указаны только цифрами*");
            return $this->bot->executeCommand(Percent::$command);
        }

        if (iconv_strlen($text) > 12){
            $this->handleError("*Слишком много символов*");
            return $this->bot->executeCommand(Percent::$command);
        }

        if ($text > 100 OR $text < 3){
            $this->handleError("*Проценты не могут быть меньше чем 3 или больше чем 100*");
            return $this->bot->executeCommand(Percent::$command);
        }

        $this->getConversation()->update([
            'percent' => $text,
        ]);

        return $this->bot->executeCommand(CreatePercentSubscription::$command);
    }
}
