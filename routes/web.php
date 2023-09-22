<?php

use App\Models\Price;
use Binance\API;
use Illuminate\Support\Facades\Route;
use Lin\Binance\Binance;
use Lin\Binance\BinanceDelivery;
use Lin\Binance\BinanceFuture;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $spot = new Binance("Lf6z1ErUmCgVBTtKWPKZCZyhYhHZjWSMFIYReLZqGuqRq7gZklUNyw4y1feY3Jz6","ZFhVhmz6uOusihdH0MR7KQYaZ1fJUSwxCRFXDwgmEyn8Yr9vKOnc5b22ivH6gE38");
    
    $candlesticks = $spot->system()->getKlines([
        'symbol' => "YGGUSDT",
        'interval' => "5m",
        'limit' => '50'
    ]);

    array_pop($candlesticks);
    $candlestick = end($candlesticks);

    

    dump($candlestick[5]);
});


Route::get('/candlesticks/averageweight/{symbol}/{interval}/{count}', function ($symbol, $interval, $count) {
    $api = new API("Lf6z1ErUmCgVBTtKWPKZCZyhYhHZjWSMFIYReLZqGuqRq7gZklUNyw4y1feY3Jz6","ZFhVhmz6uOusihdH0MR7KQYaZ1fJUSwxCRFXDwgmEyn8Yr9vKOnc5b22ivH6gE38");

    $candlesticks = array_values($api->candlesticks($symbol, $interval, $count+1));

    $percents = 0;

    echo($symbol.": $count свечей по $interval");
    foreach ($candlesticks as $key => $value) {
        if($key < $count-1){
            $percent = round((round($value['open'], 5)*100)/round($value['close'], 5)-100, 5);
            $percents += abs($percent);
            dump($percent);
            dump($value);
        }
    }

    $average = abs($percents)/$count;

    dump("Среднее волатильность всех свечей {$average}");
});

Route::get('/gpt/{symbol}/{interval}/{count}', function ($symbol, $interval, $count) {
    $api = new API("Lf6z1ErUmCgVBTtKWPKZCZyhYhHZjWSMFIYReLZqGuqRq7gZklUNyw4y1feY3Jz6","ZFhVhmz6uOusihdH0MR7KQYaZ1fJUSwxCRFXDwgmEyn8Yr9vKOnc5b22ivH6gE38");

    function getAveragePrice($symbol, $api, $interval, $count) {

        $candlesticks = $api->candlesticks($symbol, $interval, $count);

        // Извлекаем цены закрытия из свечей
        $closingPrices = array_map(function($candlestick) {
            return $candlestick['close'];
        }, $candlesticks);

        // Вычисляем среднее значение
        $averagePrice = array_sum($closingPrices) / count($closingPrices);

        return $averagePrice;
    }

    // Функция для определения, является ли цена аномально высокой или низкой
    function checkPriceAnomaly($symbol, $lastPrice, $api, $interval, $count) {
        $averagePrice = getAveragePrice($symbol, $api, $interval, $count);
        $volatility = calculateStandardDeviation($symbol, $api, $interval, $count);

        $zScore = ($lastPrice - $averagePrice) / $volatility;

        if ($zScore > 2) {
            return "Последняя цена криптовалюты $symbol является аномально высокой. lastPrice: $lastPrice, averagePrice: $averagePrice, volatility: $volatility, zScore: $zScore";
        } elseif ($zScore < -2) {
            return "Последняя цена криптовалюты $symbol является аномально низкой. lastPrice: $lastPrice, averagePrice: $averagePrice, volatility: $volatility, zScore: $zScore";
        } else {
            return "Последняя цена криптовалюты $symbol находится в пределах стандартной волатильности. lastPrice: $lastPrice, averagePrice: $averagePrice, volatility: $volatility, zScore: $zScore" ;
        }
    }

    // Функция для вычисления стандартного отклонения
    function calculateStandardDeviation($symbol, $api, $interval, $count) {

        $candlesticks = $api->candlesticks($symbol, $interval, $count);

        // Извлекаем цены закрытия из свечей
        $closingPrices = array_map(function($candlestick) {
            return $candlestick['close'];
        }, $candlesticks);

        // Вычисляем стандартное отклонение
        $mean = array_sum($closingPrices) / count($closingPrices);
        $variance = 0.0;

        foreach ($closingPrices as $price) {
            $variance += pow($price - $mean, 2);
        }

        $variance /= (count($closingPrices) - 1);
        $standardDeviation = sqrt($variance);

        return $standardDeviation;
    }

    $result = checkPriceAnomaly($symbol, $api->price($symbol), $api, $interval, $count);
    echo $result;
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';