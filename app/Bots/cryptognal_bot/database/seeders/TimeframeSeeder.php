<?php

namespace App\Bots\cryptognal_bot\Database\Seeders;

use App\Bots\cryptognal_bot\Models\Timeframe;
use Illuminate\Database\Seeder;

class TimeframeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Timeframe::insert([
            [
                'title' => '5m',
                'value' => 0
            ],
            [
                'title' => '15m',
                'value' => 2
            ],
            [
                'title' => '30m',
                'value' => 5
            ],
            [
                'title' => '1h',
                'value' => 11
            ],
        ]);
    }
}
