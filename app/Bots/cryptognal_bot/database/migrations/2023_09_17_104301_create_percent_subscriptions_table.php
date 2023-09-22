<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('percent_subscriptions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('telegram_chat_id')->nullable();
            $table->foreign('telegram_chat_id')->on('telegram_chats')->references('id');

            $table->unsignedBigInteger('timeframe_id')->nullable();
            $table->foreign('timeframe_id')->on('timeframes')->references('id');
            
            $table->integer('percent')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('percent_subscriptions');
    }
};
