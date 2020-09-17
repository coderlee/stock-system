<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Utils\RPC;
use App\Http\Requests;
use App\Currency;
use App\Ltc;
use App\LtcBuy;
use App\TransactionComplete;
use App\NewsCategory;
use App\Address;
use App\AccountLog;
use App\Setting;
use App\Users;
use App\UsersWallet;
use App\UsersWalletOut;
use App\WalletLog;
use App\Levertolegal;
use App\LeverTransaction;
use App\RechargeOrder;


class WalletController extends Controller
{
    //我的资产
    public function walletList(Request $request)
    {
        $currency_name = $request->input('currency_name', '');
//        var_dump($currency_name);die;
        $user_id = Users::getUserId();
        if (empty($user_id)) {
            return $this->error('参数错误');
        }
        $legal_wallet['balance'] = UsersWallet::where('user_id', $user_id)
            ->whereHas('currency', function ($query) use ($currency_name) {
                empty($currency_name) || $query->where('name', 'like', '%' . $currency_name . '%');
                //$query->where("is_legal", 1)->where('show_legal', 1);
                $query->where("is_legal", 1);
            })->get(['id', 'currency', 'legal_balance', 'lock_legal_balance'])
            ->toArray();
        $legal_wallet['totle'] = 0;
        foreach ($legal_wallet['balance'] as $k => $v) {
            $num = $v['legal_balance'] + $v['lock_legal_balance'];
            $legal_wallet['totle'] += $num * $v['cny_price'];
        }
        // $legal_wallet['totle'] = 0.10011000;
        $legal_wallet['CNY'] = '';
        $change_wallet['balance'] = UsersWallet::where('user_id', $user_id)
            ->whereHas('currency', function ($query) use ($currency_name) {
                empty($currency_name) || $query->where('name', 'like', '%' . $currency_name . '%');
            })->get(['id', 'currency', 'change_balance', 'lock_change_balance'])
            ->toArray();
        $change_wallet['totle'] = 0;
        foreach ($change_wallet['balance'] as $k => $v) {
            $num = $v['change_balance'] + $v['lock_change_balance'];
            $change_wallet['totle'] += $num * $v['cny_price'];
        }
        // $legal_wallet['totle'] = 0.10011000;
        $change_wallet['CNY'] = '';
        $lever_wallet['balance'] = UsersWallet::where('user_id', $user_id)
            ->whereHas('currency', function ($query) use ($currency_name) {
                empty($currency_name) || $query->where('name', 'like', '%' . $currency_name . '%');
                $query->where("is_lever", 1);
            })->get(['id', 'currency', 'lever_balance', 'lock_lever_balance'])->toArray();
        $lever_wallet['totle'] = 0;
        foreach ($lever_wallet['balance'] as $k => $v) {
            $num = $v['lever_balance'] + $v['lock_lever_balance'];
            $lever_wallet['totle'] += $num * $v['cny_price'];
        }
        // $legal_wallet['totle'] = 0.10011000;
        $lever_wallet['CNY'] = '';
        $ExRate = Setting::getValueByKey('ExRate', 6.5);
        // $lever_wallet['totle'] = 0;

        //读取是否开启充提币

        $is_open_CTbi = Setting::where("key", "=", "is_open_CTbi")->first()->value;


        return $this->success(['legal_wallet' => $legal_wallet, 'change_wallet' => $change_wallet, 'lever_wallet' => $lever_wallet, 'ExRate' => $ExRate, "is_open_CTbi" => $is_open_CTbi]);
    }

    public function insertorder()
    {
        $currency = Input::get("currency", '');
        $type = Input::get("type", "");
        $money = Input::get("money", "");
        $money = round($money, 2); //10.46
        $user_id = Users::getUserId();
        //$user = Users::where('id', $user_id)->first();
        $user = Users::find($user_id);
        $account_number = $user["account_number"];
        $user_wallet = UsersWallet::where('currency', $currency)->first();
        $user_wallet_id = $user_wallet["id"];


        $recharge_order = new RechargeOrder();
        $out_trade_no = RechargeOrder::create_order_sn();
        $recharge_order->user_id = $user_id;
        $recharge_order->account_number = $account_number;
        $recharge_order->currency_id = $currency;
        $recharge_order->currency = "USDT";
        $recharge_order->type = 5;
        $recharge_order->user_wallet_id = $user_wallet_id;
        $recharge_order->money = $money;
        $recharge_order->beginTime = time();
        $recharge_order->out_trade_no = $out_trade_no;

        $recharge_order->save();

        $data['out_trade_no'] = $out_trade_no;
        $data['money'] = $money;
        $data['account_number'] = $account_number;


        return $this->success($data);
    }

