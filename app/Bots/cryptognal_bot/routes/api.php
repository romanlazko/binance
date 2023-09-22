<?php
use App\Bots\cryptognal_bot\Http\Services\ChartService;
use App\Bots\cryptognal_bot\Models\FuturePrice;
use App\Bots\cryptognal_bot\Models\PercentSubscription;
use App\Bots\cryptognal_bot\Models\SpotPrice;
use App\Bots\cryptognal_bot\Models\Timeframe;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Lin\Binance\Binance;
use Lin\Binance\BinanceFuture;
use Romanlazko\Telegram\App\Telegram;
use Romanlazko\Telegram\App\TelegramLogDb;
use Romanlazko\Telegram\Exceptions\TelegramException;

Route::middleware(['api'])->prefix('api/telegram/{bot}/spot')->group(function () {
    Route::get('/percent', function(Telegram $telegram, ChartService $chartService){
        try {
            $spot = new Binance("Lf6z1ErUmCgVBTtKWPKZCZyhYhHZjWSMFIYReLZqGuqRq7gZklUNyw4y1feY3Jz6","ZFhVhmz6uOusihdH0MR7KQYaZ1fJUSwxCRFXDwgmEyn8Yr9vKOnc5b22ivH6gE38");
    
            $new_prices = $spot->system()->getTickerPrice();
    
            foreach ($new_prices as $price) {
                if (strpos($price['symbol'], "USDT")) {
                    $prices[$price['symbol']] = $price['price'];
                }
            }
    
            SpotPrice::create([
                'prices' => json_encode($prices)
            ]);
    
            $timeframes = Timeframe::all();
    
            $percentSubscriptionsData = PercentSubscription::with(['timeframe', 'telegram_chat'])->get();
    
            foreach ($timeframes as $timeframe) {
                $old_prices = json_decode(SpotPrice::latest()->get()?->get($timeframe->value)?->prices, true);
    
                foreach ($prices as $symbol => $price) {
    
                    if (!isset($old_prices[$symbol])) {
                        continue;
                    }
    
                    $changePercentage = round(((($price - $old_prices[$symbol] ) / $old_prices[$symbol] ) * 100), 3);
    
                    $percentSubscriptions = $percentSubscriptionsData->where('timeframe_id', $timeframe->id);
    
                    foreach ($percentSubscriptions as $percentSubscription) {
                        if ($changePercentage < 0 AND abs($changePercentage) >= $percentSubscription->percent) {
                            $i = 0;
    
                            $candlesticks = $spot->system()->getKlines([
                                'symbol' => $symbol,
                                'interval' => $timeframe->title,
                                'limit' => '50'
                            ]);
    
                            $data = [];
    
                            foreach ($candlesticks as $candlestick) {
                                $data['open'][] =  $candlestick['1'];
                                $data['close'][] =  $candlestick['4'];
                                $data['max'][] =  $candlestick['2'];
                                $data['min'][] =  $candlestick['3'];
                                if (($i+1 % 5) == 0) {
                                    $data['time'][] = Carbon::createFromTimestamp($candlestick['6'])->format('H:i');
                                }else {
                                    $data['time'][] = '';
                                }
                                $i++;
                            }
    
                            $path = $chartService->candlesticksChart($data, "charts/spot/{$symbol}_{$timeframe->title}.png");
    
                            array_pop($candlesticks);
                            $candlestick = end($candlesticks);
    
                            $text = implode("\n", [
                                "*#SPOT_$symbol*: [{$timeframe->title}](https://7772-2a02-8308-a006-d600-6515-ecb0-7c26-a3d9.ngrok-free.app/$path?".rand(0,1000000).")",
                                "*Изменение цены: $changePercentage%*"."\n",
                                "Текущая цена: *[$price USDT]*",
                                "Цена $timeframe->title незад: *{$old_prices[$symbol]} USDT*"."\n",
                                
                                "Volume: *".round($candlestick['5'])."*",
                                "Quote asset volume: *".round($candlestick['7'])."*",
                                "Number of trades: *".$candlestick['8']."*",
                                "Taker buy base asset volume: *".round($candlestick['9'])."*",
                                "Taker buy quote asset volume: *".round($candlestick['10'])."*"
                            ]);
    
                            try {
                                $telegram::sendMessage([
                                    'text'          =>  $text,
                                    'chat_id'       =>  $percentSubscription->telegram_chat->chat_id,
                                    'parse_mode'    =>  'Markdown',
                                ]);
                            }
                            catch (TelegramException|\Exception|\Throwable|\Error $exception) {
                                TelegramLogDb::report($telegram->botId, $exception);
                            } 
                        }
                    }
                }
            }
        }
        catch (TelegramException|\Exception|\Throwable|\Error $exception) {
            TelegramLogDb::report($telegram->botId, $exception);
        }
    });
});

