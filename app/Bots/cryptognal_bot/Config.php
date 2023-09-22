<?php

namespace App\Bots\cryptognal_bot;


class Config
{
    public static function getConfig()
    {
        return [
            'inline_data'       => [
                'percent_subscription_id' => null,
                'timeframe' => null,
                'percent' => null,
            ],
            'lang'              => 'ru',
            'admin_ids'         => [
            ],
        ];
    }
}