    //币种列表
    public function currencyList()
    {
        $user_id = Users::getUserId();
        $currency = Currency::where('is_display', 1)->orderBy('sort', 'asc')->get()->toArray();
        if (empty($currency)) {
            return $this->error("暂时还没有添加币种");
        }
        foreach ($currency as $k => $c) {
            $w = Address::where("user_id", $user_id)->where("currency", $c['id'])->count();
            $currency[$k]['has_address_num'] = $w;//已添加提币地址数量
        }
//        var_dump($currency);die;
        return $this->success($currency);
    }

    //添加提币地址
    public function addAddress()
    {
        $user_id = Users::getUserId();
        $id = Input::get("currency_id", '');
        $address = Input::get("address", "");
        $notes = Input::get("notes", "");
        if (empty($user_id) || empty($id) || empty($address)) {
            return $this->error("参数错误");
        }
        $user = Users::find($user_id);
        if (empty($user)) {
            return $this->error("用户未找到");
        }
        $currency = Currency::find($id);
        if (empty($currency)) {
            return $this->error("此币种不存在");
        }
        $has = Address::where("user_id", $user_id)->where("currency", $id)->where('address', $address)->first();
        if ($has) {
            return $this->error("已经有此提币地址");
        }
        try {
            $currency_address = new Address();
            $currency_address->address = $address;
            $currency_address->notes = $notes;
            $currency_address->user_id = $user_id;
            $currency_address->currency = $id;
            $currency_address->save();
            return $this->success("添加提币地址成功");
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }

    //删除提币地址
    public function addressDel()
    {
        $user_id = Users::getUserId();
        $address_id = Input::get("address_id", '');

        if (empty($user_id) || empty($address_id)) {
            return $this->error("参数错误");
        }
        $user = Users::find($user_id);
        if (empty($user)) {
            return $this->error("用户未找到");
        }
        $address = Address::find($address_id);

        if (empty($address)) {
            return $this->error("此提币地址不存在");
        }
        if ($address->user_id != $user_id) {
            return $this->error("您没有权限删除此地址");
        }

        try {
            $address->delete();
            return $this->success("删除提币地址成功");
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }

//    /**
//     *法币账户划转到交易账户
//     *划转 法币账户只能划转到交易账户  杠杆账户只能和交易账户划转
//     *划转类型type 1 法币划给交易 2 交易划给法币 3 交易划给杠杆 4杠杆划给交易
//     *记录日志
//     */
//    public function changeWallet()// (元逻辑备份)
//    {
//        $user_id = Users::getUserId();
//        $currency_id = Input::get("currency_id", '');
//        // $currency_name = Currency::find($currency_id)->name;
//        $number = Input::get("number", '');
//        $type = Input::get("type", '');//1从法币划到交易账号
//        if (empty($currency_id) || empty($number) || empty($type)) {
//            return $this->error('参数错误');
//        }
//        if ($number < 0) {
//            return $this->error('输入的金额不能为负数');
//        }
//        $user_wallet = UsersWallet::where('user_id', $user_id)->where('currency', $currency_id)->first();
//
//        if ($type == 1) {
//            if ($user_wallet->legal_balance < $number) {
//                return $this->error('余额不足');
//            }
//            $data_wallet1 = [
//                'balance_type' => 1,
//                'wallet_id' => $user_wallet->id,
//                'lock_type' => 0,
//                'create_time' => time(),
//                'before' => $user_wallet->legal_balance,
//                'change' => -$number,
//                'after' => bc_sub($user_wallet->legal_balance, $number, 5),
//            ];
//            $data_wallet2 = [
//                'balance_type' => 2,
//                'wallet_id' => $user_wallet->id,
//                'lock_type' => 0,
//                'create_time' => time(),
//                'before' => $user_wallet->change_balance,
//                'change' => $number,
//                'after' => bc_add($user_wallet->change_balance, $number, 5),
//            ];
//            $user_wallet->legal_balance = $user_wallet->legal_balance - $number;
//            $user_wallet->change_balance = $user_wallet->change_balance + $number;
//            DB::beginTransaction();
//            try {
//                $user_wallet->save();
//                AccountLog::insertLog([
//                    'user_id' => $user_id,
//                    'value' => -$number,
//                    'currency' => $currency_id,
//                    'info' => AccountLog::getTypeInfo(AccountLog::WALLET_LEGAL_OUT),
//                    'type' => AccountLog::WALLET_LEGAL_OUT,
//                ], $data_wallet1);
//                AccountLog::insertLog([
//                    'user_id' => $user_id,
//                    'value' => $number,
//                    'currency' => $currency_id,
//                    'info' => AccountLog::getTypeInfo(AccountLog::WALLET_CHANGE_IN),
//                    'type' => AccountLog::WALLET_CHANGE_IN,
//                ], $data_wallet2);
//                DB::commit();
//                return $this->success('划转成功');
//            } catch (\Exception $ex) {
//                DB::rollBack();
//                return $this->error($ex->getMessage());
//            }
//        } elseif ($type == 2) {
//            if ($user_wallet->change_balance < $number) {
//                return $this->error('余额不足');
//            }
//            $data_wallet1 = [
//                'balance_type' => 2,
//                'wallet_id' => $user_wallet->id,
//                'lock_type' => 0,
//                'create_time' => time(),
//                'before' => $user_wallet->change_balance,
//                'change' => -$number,
//                'after' => bc_sub($user_wallet->change_balance, $number, 5),
//            ];
//            $data_wallet2 = [
//                'balance_type' => 1,
//                'wallet_id' => $user_wallet->id,
//                'lock_type' => 0,
//                'create_time' => time(),
//                'before' => $user_wallet->legal_balance,
//                'change' => $number,
//                'after' => bc_add($user_wallet->legal_balance, $number, 5),
//            ];
//            $user_wallet->change_balance = $user_wallet->change_balance - $number;
//            $user_wallet->legal_balance = $user_wallet->legal_balance + $number;
//            DB::beginTransaction();
//            try {
//                $user_wallet->save();
//                AccountLog::insertLog([
//                    'user_id' => $user_id,
//                    'value' => $number,
//                    'currency' => $currency_id,
//                    'info' => AccountLog::getTypeInfo(AccountLog::WALLET_CHANGE_IN),
//                    'type' => AccountLog::WALLET_LEGAL_IN,
//                ], $data_wallet2);
//                AccountLog::insertLog([
//                    'user_id' => $user_id,
//                    'value' => -$number,
//                    'currency' => $currency_id,
//                    'info' => AccountLog::getTypeInfo(AccountLog::WALLET_CHANGE_OUT),
//                    'type' => AccountLog::WALLET_CHANGE_OUT,
//                ], $data_wallet1);
//                DB::commit();
//                return $this->success('划转成功');
//            } catch (\Exception $ex) {
//                DB::rollBack();
//                return $this->error($ex->getMessage());
//            }
//        } elseif ($type == 3) {
//            if ($user_wallet->change_balance < $number) {
//                return $this->error('余额不足');
//            }
//            $data_wallet1 = [
//                'balance_type' => 2,
//                'wallet_id' => $user_wallet->id,
//                'lock_type' => 0,
//                'create_time' => time(),
//                'before' => $user_wallet->change_balance,
//                'change' => -$number,
//                'after' => bc_sub($user_wallet->change_balance, $number, 5),
//            ];
//            $data_wallet2 = [
//                'balance_type' => 3,
//                'wallet_id' => $user_wallet->id,
//                'lock_type' => 0,
//                'create_time' => time(),
//                'before' => $user_wallet->lever_balance,
//                'change' => $number,
//                'after' => bc_add($user_wallet->lever_balance, $number, 5),
//            ];
//            $user_wallet->change_balance = $user_wallet->change_balance - $number;
//            $user_wallet->lever_balance = $user_wallet->lever_balance + $number;
//            DB::beginTransaction();
//            try {
//                $user_wallet->save();
//                AccountLog::insertLog([
//                    'user_id' => $user_id,
//                    'value' => -$number,
//                    'currency' => $currency_id,
//                    'info' => AccountLog::getTypeInfo(AccountLog::WALLET_CHANGE_LEVEL_OUT),
//                    'type' => AccountLog::WALLET_CHANGE_LEVEL_OUT,
//                ], $data_wallet1);
//                AccountLog::insertLog([
//                    'user_id' => $user_id,
//                    'value' => $number,
//                    'currency' => $currency_id,
//                    'info' => AccountLog::getTypeInfo(AccountLog::WALLET_LEVEL_IN),
//                    'type' => AccountLog::WALLET_LEVEL_IN,
//                ], $data_wallet2);
//                DB::commit();
//                return $this->success('划转成功');
//            } catch (\Exception $ex) {
//                DB::rollBack();
//                return $this->error($ex->getMessage());
//            }
//        } elseif ($type == 4) {
//            if ($user_wallet->lever_balance < $number) {
//                return $this->error('余额不足');
//            }
//            $data_wallet1 = [
//                'balance_type' => 3,
//                'wallet_id' => $user_wallet->id,
//                'lock_type' => 0,
//                'create_time' => time(),
//                'before' => $user_wallet->lever_balance,
//                'change' => -$number,
//                'after' => bc_sub($user_wallet->lever_balance, $number, 5),
//            ];
//            $data_wallet2 = [
//                'balance_type' => 2,
//                'wallet_id' => $user_wallet->id,
//                'lock_type' => 0,
//                'create_time' => time(),
//                'before' => $user_wallet->change_balance,
//                'change' => $number,
//                'after' => bc_add($user_wallet->change_balance, $number, 5),
//            ];
//            $user_wallet->change_balance = $user_wallet->change_balance + $number;
//            $user_wallet->lever_balance = $user_wallet->lever_balance - $number;
//            DB::beginTransaction();
//            try {
//                $user_wallet->save();
//                AccountLog::insertLog([
//                    'user_id' => $user_id,
//                    'value' => $number,
//                    'currency' => $currency_id,
//                    'info' => AccountLog::getTypeInfo(AccountLog::WALLET_CHANGE_LEVEL_IN),
//                    'type' => AccountLog::WALLET_CHANGE_LEVEL_IN,
//                ], $data_wallet2);
//                AccountLog::insertLog([
//                    'user_id' => $user_id,
//                    'value' => -$number,
//                    'currency' => $currency_id,
//                    'info' => AccountLog::getTypeInfo(AccountLog::WALLET_LEVEL_OUT),
//                    'type' => AccountLog::WALLET_LEVEL_OUT,
//                ], $data_wallet1);
//                DB::commit();
//                return $this->success('划转成功');
//            } catch (\Exception $ex) {
//                DB::rollBack();
//                return $this->error($ex->getMessage());
//            }
//        } else {
//            return $this->error('类型错误');
//        }
//    }

    public function chargeReq()
    {
        $user_id = Users::getUserId();
        $currency_id = Input::get("currency", '');
        $number = Input::get("account", '');
        $amount = Input::get("amount", 0);
        if (empty($currency_id) || empty($number) || empty($amount)) {
            return $this->error('参数错误');
        }
        $currency = Db::table('currency')->where('id', $currency_id)->first();
        if (!$currency) {
//            if(!$currency || !$currency->charge_address) {
            return $this->error('参数错误');
        }

        $data = [
            'uid' => $user_id,
            'currency_id' => $currency_id,
            'amount' => $amount,
            'user_account' => $number,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
        Db::table('charge_req')->insert($data);
        return $this->success('申请成功');
    }

    /**
     *法币账户划转到交易账户
     *划转 法币账户只能划转到交易账户  杠杆账户只能和交易账户划转
     *划转类型type 1 法币(c2c)划给杠杆币 2 杠杆划给法币 3 交易划给杠杆 4杠杆划给交易
     *记录日志
     */
    public function changeWallet()  //BY tian
    {
        $user_id = Users::getUserId();
        $currency_id = Input::get("currency_id", '');
        // $currency_name = Currency::find($currency_id)->name;
        $number = Input::get("number", '');
        $type = Input::get("type", '');//1从法币划到交易账号
//        var_dump($type);die;
        if (empty($currency_id) || empty($number) || empty($type)) {
            return $this->error('参数错误');
        }
        if ($number < 0) {
            return $this->error('输入的金额不能为负数');
        }
        $user_wallet = UsersWallet::where('user_id', $user_id)->where('currency', $currency_id)->first();

        if ($type == 2)//1 法币(c2c)划给杠杆币
        {
            if ($user_wallet->legal_balance < $number) {
                return $this->error('余额不足');
            }
            $data_wallet1 = [
                'balance_type' => 1,//余额类型:1.法币,2.币币,3.杆杠
                'wallet_id' => $user_wallet->id,
                'lock_type' => 0,
                'create_time' => time(),
                'before' => $user_wallet->legal_balance,
                'change' => -$number,
                'after' => bc_sub($user_wallet->legal_balance, $number, 5),
            ];
            $data_wallet2 = [
                'balance_type' => 3,////余额类型:1.法币,2.币币,3.杆杠(lever_balance)
                'wallet_id' => $user_wallet->id,
                'lock_type' => 0,
                'create_time' => time(),
                'before' => $user_wallet->lever_balance,
                'change' => $number,
                'after' => bc_add($user_wallet->lever_balance, $number, 5),
            ];
            $user_wallet->legal_balance = $user_wallet->legal_balance - $number;
            $user_wallet->lever_balance = $user_wallet->lever_balance + $number;
            DB::beginTransaction();
            try {
                $user_wallet->save();
                AccountLog::insertLog([
                    'user_id' => $user_id,
                    'value' => -$number,
                    'currency' => $currency_id,
                    'info' => AccountLog::getTypeInfo(AccountLog::WALLET_LEGAL_LEVEL_OUT),
                    'type' => AccountLog::WALLET_LEGAL_LEVEL_OUT,
                ], $data_wallet1);
                AccountLog::insertLog([
                    'user_id' => $user_id,
                    'value' => $number,
                    'currency' => $currency_id,
                    'info' => AccountLog::getTypeInfo(AccountLog::WALLET_LEGAL_LEVEL_IN),
                    'type' => AccountLog::WALLET_LEGAL_LEVEL_IN,
                ], $data_wallet2);
                DB::commit();

                //记录本次划转记录
                $res11 = new Levertolegal();
                $res11->user_id = $user_id;
                $res11->number = $number;
                $res11->type = $type;
                $res11->status = 2; //2：审核通过
                $res11->add_time = time();
                $res11->save();


                return $this->success('划转成功');
            } catch (\Exception $ex) {
                DB::rollBack();
                return $this->error($ex->getMessage());
            }
        } elseif ($type == 1)// 杠杆转给法币(c2c)  //逻辑备份
        {
            $exist_close_trade = LeverTransaction::where('user_id', $user_id)
                ->whereNotIn('status', [LeverTransaction::CLOSED, LeverTransaction::CANCEL])
                ->count();
            if ($exist_close_trade > 0) {
                return $this->error('操作失败:您有未平仓的交易,操作禁止');
            }
            if ($user_wallet->lever_balance < $number) {
                return $this->error('余额不足');
            }
            $user_wallet->lever_balance = $user_wallet->lever_balance - $number;
            //冻结余额
            $user_wallet->lock_lever_balance = $user_wallet->lock_lever_balance + $number;
            DB::beginTransaction();
            try {
                $user_wallet->save();
                AccountLog::newinsertLog([
                    'user_id' => $user_id,
                    'value' => $number,
                    'currency' => $currency_id,
                    'info' => AccountLog::getTypeInfo(AccountLog::WALLET_DONGJIEGANGGAN),
                    'type' => AccountLog::WALLET_DONGJIEGANGGAN,
                ]);
                DB::commit();
                //记录本次划转记录
                $res11 = new Levertolegal();
                $res11->user_id = $user_id;
                $res11->number = $number;
                $res11->type = $type;
                $res11->add_time = time();
                $res11->save();
                return $this->success('划转成功,等待审核');
            } catch (\Exception $ex) {
                DB::rollBack();
                return $this->error($ex->getMessage());
            }
        } elseif ($type == 3) {
            if ($user_wallet->change_balance < $number) {
                return $this->error('余额不足');
            }
            $data_wallet1 = [
                'balance_type' => 2,
                'wallet_id' => $user_wallet->id,
                'lock_type' => 0,
                'create_time' => time(),
                'before' => $user_wallet->change_balance,
                'change' => -$number,
                'after' => bc_sub($user_wallet->change_balance, $number, 5),
            ];
            $data_wallet2 = [
                'balance_type' => 3,
                'wallet_id' => $user_wallet->id,
                'lock_type' => 0,
                'create_time' => time(),
                'before' => $user_wallet->lever_balance,
                'change' => $number,
                'after' => bc_add($user_wallet->lever_balance, $number, 5),
            ];
            $user_wallet->change_balance = $user_wallet->change_balance - $number;
            $user_wallet->lever_balance = $user_wallet->lever_balance + $number;
            DB::beginTransaction();
            try {
                $user_wallet->save();
                AccountLog::insertLog([
                    'user_id' => $user_id,
                    'value' => -$number,
                    'currency' => $currency_id,
                    'info' => AccountLog::getTypeInfo(AccountLog::WALLET_CHANGE_LEVEL_OUT),
                    'type' => AccountLog::WALLET_CHANGE_LEVEL_OUT,
                ], $data_wallet1);
                AccountLog::insertLog([
                    'user_id' => $user_id,
                    'value' => $number,
                    'currency' => $currency_id,
                    'info' => AccountLog::getTypeInfo(AccountLog::WALLET_LEVEL_IN),
                    'type' => AccountLog::WALLET_LEVEL_IN,
                ], $data_wallet2);
                DB::commit();
                return $this->success('划转成功');
            } catch (\Exception $ex) {
                DB::rollBack();
                return $this->error($ex->getMessage());
            }
        } elseif ($type == 4) {
            if ($user_wallet->lever_balance < $number) {
                return $this->error('余额不足');
            }
            $data_wallet1 = [
                'balance_type' => 3,
                'wallet_id' => $user_wallet->id,
                'lock_type' => 0,
                'create_time' => time(),
                'before' => $user_wallet->lever_balance,
                'change' => -$number,
                'after' => bc_sub($user_wallet->lever_balance, $number, 5),
            ];
            $data_wallet2 = [
                'balance_type' => 2,
                'wallet_id' => $user_wallet->id,
                'lock_type' => 0,
                'create_time' => time(),
                'before' => $user_wallet->change_balance,
                'change' => $number,
                'after' => bc_add($user_wallet->change_balance, $number, 5),
            ];
            $user_wallet->change_balance = $user_wallet->change_balance + $number;
            $user_wallet->lever_balance = $user_wallet->lever_balance - $number;
            DB::beginTransaction();
            try {
                $user_wallet->save();
                AccountLog::insertLog([
                    'user_id' => $user_id,
                    'value' => $number,
                    'currency' => $currency_id,
                    'info' => AccountLog::getTypeInfo(AccountLog::WALLET_CHANGE_LEVEL_IN),
                    'type' => AccountLog::WALLET_CHANGE_LEVEL_IN,
                ], $data_wallet2);
                AccountLog::insertLog([
                    'user_id' => $user_id,
                    'value' => -$number,
                    'currency' => $currency_id,
                    'info' => AccountLog::getTypeInfo(AccountLog::WALLET_LEVEL_OUT),
                    'type' => AccountLog::WALLET_LEVEL_OUT,
                ], $data_wallet1);
                DB::commit();
                return $this->success('划转成功');
            } catch (\Exception $ex) {
                DB::rollBack();
                return $this->error($ex->getMessage());
            }
        } else {
            return $this->error('类型错误');
        }
    }


    public function hzhistory()
    {
        $user_id = Users::getUserId();
        $result = new Levertolegal();
        $count = $result::all()->count();
        $result = $result->orderBy("add_time", "desc")->where("user_id", "=", $user_id)->get()->toArray();
        foreach ($result as $key => $value) {
            $result[$key]["add_time"] = date("Y-m-d H:i:s", $value["add_time"]);
            if ($value["type"] == 1) {
                $result[$key]["type"] = "杠杆转法币";
            } elseif ($value["type"] == 2) {
                $result[$key]["type"] = "法币转杠杆";
            }

        }
//        var_dump($result);die;

        return response()->json(['type' => "ok", 'data' => $result, 'count' => $count]);
    }



    //↓↓↓↓↓↓下边是提币的一些接口//app只有交易账户可以提币
    //渲染提币时的页面，最小交易额，手续费,可用余额
    public function getCurrencyInfo()
    {
        $user_id = Users::getUserId();
        $currency_id = Input::get("currency", '');
        if (empty($currency_id)) return $this->error('参数错误');
        $currencyInfo = Currency::find($currency_id);
        if (empty($currencyInfo)) return $this->error('币种不存在');
        $wallet = UsersWallet::where('user_id', $user_id)->where('currency', $currency_id)->first();
        $data = [
            'rate' => $currencyInfo->rate,
            'min_number' => $currencyInfo->min_number,
            'name' => $currencyInfo->name,
            'change_balance' => $wallet->lever_balance,
        ];
        return $this->success($data);
    }

    //提币地址，根据currency_id列表地址,提币的时候需要选择地址
    public function getAddressByCurrency()
    {
        $user_id = Users::getUserId();
        $currency_id = Input::get("currency", '');
        if (empty($user_id) || empty($currency_id)) {
            return $this->error('参数错误');
        }
        $address = Address::where('user_id', $user_id)->where('currency', $currency_id)->get()->toArray();
        if (empty($address)) {
            return $this->error('您还没有添加提币地址');
        }
        return $this->success($address);
    }

    //提交提币信息。数量。
    public function postWalletOut()
    {
        $user_id = Users::getUserId();
        $currency_id = Input::get("currency", '');
        $number = Input::get("number", '');
        $rate = Input::get("rate", '');
        $address = Input::get("address", '');
        if (empty($currency_id) || empty($currency_id) || empty($address)) {
            return $this->error('参数错误');
        }
        if ($number < 0) {
            return $this->error('输入的金额不能为负数');
        }
        $currencyInfo = Currency::find($currency_id);
        $wallet = UsersWallet::where('user_id', $user_id)->where('currency', $currency_id)->first();
        if ($number < $currencyInfo->min_number) {
            return $this->error('数量不能少于最小值');
        }
        if ($number > $wallet->lever_balance) {
            return $this->error('余额不足');
        }
        DB::beginTransaction();
        $walletOut = new UsersWalletOut();
        try {
            $walletOut->user_id = $user_id;
            $walletOut->currency = $currency_id;
            $walletOut->number = $number;
            $walletOut->address = $address;
            $walletOut->rate = $rate;
            $walletOut->real_number = $number * (1 - $rate / 100);
            $walletOut->create_time = time();
            $walletOut->status = 1;//1提交提币2已经提币3失败
            $walletOut->save();
            //冻结余额
            $wallet->lever_balance = $wallet->lever_balance - $number;
            $wallet->lock_lever_balance = $wallet->lock_lever_balance + $number;
            $wallet->save();
            AccountLog::insertLog([
                'user_id' => $user_id,
                'value' => $number,
                'currency' => $currency_id,
                'info' => '申请提币',
                'type' => AccountLog::WALLETOUT,
            ]);
            DB::commit();
            return $this->success('提币申请已成功，等待审核');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage());
        }

    }

    //充币地址
    public function getWalletAddressIn()
    {
        $user_id = Users::getUserId();
        $currency_id = Input::get("currency", '');
        if (empty($user_id) || empty($currency_id)) {
            return $this->error('参数错误');
        }
        $wallet = UsersWallet::where('user_id', $user_id)->where('currency', $currency_id)->first();
        if (empty($wallet)) {
            return $this->error('钱包不存在');
        }
        return $this->success($wallet->address);
    }

    //余额页面详情
    public function getWalletDetail()
    {
        // return $this->error('参数错误');
        $user_id = Users::getUserId();
        $currency_id = Input::get("currency", '');
        //
        $type = Input::get("type", '');
        //exit($type);
        if (empty($user_id) || empty($currency_id)) {
            return $this->error('参数错误');
        }
        $ExRate = Setting::getValueByKey('ExRate', 6.5);
        // $userWallet = new UsersWallet();
        // return $this->error('参数错误');
        // $wallet = $userWallet->where('user_id', $user_id)->where('currency', $currency_id);
        if ($type == 'legal') {
            $wallet = UsersWallet::where('user_id', $user_id)->where('currency', $currency_id)->first(['id', 'currency', 'legal_balance', 'lock_legal_balance']);
        } else if ($type == 'change') {
            $wallet = UsersWallet::where('user_id', $user_id)->where('currency', $currency_id)->first(['id', 'currency', 'change_balance', 'lock_change_balance']);
        } else if ($type == 'lever') {
            $wallet = UsersWallet::where('user_id', $user_id)->where('currency', $currency_id)->first(['id', 'currency', 'lever_balance', 'lock_lever_balance']);
        } else {
            return $this->error('类型错误');
        }
        if (empty($wallet)) return $this->error("钱包未找到");
        // print_r($wallet);

        $wallet->ExRate = $ExRate;
        // print_r($wallet);
        return $this->success($wallet);
    }

    public function legalLog(Request $request)
    {
        // $user_id = Users::getUserId();
        // $limit = Input::get('limit', 10);
        // $page = Input::get('page', 1);
        // $currency = Input::get("currency", '');
        // $type = Input::get("type", '');
        // if (empty($user_id) || empty($currency)|| empty($type)) {
        //     return $this->error('参数错误');
        // }
        // $log = WalletLog::whereHas('UsersWallet', function ($query) use ($user_id,$currency) {
        //          $query->where('user_id',  $user_id );
        //          $query->where('currency',  $currency );

        //     })->where('balance_type', $type)
        //     ->orderBy('id', 'desc')
        //     ->paginate($limit, ['*'], 'page', $page);               
        // if (empty($log)) return $this->error('您还没有交易记录');
        // return $this->success(array(
        //     "list" => $log->items(), 'count' => $log->total(),
        //     "page" => $page, "limit" => $limit
        // ));
        $limit = $request->get('limit', 10);
        $account = $request->get('account', '');
        // $start_time = strtotime($request->get('start_time',0));
        // $end_time = strtotime($request->get('end_time',0));
        $currency = $request->get('currency', 0);
        // $type= $request->get('type',0);
        $user_id = Users::getUserId();
        $list = New AccountLog();
        if (!empty($currency)) {
            $list = $list->where('currency', $currency);
        }
        if (!empty($user_id)) {
            $list = $list->where('user_id', $user_id);
        }
        // if (!empty($type)) {
        //      $list = $list->where('type',$type);
        // }
        // if(!empty($start_time)){
        //     $list = $list->where('created_time','>=',$start_time);
        // }
        // if(!empty($end_time)){
        //     $list = $list->where('created_time','<=',$end_time);
        // }
        // if (!empty($account)) {
        //    $list = $list->whereHas('user', function($query) use ($account) {
        //     $query->where("phone",'like','%'.$account.'%')->orwhere('email','like','%'.$account.'%');
        //      } );
        // }
        $list = $list->orderBy('id', 'desc')->paginate($limit);
        //dd($list->items());
        // return response()->json(['code' => 0, 'data' => $list->items(), 'count' => $list->total()]);


        //读取是否开启充提币

        $is_open_CTbi = Setting::where("key", "=", "is_open_CTbi")->first()->value;
//        var_dump($is_open_CTbi);die;

        return $this->success(array(
            "list" => $list->items(), 'count' => $list->total(),
            "limit" => $limit,
            "is_open_CTbi" => $is_open_CTbi
        ));
    }

    //提币记录
    public function walletOutLog()
    {
        $id = Input::get("id", '');
        $walletOut = UsersWalletOut::find($id);
        return $this->success($walletOut);
    }


    //接收来自钱包的PB
    public function getLtcKMB()
    {
        $address = Input::get('address', '');
        $money = Input::get('money', '');
        // $key = Input::get('key', '');
        // if(md5(time())!=$key){
        //     return $this->error('系统错误');
        // }
        $wallet = UsersWallet::whereHas('currency', function ($query) {
            $query->where('name', 'PB');
        })->where('address', $address)->first();
        if (empty($wallet)) {
            return $this->error('钱包不存在');
        }
        DB::beginTransaction();
        try {

            $data_wallet1 = array(
                'balance_type' => 1,
                'wallet_id' => $wallet->id,
                'lock_type' => 0,
                'create_time' => time(),
                'before' => $wallet->change_balance,
                'change' => $money,
                'after' => $wallet->change_balance + $money,
            );
            $wallet->change_balance = $wallet->change_balance + $money;
            $wallet->save();
            AccountLog::insertLog([
                'user_id' => $wallet->user_id,
                'value' => $money,
                'currency' => $wallet->currency,
                'info' => '转账来自钱包的余额',
                'type' => AccountLog::LTC_IN,
            ], $data_wallet1);
            DB::commit();
            return $this->success('转账成功');
        } catch (\Exception $rex) {
            DB::rollBack();
            return $this->error($rex);
        }

    }

    public function sendLtcKMB()
    {
        $user_id = Users::getUserId();
        $account_number = Input::get('account_number', '');
        $money = Input::get('money', '');
//        var_dump($account_number);var_dump($user_id);die;
        // $key = md5(time());
        if (empty($account_number) || empty($money) || $money < 0) {
            return $this->error('参数错误');
        }
        $wallet = UsersWallet::whereHas('currency', function ($query) {
            $query->where('name', 'PB');
        })->where('user_id', $user_id)->first();
        if ($wallet->change_balance < $money) {
            return $this->error('余额不足');
        }

        DB::beginTransaction();
        try {

            $data_wallet1 = array(
                'balance_type' => 1,
                'wallet_id' => $wallet->id,
                'lock_type' => 0,
                'create_time' => time(),
                'before' => $wallet->change_balance,
                'change' => $money,
                'after' => $wallet->change_balance - $money,
            );
            $wallet->change_balance = $wallet->change_balance - $money;
            $wallet->save();
            AccountLog::insertLog([
                'user_id' => $wallet->user_id,
                'value' => $money,
                'currency' => $wallet->currency,
                'info' => '转账余额至钱包',
                'type' => AccountLog::LTC_SEND,
            ], $data_wallet1);

            $url = "http://walletapi.bcw.work/api/ltcGet?account_number=" . $account_number . "&money=" . $money;
            $data = RPC::apihttp($url);
            $data = @json_decode($data, true);
//            var_dump($data);die;
            if ($data["type"] != 'ok') {
                DB::rollBack();
                return $this->error($data["message"]);
            }
            DB::commit();
            return $this->success('转账成功');
        } catch (\Exception $rex) {
            DB::rollBack();
            return $this->error($rex->getMessage());
        }


    }

    //获取pb的余额交易余额
    public function PB()
    {
        $user_id = Users::getUserId();
        $wallet = UsersWallet::whereHas('currency', function ($query) {
            $query->where('name', 'PB');
        })->where('user_id', $user_id)->first();
        return $this->success($wallet->change_balance);
    }


}