Route::middleware(['api'])->prefix('api/telegram/{bot}/future')->group(function () {
    Route::get('/percent', function(Telegram $telegram, ChartService $chartService){
        try {
            $future = new BinanceFuture("Lf6z1ErUmCgVBTtKWPKZCZyhYhHZjWSMFIYReLZqGuqRq7gZklUNyw4y1feY3Jz6","ZFhVhmz6uOusihdH0MR7KQYaZ1fJUSwxCRFXDwgmEyn8Yr9vKOnc5b22ivH6gE38");
    
            $new_prices = $future->market()->getTickerPrice();
    
            foreach ($new_prices as $price) {
                if (strpos($price['symbol'], "USDT")) {
                    $prices[$price['symbol']] = $price['price'];
                }
            }
    
            FuturePrice::create([
                'prices' => json_encode($prices)
            ]);
    
            $timeframes = Timeframe::all();
    
            $percentSubscriptionsData = PercentSubscription::with(['timeframe', 'telegram_chat'])->get();
    
            foreach ($timeframes as $timeframe) {
                $old_prices = json_decode(FuturePrice::latest()->get()?->get($timeframe->value)?->prices, true);
    
                foreach ($prices as $symbol => $price) {
    
                    if (!isset($old_prices[$symbol])) {
                        continue;
                    }
    
                    $changePercentage = round(((($price - $old_prices[$symbol] ) / $old_prices[$symbol] ) * 100), 3);
    
                    $percentSubscriptions = $percentSubscriptionsData->where('timeframe_id', $timeframe->id);
    
                    foreach ($percentSubscriptions as $percentSubscription) {
                        if (abs($changePercentage) >= $percentSubscription->percent) {
                            $i = 0;
    
                            $candlesticks = $future->market()->getKlines([
                                'symbol' => $symbol,
                                'interval' => $timeframe->title,
                                'limit' => '50'
                            ]);
    
                            $data = [];
    
                            foreach ($candlesticks as $candlestick) {
                                $data['open'][] =  $candlestick['1'];
                                $data['close'][] =  $candlestick['4'];
                                $data['max'][] =  $candlestick['2'];
                                $data['min'][] =  $candlestick['3'];
                                if (($i % 4) == 0) {
                                    $data['time'][] = Carbon::createFromTimestamp($candlestick['6'])->format('H:i');
                                }else {
                                    $data['time'][] = '';
                                }
                                $i++;
                            }
    
                            $path = $chartService->candlesticksChart($data, "charts/future/{$symbol}_{$timeframe->title}.png");
    
                            array_pop($candlesticks);
                            $candlestick = end($candlesticks);
    
                            $text = implode("\n", [
                                "*#FUTURE_$symbol*: [{$timeframe->title}](https://7772-2a02-8308-a006-d600-6515-ecb0-7c26-a3d9.ngrok-free.app/$path?".rand(0,1000000).")",
                                "*Изменение цены: $changePercentage%*"."\n",
                                "Текущая цена: *[$price USDT]*",
                                "Цена $timeframe->title незад: *{$old_prices[$symbol]} USDT*"."\n",

                                "Volume: *".round($candlestick['5'])."*",
                                "Quote asset volume: *".round($candlestick['7'])."*",
                                "Number of trades: *".$candlestick['8']."*",
                                "Taker buy base asset volume: *".round($candlestick['9'])."*",
                                "Taker buy quote asset volume: *".round($candlestick['10'])."*"
                            ]);
    
                            try {
                                $telegram::sendMessage([
                                    'text'          =>  $text,
                                    'chat_id'       =>  $percentSubscription->telegram_chat->chat_id,
                                    'parse_mode'    =>  'Markdown',
                                ]);
                            }
                            catch (TelegramException|\Exception|\Throwable|\Error $exception) {
                                TelegramLogDb::report($telegram->botId, $exception);
                            } 
                        }
                    }
                }
            }
        }
        catch (TelegramException|\Exception|\Throwable|\Error $exception) {
            TelegramLogDb::report($telegram->botId, $exception);
        }
    });
});