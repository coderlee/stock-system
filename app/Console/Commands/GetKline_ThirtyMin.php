<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

// 定义参数
defined('ACCOUNT_ID') or define('ACCOUNT_ID', '50154012'); // 你的账户ID
defined('ACCESS_KEY') or define('ACCESS_KEY', 'c96392eb-b7c57373-f646c2ef-25a14'); // 你的ACCESS_KEY
defined('SECRET_KEY') or define('SECRET_KEY', ''); // 你的SECRET_KEY

class GetKline_ThirtyMin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get_kline_data_thirtymin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '获取K线图数据';

    // 定义参数
    //    const ACCOUNT_ID = 50154012; // 你的账户ID
    //    const ACCESS_KEY = 'c96392eb-b7c57373-f646c2ef-25a14'; // 你的ACCESS_KEY
    //    const SECRET_KEY = ''; // 你的SECRET_KEY

    private $url = 'https://api.huobi.br.com'; //'https://api.huobi.pro';
    private $api = '';
    public $api_method = '';
    public $req_method = '';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        while (true) {
            try {
                //code...

                $all = DB::table('currency')->where('is_display', '1')->get();
                $all_arr = $this->object2array($all);
                $legal = DB::table('currency')->where('is_display', '1')->where('is_legal', '1')->get();
                $legal_arr = $this->object2array($legal);
                //拼接所有的交易对
                $ar = [];
                foreach ($legal_arr as $legal) {
                    foreach ($all_arr as $item) {
                        if ($legal['id'] != $item['id']) {
                            $ar_a = [];
                            $ar_a['name'] = strtolower($item['name']) . strtolower($legal['name']);
                            $ar_a['currency_id'] = $item['id'];
                            $ar_a['legal_id'] = $legal['id'];
                            $ar[] = $ar_a;
                        }
                    }
                }

                //此数组来自于上面注释的代码。  为了减少消耗服务器资源，去掉不必要的循坏。  把上面的循环结果写固定，如果有更新，去掉注释即可
                //获取火币交易平台上面的有数据的交易对
                $kko = json_decode($this->curl('https://api.huobi.br.com/v1/common/symbols'), true);
                if ($kko['status'] == 'ok') {

                    $trade = [];
                    foreach ($kko['data'] as $key => $value) {
                        $trade[] = $value['symbol'];
                    }

                    foreach ($ar as $it) {
                        if (in_array($it['name'], $trade)) {
                            $data = array();
                            $data = $this->get_history_kline($it['name'], '30min', 1);
                            if ($data['status'] == 'ok') {
                                $info = $data['data'][0];
                                $insert_Data = array();
                                $insert_Data['currency_id'] = $it['currency_id'];
                                $insert_Data['legal_id'] = $it['legal_id'];
                                $insert_Data['start_price'] = $this->sctonum($info['open']);
                                $insert_Data['end_price'] = $this->sctonum($info['close']);
                                $insert_Data['mminimum'] = $this->sctonum($info['low']);
                                $insert_Data['highest'] = $this->sctonum($info['high']);
                                $insert_Data['type'] = 7;
                                $insert_Data['sign'] = 2;
                                $insert_Data['day_time'] = $info['id'];
                                $insert_Data['period'] = '30min';
                                $insert_Data['number'] = bcmul($info['amount'], 1, 5);
                                $insert_Data['mar_id'] = $info['id'];

                                DB::table('market_hour')->insert($insert_Data);
                            }
                        }
                    }
                }
                sleep(60);
            } catch (Exception $e) {
                continue;
            }
        }
    }

    /**对象转数组
     * @param $obj
     * @return mixed
     */
    public function object2array($obj)
    {
        return json_decode(json_encode($obj), true);
    }
    //科学计算发转字符串
    public function sctonum($num, $double = 8)
    {
        if (false !== stripos($num, "e")) {
            $a = explode("e", strtolower($num));
            return bcmul($a[0], bcpow(10, $a[1], $double), $double);
        } else {
            return $num;
        }
    }

//    /**
    //     * 行情类API
    //     */
    //    // 获取K线数据
    public function get_history_kline($symbol = '', $period = '', $size = 0)
    {
        $this->api_method = "/market/history/kline";
        $this->req_method = 'GET';
        $param = [
            'symbol' => $symbol,
            'period' => $period,
        ];
        if ($size) {
            $param['size'] = $size;
        }

        $url = $this->create_sign_url($param);
        return json_decode($this->curl($url), true);
    }
//    /**
    //     * 类库方法
    //     */
    //    // 生成验签URL
    public function create_sign_url($append_param = [])
    {
        // 验签参数
        $param = [
            'AccessKeyId' => ACCESS_KEY,
            'SignatureMethod' => 'HmacSHA256',
            'SignatureVersion' => 2,
            'Timestamp' => date('Y-m-d\TH:i:s', time()),
        ];
        if ($append_param) {
            foreach ($append_param as $k => $ap) {
                $param[$k] = $ap;
            }
        }
        return $this->url . $this->api_method . '?' . $this->bind_param($param);
    }
//    // 组合参数
    public function bind_param($param)
    {
        $u = [];
        $sort_rank = [];
        foreach ($param as $k => $v) {
            $u[] = $k . "=" . urlencode($v);
            $sort_rank[] = ord($k);
        }
        asort($u);
        $u[] = "Signature=" . urlencode($this->create_sig($u));
        return implode('&', $u);
    }
//    // 生成签名
    public function create_sig($param)
    {
        $sign_param_1 = $this->req_method . "\n" . $this->api . "\n" . $this->api_method . "\n" . implode('&', $param);
        $signature = hash_hmac('sha256', $sign_param_1, SECRET_KEY, true);
        return base64_encode($signature);
    }
    public function curl($url, $postdata = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($this->req_method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
        ]);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return $output;
    }
}
