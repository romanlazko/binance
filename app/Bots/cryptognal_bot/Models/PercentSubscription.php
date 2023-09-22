<?php

namespace App\Bots\cryptognal_bot\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Romanlazko\Telegram\Models\TelegramChat;

class PercentSubscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function timeframe() 
    {
        return $this->belongsTo(Timeframe::class);
    }

    public function telegram_chat()
    {
        return $this->belongsTo(TelegramChat::class);
    }
}
