<?php

namespace App\Bots\cryptognal_bot\Http\Services;

use CpChart\Chart\Stock;
use CpChart\Data;
use CpChart\Image;

class ChartService 
{

    public function candlesticksChart(array $data, $path, $width = 900, $height = 600): string|bool
    {
        $myData = new Data($width, $height);

        $myData->addPoints($data['open'], "Open"); 
        $myData->addPoints($data['close'], "Close"); 
        $myData->addPoints($data['min'], "Min"); 
        $myData->addPoints($data['max'], "Max"); 
        
        $myData->setAxisDisplay(0,AXIS_FORMAT_CURRENCY,"$");
        $myData->addPoints($data['time'], "Time");
        $myData->setAbscissa("Time");

        $myPicture = new Image($width, $height, $myData);
        $myPicture->setGraphArea(40, 40, $width-40, $height-40);
 
        $myPicture->drawScale([
            "GridR" => 0, 
            "GridG" => 0, 
            "GridB" => 0, 
            "DrawSubTicks" => TRUE, 
            "CycleBackground" => TRUE, 
            "XMargin" => 2,
        ]);

        $mystockChart = new Stock($myPicture, $myData);
        $mystockChart->drawStockChart([
            "BoxUpR" => 93,
            "BoxUpG" => 249,
            "BoxUpB" => 76,
            "BoxDownR" => 249,
            "BoxDownG" => 99,
            "BoxDownB" => 76
        ]);

        return $myPicture->Render($path) ? $path : false;
    }
}