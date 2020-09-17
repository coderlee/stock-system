<?php

namespace App\Utils\Workerman;

use Workerman\Connection\AsyncTcpConnection;
use Workerman\Lib\Timer;
use App\Jobs\SendMarket;
use App\Jobs\WriteMarket;
use App\CurrencyQuotation;
use App\CurrencyMatch;
use App\UserChat;
use App\MarketHour;
use App\Jobs\EsearchMarket;

class WsConnection
{
   //   protected $server_address = 'ws://api.huobi.pro:443/ws';
     protected $server_address = 'ws://api.huobi.br.com:443/ws'; //ws国内开发调试
    // $con = new AsyncTcpConnection('ws://api.huobi.pro:443/ws');
    protected $server_ping_freq = 5; //服务器ping检测周期,单位秒
    protected $server_time_out = 10; //服务器响应超时
    protected $send_freq = 0.5; //写入和发送数据的周期，单位秒

    protected $worker_id;

    protected $events = [
        'onConnect',
        'onClose',
        'onMessage',
        'onError',
        'onBufferFull',
        'onBufferDrain',
    ];

    protected static $marketKlineData = [];

    protected $timer;

    protected $pingTimer;

    protected $connection;

    protected $sendTimer;

    protected $subscribed = [];

    protected $topicTemplate = [
        'sub' => [
            'market_kline' => 'market.$symbol.kline.$period',
            'market_detail' => 'market.$symbol.detail',
        ],
    ];

