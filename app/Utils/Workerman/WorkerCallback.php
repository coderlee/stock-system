<?php

namespace App\Utils\Workerman;

use Workerman\Lib\Timer;
use App\Jobs\LeverUpdate;
use App\Jobs\LeverPushPrice;
use App\MarketHour;

class WorkerCallback
{
    protected $events = [
        'onWorkerStart',
        'onConnect',
        'onMessage',
        'onClose',
        'onError',
        'onBufferFull',
        'onBufferDrain',
        'onWorkerStop',
        'onWorkerReload'
    ];

    protected $interval = 1; //行情处理时间间隔，单位秒，支持小数
    protected $wsConnection; //websocket client连接
    protected $worker;

    public function __construct()
    {
        echo("__construct\n\r");
        $this->registerEvent();
    }

    public function registerEvent()
    {
        foreach ($this->events as $key => $event) {
            method_exists($this, $event) && $this->$event = [$this, $event];
        }
    }

    public function onWorkerStart($worker)
    {
        echo("onWorkerStart1\r\n");
        $this->worker = $worker;
        echo '进程' . $worker->id .'启动'. PHP_EOL;
       
        $periods = ['1min', '5min', '15min', '30min', '60min', '1day', '1mon', '1week']; //['1day', '1min']; 
        $period = $periods[$worker->id];
        $worker->name = 'huobi.ws:' . 'market.kline.' . $period;
        $ws_con = new WsConnection($worker->id);
        $this->wsConnection = $ws_con;
        $ws_con->connect();
        echo("bg2\r\n");
        if ($period == '1day') {
            echo("bg3\r\n");
            Timer::add($this->interval, function () {
                $data = MarketHour::getHuobiLeverMarket();
                echo date('Y-m-d H:i:s') . '定时器取价格' . PHP_EOL;
                
                $now = microtime(true);
                $master_start = microtime(true);
                echo str_repeat('=', 80) . PHP_EOL;
                echo date('Y-m-d H:i:s') . '开始发送价格到合约交易系统' . PHP_EOL;
                
                echo '{' . PHP_EOL;
                foreach ($data as $key => $value) {
                    $start = microtime(true);
                    echo "\t" . date('Y-m-d H:i:s') . ' 发送' . $value->symbol . ',价格:' . $value->now_price . PHP_EOL;
                    $params = [
                        'legal_id' => $value->legal_id,
                        'legal_name' => $value->legal_name,
                        'currency_id' => $value->currency_id,
                        'currency_name' => $value->currency_name,
                        'now_price' => $value->now_price,
                        'now' => $now
                    ];
                    //价格大于0才进行任务推送
                    if (bc_comp($value->now_price, 0) > 0) {
                        LeverUpdate::dispatch($params)->onQueue('lever:update');
                        LeverPushPrice::dispatch($params)->onQueue('lever:push:price');
                    }
                    $end = microtime(true);
                    echo "\t" . date('Y-m-d H:i:s') . $value->symbol . '处理完成,耗时' .($end - $start) . '秒' . PHP_EOL;
                }
                $master_end = microtime(true);
                echo '}' . PHP_EOL;
                                    file_put_contents("leverLog.txt", '完成发送价格到合约交易系统'.date('Y-m-d H:i:s'),FILE_APPEND);
                echo date('Y-m-d H:i:s') .'合约交易系统处理完成,耗时' .($master_end - $master_start) . '秒' . PHP_EOL;
                echo str_repeat('=', 80) . PHP_EOL;
            });
        }else{
            echo("bg4\r\n");
        }
    }

    public function onWorkerReload($worker)
    {
    }

    public function onConnect($connection)
    {
    }

    public function onClose($connection)
    {
    }

    public function onError($connection, $code, $msg)
    {
    }

    public function onMessage($connection, $data)
    {
    }
}