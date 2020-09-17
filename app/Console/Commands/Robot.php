<?php

namespace App\Console\Commands;

use App\AccountLog;
use App\Transaction;
use App\TransactionIn;
use App\TransactionOut;
use App\Users;
use App\UsersWallet;
use Faker\Factory;
use Illuminate\Console\Command;
use App\Robot as RobotModel;
use Illuminate\Support\Facades\Log;

defined('ACCOUNT_ID') or define('ACCOUNT_ID', '50154012'); // 你的账户ID
defined('ACCESS_KEY') or define('ACCESS_KEY', 'c96392eb-b7c57373-f646c2ef-25a14'); // 你的ACCESS_KEY
defined('SECRET_KEY') or define('SECRET_KEY', ''); // 你的SECRET_KEY

class Robot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'robot {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '匹配交易自动挂单机器人';

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
        $this->info('--------------------------------------------------');
        $this->info('开始执行机器人:' . now()->toDateTimeString());

        $id = $this->argument('id');

        while (true) {
            $robot = RobotModel::find($id);

            if (!$robot) {
                $this->info('找不到此机器人');
                break;
            }

            if ($robot->status == RobotModel::STOP) {
                $this->info('机器人已关闭');
                break;
            }

            $this->info('当前交易对是:' . $robot->currency_info . '/' . $robot->legal_info);
            $this->info('当前数量区间:' . $robot->number_min . '-' . $robot->number_max);

            try {
                if ($robot->sell == RobotModel::OPEN) {
                    $this->info('开始卖出');
                    $this->sell($robot, $robot->number_max, $robot->number_min);
                }
                if ($robot->buy == RobotModel::OPEN) {
                    $this->info('开始买入');
                    $this->buy($robot, $robot->number_max, $robot->number_min);
                }
            } catch (\Exception $e) {
                $this->info($e->getMessage());
            }

            sleep($robot->second);
        }

        $this->info('机器人执行结束:' . now()->toDateTimeString());
        $this->info('--------------------------------------------------');
    }

    protected function sell($robot, $number_max, $number_min)
    {
        //随机数量
        $num          = $this->getNumber($number_min, $number_max);
        $total_number = $num;

        //随机价格
        $price = $this->getPrice(strtolower($robot->currency_info . $robot->legal_info), $robot->float_number_down, $robot->float_number_up);

        $user = Users::find($robot->sell_user_id);

        $in = TransactionIn::where("price", ">=", $price)
            ->where("currency", $robot->currency_id)
            ->where("legal", $robot->legal_id)
            ->where("number", ">", "0")
            ->orderBy('price', 'desc')
            ->get();

        $user_currency = UsersWallet::where("user_id", $robot->sell_user_id)
            ->where("currency", $robot->currency_id)
            ->first();

        $has_num = 0;
        if (!empty($in)) {
            foreach ($in as $i) {
                if ($has_num >= $num) break;

                $shengyu_num = $num - $has_num;
                $this_num    = $i->number > $shengyu_num ? $this_num = $shengyu_num : $this_num = $i->number;
                $has_num     = $has_num + $this_num;

                if ($this_num > 0) {
                    TransactionOut::transaction($i, $this_num, $user, $user_currency, $robot->legal_id, $robot->currency_id);
                }
            }
            Transaction::newDealList($robot->legal_id, $robot->currency_id);
        }

        $num = $num - $has_num;
        if ($num > 0) {
            $out               = new TransactionOut();
            $out->user_id      = $robot->sell_user_id;
            $out->price        = $price;
            $out->number       = $num;
            $out->total_number = $total_number;
            $out->currency     = $robot->currency_id;
            $out->legal        = $robot->legal_id;
            $out->create_time  = time();

            $out->save();

            $user_currency->change_balance      = $user_currency->change_balance - $num;
            $user_currency->lock_change_balance = $user_currency->lock_change_balance + $num;
            $user_currency->save();

            AccountLog::insertLog([
                'user_id' => $robot->sell_user_id,
                'value'   => -$num,
                'info'    => "提交卖出记录扣除",
                'type'    => AccountLog::TRANSACTIONOUT_SUBMIT_REDUCE
            ]);
        }

        Transaction::pushNews($robot->currency_id, $robot->legal_id);
    }

    protected function buy($robot, $number_max, $number_min)
    {
        //随机数量
        $num          = $this->getNumber($number_min, $number_max);
        $total_number = $num;

        //随机价格
        $price = $this->getPrice(strtolower($robot->currency_info.$robot->legal_info), $robot->float_number_down, $robot->float_number_up);

        $user = Users::find($robot->buy_user_id);

        $has_num = 0;

        $user_legal = UsersWallet::where("user_id", $robot->buy_user_id)->where("currency", $robot->legal_id)->first();

        $out = TransactionOut::where("price", "<=", $price)
            ->where("number", ">", "0")
            ->where("currency", $robot->currency_id)
            ->where("legal", $robot->legal_id)
            ->orderBy('price', 'asc')
            ->get();

        if (!empty($out)) {

            foreach ($out as $o) {
                if ($has_num < $num) {
                    $shengyu_num = $num - $has_num;
                    $this_num    = 0;
                    if ($o->number > $shengyu_num) {
                        $this_num = $shengyu_num;
                    } else {
                        $this_num = $o->number;
                    }
                    $has_num = $has_num + $this_num;
                    if ($this_num > 0) {
                        TransactionIn::transaction($o, $this_num, $user, $robot->legal_id, $robot->currency_id);
                    }
                } else {
                    break;
                }
            }
            Transaction::newDealList($robot->legal_id, $robot->currency_id);
        }

        $num = $num - $has_num;

        if ($num > 0) {
            $in               = new TransactionIn();
            $in->user_id      = $robot->buy_user_id;
            $in->price        = $price;
            $in->number       = $num;
            $in->currency     = $robot->currency_id;
            $in->legal        = $robot->legal_id;
            $in->total_number = $total_number;
            $in->create_time  = time();

            $in->save();

            $all_balance                    = $price * $num;
            $user_legal->legal_balance      = $user_legal->legal_balance - $all_balance;
            $user_legal->lock_legal_balance = $user_legal->lock_legal_balance + $all_balance;
            $user_legal->save();

            AccountLog::insertLog([
                'user_id' => $robot->buy_user_id,
                'value'   => -$all_balance,
                'info'    => "提交卖入记录扣除",
                'type'    => AccountLog::TRANSACTIONIN_SUBMIT_REDUCE
            ]);
        }
//        Transaction::pushNews($robot->currency_id, $robot->legal_id);


    }


    /**获取火币行情价格
     *
     * @param $symbol
     * @param $float_number_down
     * @param $float_number_up
     *
     * @return float
     */
    public function getPrice($symbol, $float_number_down, $float_number_up)
    {
        $url   = 'https://api.huobi.br.com/market/trade?symbol=' . $symbol;
        $info  = $this->curl($url);
        $price = $info['tick']['data'][0]['price'];

        $faker = Factory::create();
        $price = $faker->randomFloat(2, $price - $float_number_down, $price + $float_number_up);
        unset($faker);
        return $price;
    }

    /**获取买入卖出随机数
     *
     * @param $number_min
     * @param $number_max
     *
     * @return float
     */
    public function getNumber($number_min, $number_max)
    {
        $faker = Factory::create();
        $num   = $faker->randomFloat(2, $number_min, $number_max);
        unset($faker);
        return $num;
    }

    public function curl($url, $type = 'GET', $postdata = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($type == 'POST') {
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
        $info   = curl_getinfo($ch);
        curl_close($ch);
        return @json_decode($output, true);
    }
}