    public function __construct($worker_id)
    {
        $this->worker_id = $worker_id;
        AsyncTcpConnection::$defaultMaxPackageSize = 1048576000;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * 绑定所有事件到连接
     *
     * @return void
     */
    protected function bindEvent()
    {
        foreach ($this->events as $key => $event) {
            if (method_exists($this, $event)) {
                $this->connection && $this->connection->$event = [$this, $event];
                //echo '绑定' . $event . '事件成功' . PHP_EOL;
            }
        }
        
    }

    /**
     * 解除连接所有绑定事件
     *
     * @return void
     */
    protected function unBindEvent()
    {
        foreach ($this->events as $key => $event) {
            if (method_exists($this, $event)) {
                $this->connection && $this->connection->$event = null;
                //echo '解绑' . $event . '事件成功' . PHP_EOL;
            }
        }
    }

    public function getSubscribed($topic = null)
    {
        if (is_null($topic)) {
            return $this->subscribed;
        }
        return $this->subscribed[$topic] ?? null;
        
    }

    protected function setSubscribed($topic, $value)
    {
        $this->subscribed[$topic] = $value;
        
    }

    protected function delSubscribed($topic)
    {
        unset($this->subscribed[$topic]);
    }

    public function connect()
    {
        $this->connection = new AsyncTcpConnection($this->server_address);
        $this->bindEvent();
        $this->connection->transport = 'ssl';
        $this->connection->connect();
        echo("连接\r\n");
       
    }

    public function onConnect($con)
    {
       
        echo("连接成功后\r\n");
        //连接成功后定期发送ping数据包检测服务器是否在线
        $this->timer = Timer::add($this->server_ping_freq, [$this, 'ping'], [$this->connection], true);
        $this->sendTimer = Timer::add($this->send_freq, [self::class, 'writeMarketKline'], [], true);
        //添加订阅事件代码
        $this->subscribe($con);
        
    }

    public function onClose($con)
    {
        
        echo $this->server_address . '连接关闭' . PHP_EOL;
      
        $path = base_path() . '/storage/logs/wss/';
        $filename = date('Ymd') . '.log';
        file_exists($path) || @mkdir($path);
        error_log(date('Y-m-d H:i:s') . ' ' . $this->server_address . '连接关闭' . PHP_EOL, 3, $path . $filename);
        //解除事件
        $this->timer && Timer::del($this->timer);
        $this->sendTimer && Timer::del($this->sendTimer);
        $this->pingTimer && Timer::del($this->pingTimer);
        $this->unBindEvent();
        unset($this->connection);
        $this->connection = null;
        $this->subscribed = null; //清空订阅
        echo '尝试重新连接' . PHP_EOL;
        $this->connect();
    }

    public function close($msg)
    {
        $path = base_path() . '/storage/logs/wss/';
        $filename = date('Ymd') . '.log';
        file_exists($path) || @mkdir($path);
        error_log(date('Y-m-d H:i:s') . ' ' . $msg, 3, $path . $filename);
        $this->connection->destroy();
    }

    protected function makeSubscribeTopic($topic_template, $param)
    {
        
        echo("makeSubscribeTopic\r\n");
        $need_param = [];
        $match_count = preg_match_all('/\$([a-zA-Z_]\w*)/', $topic_template, $need_param);
        if ($match_count > 0 && count($need_param[0]) > count($param)) {
            throw new \Exception('所需参数不匹配');
        }
        $diff = array_diff($need_param[1], array_keys($param));
        if (count($diff) > 0) {
            throw new \Exception('topic:' . $topic_template . '缺少参数：' . implode(',', $diff));
        }
        return preg_replace_callback('/\$([a-zA-Z_]\w*)/', function ($matches) use ($param) {
            extract($param);
            $value = $matches[1];
            return $$value ?? '';
        }, $topic_template);
       
    }

    public function onBufferFull()
    {
        
        echo 'buffer is full' . PHP_EOL;
       
    }

    protected function subscribe($con)
    {
        echo("subscribe\n\r");
        $periods = ['1min', '5min', '15min', '30min', '60min', '1day', '1mon', '1week']; //['1day', '1min'];
       // $value = $periods[$this->worker_id];
       $value = $periods[5];
        echo($this->worker_id."\n\r");
        echo '进程'. $this->worker_id . '开始订阅' . $value . '数据' . PHP_EOL;
        // $this->subscribeKline($con, $value); //订阅1分钟k线行情
          $this->subscribeKline($con, "1min"); 
          $this->subscribeKline($con, "1day"); //订阅1分钟k线行情
     
    }

    //订阅24小时内最新行情详情
    protected function subscribeKline($con, $period)
    {
        
        echo("订阅开始\r\n");
        $currency_match = CurrencyMatch::getHuobiMatchs();
       
        foreach ($currency_match as $key => $value) {
            $param = [
                'symbol' => $value->match_name,
                'period' => $period,
            ];
            

            $topic = $this->makeSubscribeTopic($this->topicTemplate['sub']['market_kline'], $param);
            $sub_data = json_encode([
                'sub' => $topic,
                'id' => $topic,
                //'freq-ms' => 5000, //推送频率，实测只能是0和5000，与官网文档不符
            ]);
           // print_r($sub_data);
          //  exit("\n\r");
            //未订阅过的才能订阅
            if (is_null($this->getSubscribed($topic))) {
                $this->setSubscribed($topic, [
                    'callback' => 'onMarketKline',
                    'match' => $value
                ]);
                $con->send($sub_data);
            }
        }
        
    }

    //订阅回调
    protected function onSubscribe($data)
    {
        
        if ($data->status == 'ok') {
            echo $data->subbed . '订阅成功' . PHP_EOL;
        } else {
            echo '订阅失败:' . $data->{'err-msg'} . PHP_EOL;
        }
    }

    //取消订阅
    protected function unsubscribe()
    {
    }

    protected function onUnsubscribe()
    {
    }

    public function onMessage($con, $data)
    {
        //exit();
        
        echo("onMessage链接开始\r\n");
       
        $data = gzdecode($data);
        $data = json_decode($data);
       // print_r($data);
       // exit();
        if (isset($data->ping)) {
            $this->onPong($con, $data);
        } elseif (isset($data->pong)) {
            $this->onPing($con, $data);
        } elseif (isset($data->id) && $this->getSubscribed($data->id) != null) {
            $this->onSubscribe($data);
        } elseif (isset($data->id)) {

        } else {
            $this->onData($con, $data);
           // print_r($data);
           // exit();
        }
       
    }

    protected function onData($con, $data)
    { // print_r($data);
         //    exit();
       
        if (isset($data->ch)) {
            $subscribed = $this->getSubscribed($data->ch);
            if ($subscribed != null) {
                //调用回调处理
                $callback = $subscribed['callback'];
                $this->$callback($con, $data, $subscribed['match']);
            } else {
                //不在订阅中的数据
            }
        } else {
            echo '未知数据' . PHP_EOL;
           var_dump($data);
        }
    }

    public static function writeMarketKline()
    {
        
       
        echo("writeMarketKline\n\r");
        $market_data = self::$marketKlineData;
       
       
        foreach ($market_data as $period => $data) {
           // print_r($data);
           // exit();
            foreach ($data as $key => $symbol) {
                 print_r($symbol);
                echo '处理' . $key . '.' . $period . '数据' . PHP_EOL;
               //  EsearchMarket::dispatch($symbol['market_data'])->onQueue('esearch:market:' . $period);
                if ($period == '1min') {
                    //推送一分钟行情
                    echo("推送一分钟行情\n\r");
                    print_r($symbol['kline_data']);
                     SendMarket::dispatch($symbol['kline_data'])->onQueue('kline.1min');
                   
                } elseif ($period == '1day') {
                    //推送一天行情
                     SendMarket::dispatch($symbol['kline_data'])->onQueue('kline.1day');
                    
                } else {
                    continue;
                }
                
            }
        }
       
       
    }

    protected function onMarketKline($con, $data, $match)
    {    
        echo("onMarketKline\n\r");
        $topic = $data->ch;
        $msg = date('Y-m-d H:i:s') . ' 进程' . $this->worker_id . '接收' . $topic  . '行情' . PHP_EOL;
       // exit();
        list($name, $symbol, $detail_name, $period) = explode('.', $topic);
        $subscribed_data = $this->getSubscribed($topic);
        $currency_match = $subscribed_data['match'];
        $tick = $data->tick;
        $market_data = [
            'id' => $tick->id,
            'period' => $period,
            'base-currency' => $currency_match->currency_name,
            'quote-currency' => $currency_match->legal_name,
            'open' => sctonum($tick->open),
            'close' => sctonum($tick->close),
            'high' => sctonum($tick->high),
            'low' => sctonum($tick->low),
            'vol' => sctonum($tick->vol),
            'amount' => sctonum($tick->amount),
        ];
        $kline_data = [
            'type' => 'kline',
            'period' => $period,
            'currency_id' => $currency_match->currency_id,
            'currency_name' => $currency_match->currency_name,
            'legal_id' => $currency_match->legal_id,
            'legal_name' => $currency_match->legal_name,
            'open' => sctonum($tick->open),
            'close' => sctonum($tick->close),
            'high' => sctonum($tick->high),
            'low' => sctonum($tick->low),
            'symbol' => $currency_match->currency_name . '/' . $currency_match->legal_name,
            'volume' => sctonum($tick->amount),
            'time' => $tick->id * 1000,
        ];
        //EsearchMarket::dispatch($market_data)->onQueue('esearch:market:' . $period);
        $key = $currency_match->currency_name . '.' . $currency_match->legal_name;
        self::$marketKlineData[$period][$key] = [
            'market_data' => $market_data,
            'kline_data' => $kline_data,
        ];
        if ($period == '1min') {
            // $params = [
            //     'currency_id' => $currency_match->currency_id,
            //     'legal_id' => $currency_match->legal_id,
            //     'market_data' => $market_data,
            //     'sign' => 2,
            //     'time' => $tick->id
            // ];
            //SendMarket::dispatch($kline_data)->onQueue('kline.1min');
            //WriteMarket::dispatch($params)->onConnection('sync')->onQueue('write:market'); //数据库写入太慢了，造成阻塞，弃用
        } elseif ($period == '1day') {
            //推送币种的日行情(带涨副)
            $change = $this->calcIncreasePair($kline_data);
            bc_comp($change, 0) > 0 && $change = '+' . $change;
            $daymarket_data = [
                'type' => 'daymarket',
                'change' => $change,
                'now_price' => $market_data['close'],
                'api_form' => 'huobi_websocket',
            ];
            $kline_data = array_merge($kline_data, $daymarket_data);
            self::$marketKlineData[$period][$key]['kline_data'] = $kline_data;
            //SendMarket::dispatch($kline_data)->onQueue('kline.1day');
            //存入数据库
            CurrencyQuotation::getInstance($currency_match->legal_id, $currency_match->currency_id)
                ->updateData([
                    'change' => $change,
                    'now_price' => $tick->close,
                    'volume' => $tick->amount,
                ]);
        }
       
    }

    protected function calcIncreasePair($kline_data)
    {
        
        $open = $kline_data['open'];
        $close = $kline_data['close'];;
        $change_value = bc_sub($close, $open);
        $change = bc_mul(bc_div($change_value, $open), 100, 2);
        return $change;
    }

    //心跳响应
    protected function onPong($con, $data)
    {
        
        //echo '收到心跳包,PING:' . $data->ping . PHP_EOL;
        $send_data = [
            'pong' => $data->ping,
        ];
        $send_data = json_encode($send_data);
        $con->send($send_data);
        //echo '已进行心跳响应' . PHP_EOL;
    }

    public function ping($con)
    {
       
        $ping = time();
        //echo '进程' . $this->worker_id . '发送ping服务器数据包,ping值:' . $ping . PHP_EOL;
        $send_data = json_encode([
            'ping' => $ping,
        ]);
        $con->send($send_data);
        // $this->pingTimer = Timer::add($this->server_time_out, function () use ($con) {
        //     $msg = '进程' . $this->worker_id . '服务器响应超时,连接关闭' . PHP_EOL;
        //     echo $msg;
        //     $this->close($msg);
        // }, [], false);
    }

    protected function onPing($con, $data)
    {
       
        $this->pingTimer && Timer::del($this->pingTimer);
        $this->pingTimer = null;
        //echo '进程' . $this->worker_id . '服务器正常响应中,pong:' . $data->pong. PHP_EOL;
    }
}
