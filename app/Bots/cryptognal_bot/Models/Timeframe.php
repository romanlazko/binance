<?php

namespace App\Bots\cryptognal_bot\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timeframe extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
}
