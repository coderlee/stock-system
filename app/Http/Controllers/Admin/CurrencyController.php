<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use App\Currency;
use App\CurrencyMatch;
use App\Setting;
use App\UsersWallet;
use App\Users;

class CurrencyController extends Controller
{
    public function index()
    {
        return view('admin.currency.index');
    }

    public function add(Request $request)
    {
        $id = $request->get('id', 0);
        if (empty($id)) {
            $result = new Currency();
        } else {
            $result = Currency::find($id);
        }
        return view('admin.currency.add')->with('result', $result);
    }

    public function postAdd(Request $request)
    {
        $id = $request->get('id', 0);
        $name = $request->get('name', '');
        // $token = $request->get('token','');
        // $get_address = $request->get('get_address','');
        $sort = $request->get('sort', 0);
        $logo = $request->get('logo', '');
        $qr_code = $request->get('qr_code','');
        $charge_address = $request->get('charge_address','');
        $type = $request->get('type', '');
        $is_legal = $request->get('is_legal', '');
        $is_lever = $request->get('is_lever', '');
        $is_match = $request->get('is_match', '');
        $min_number = $request->get('min_number', 0);
        $rate = $request->get('rate', 0);
        $total_account = $request->get('total_account', 0);
        $key = $request->get('key', 0);
        $contract_address = $request->get('contract_address', 0);
        //自定义验证错误信息
        $messages = [
            'required' => ':attribute 为必填字段',
        ];
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'sort' => 'required',
            'type' => 'required',
            'is_legal' => 'required',
            'is_lever' => 'required',

            // 'logo'=>'required',
        ], $messages);

        //如果验证不通过
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $has = Currency::where('name', $name)->first();
        if (empty($id) && !empty($has)) {
            return $this->error($name . ' 已存在');
        }
        if (empty($id)) {
            $currency = new Currency();
            $currency->create_time = time();
        } else {
            $currency = Currency::find($id);
        }
        $currency->name = $name;
        // $acceptor->token = $token;
        // $acceptor->get_address = $get_address;
        $currency->sort = intval($sort);
        $currency->logo = $logo;
        $currency->is_legal = $is_legal;
        $currency->is_lever = $is_lever;
        $currency->is_match = $is_match;
        $currency->min_number = $min_number;
        $currency->rate = $rate;
        $currency->total_account = $total_account;
        $currency->key = $key;
        $currency->contract_address = $contract_address;
        $currency->type = $type;
        $currency->is_display = 1;
        $currency->charge_address = $charge_address;
        $currency->qr_code = $qr_code;
        DB::beginTransaction();
        try {
            $currency->save();//保存币种
            // if(empty($id)){// 如果是添加新币 //没添加一种交易币，就给用户添加一个交易币钱包
            //     $currency_id = Currency::where('name',$name)->first()->id;
            //     $users = Users::all();
            //     foreach ($users as $key => $value) {
            //         $userWallet = new UsersWallet();
            //         $userWallet->user_id = $value->id;
            //         $userWallet->currency = $currency_id;
            //         $userWallet->create_time = time();
            //         $userWallet->save();
            //     }
            // }
            DB::commit();
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->error($exception->getMessage());
        }
    }

    public function lists(Request $request)
    {
        $limit = $request->get('limit', 10);
//        $account_number = $request->get('account_number','');
        $result = new Currency();
        $result = $result->orderBy('sort', 'asc')->orderBy('id', 'desc')->paginate($limit);
        return $this->layuiData($result);
    }

    public function delete(Request $request)
    {
        $id = $request->get('id', 0);
        $acceptor = Currency::find($id);
        if (empty($acceptor)) {
            return $this->error('无此币种');
        }
        try {
            $acceptor->delete();
            return $this->success('删除成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function isDisplay(Request $request)
    {
        $id = $request->get('id', 0);
        $currency = Currency::find($id);
        if (empty($currency)) {
            return $this->error('参数错误');
        }
        if ($currency->is_display == 1) {
            $currency->is_display = 0;
        } else {
            $currency->is_display = 1;
        }
        try {
            $currency->save();
            return $this->success('操作成功');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function executeCurrency(Request $request)
    {
        $id = $request->get('id', 0);
        $is_execute = Setting::getValueByKey('currency_' . $id, 0);
        if ($is_execute == 1) {
            return $this->error('该币上币程序正在后台执行中');
        } elseif ($is_execute == 2) {
            return $this->error('该币已经运行过上币程序');
        } else {
            $path = base_path();
            // $process = new Process('nohup php artisan execute_currency ' . $id . ' >./execute_currency.log 2>&1 &', $path); //第一个参数是运行的命令,命令方式跟 Linux 一致，第二个参数是可以执行此条命令的路径
           //上边那个是交易所的上币逻辑，下边这个是钱包的上币逻辑
            $process = new Process('nohup php artisan make_wallet ' . $id . ' >./execute_currency.log 2>&1 &', $path); //第一个参数是运行的命令,命令方式跟 Linux 一致，第二个参数是可以执行此条命令的路径
            $process->run();
            return $this->success('开始在后台执行上币脚本');
        }
    }

    /**
     * 交易对显示
     *
     * @return void
     */
    public function match()
    {
        return view('admin.currency.match');
    }

    public function matchList(Request $request)
    {
        $legal_id = $request->route('legal_id');
        $limit = $request->input('limit', 10);
        $legal = Currency::find($legal_id);
        $matchs = $legal->quotation()->paginate($limit);
        return $this->layuiData($matchs);
    }

    public function addMatch($legal_id)
    {
        $is_legal = Currency::where('id', $legal_id)->value('is_legal');
        if (!$is_legal) {
            abort(403, '指定币种不是法币,不能添加交易对');
        }
        $currencies = Currency::where('id', '<>', $legal_id)->get();
        $market_from_names = CurrencyMatch::enumMarketFromNames();
        return view('admin.currency.match_add')->with('currencies', $currencies)
            ->with('market_from_names', $market_from_names);
    }

    public function postAddMatch(Request $request, $legal_id)
    {
        $is_legal = Currency::where('id', $legal_id)->value('is_legal');
        if (!$is_legal) {
            return $this->error('指定币种不是法币,不能添加交易对');
        }
        $currency_id = $request->input('currency_id');
        $is_display = $request->input('is_display', 1);
        $market_from = $request->input('market_from', 0);
        $open_transaction = $request->input('open_transaction', 0);
        $open_lever = $request->input('open_lever', 0);
        $lever_share_num = $request->input('lever_share_num', 1);
        $spread = $request->input('spread', 0);
        $overnight = $request->input('overnight', 0);
        $lever_trade_fee = $request->input('lever_trade_fee', 0);
        $lever_min_share = $request->input('lever_min_share', 0);
        $lever_max_share = $request->input('lever_max_share', 0);

        //检测交易对是否已存在
        $exist = CurrencyMatch::where('currency_id', $currency_id)
            ->where('legal_id', $legal_id)
            ->first();
        if ($exist) {
            return $this->error('对应交易对已存在');
        }
        CurrencyMatch::unguard();
        $currency_match = CurrencyMatch::create([
            'legal_id' => $legal_id,
            'currency_id' => $currency_id,
            'is_display' => $is_display,
            'market_from' => $market_from,
            'open_transaction' => $open_transaction,
            'open_lever' => $open_lever,
            'lever_share_num' => $lever_share_num,
            'lever_trade_fee' => $lever_trade_fee,
            'spread' => $spread,
            'overnight' => $overnight,
            'lever_min_share' => $lever_min_share,
            'lever_max_share' => $lever_max_share,
            'create_time' => time(),
        ]);
        CurrencyMatch::reguard();
        return isset($currency_match->id) ? $this->success('添加成功') : $this->error('添加失败');
    }

    public function editMatch($id)
    {
        $currency_match = CurrencyMatch::find($id);
        if (!$currency_match) {
            abort(403, '指定交易对不存在');
        }
        $market_from_names = CurrencyMatch::enumMarketFromNames();
        $currencies = Currency::where('id', '<>', $currency_match->legal_id)->get();
        $var = compact('currency_match', 'currencies', 'market_from_names');
        return view('admin.currency.match_add', $var);
    }

    public function postEditMatch(Request $request, $id)
    {
        $currency_id = $request->input('currency_id');
        $is_display = $request->input('is_display', 1);
        $market_from = $request->input('market_from', 0);
        $open_transaction = $request->input('open_transaction', 0);
        $open_lever = $request->input('open_lever', 0);
        $lever_share_num = $request->input('lever_share_num', 1);
        $spread = $request->input('spread', 0);
        $overnight = $request->input('overnight', 0);
        $lever_trade_fee = $request->input('lever_trade_fee', 0);
        $lever_min_share = $request->input('lever_min_share', 0);
        $lever_max_share = $request->input('lever_max_share', 0);
        $currency_match = CurrencyMatch::find($id);
        if (!$currency_match) {
            abort(403, '指定交易对不存在');
        }
        CurrencyMatch::unguard();
        $result = $currency_match->fill([
            'currency_id' => $currency_id,
            'is_display' => $is_display,
            'market_from' => $market_from,
            'open_transaction' => $open_transaction,
            'open_lever' => $open_lever,
            'lever_share_num' => $lever_share_num,
            'lever_trade_fee' => $lever_trade_fee,
            'spread' => $spread,
            'overnight' => $overnight,
            'lever_min_share' => $lever_min_share,
            'lever_max_share' => $lever_max_share,
            'create_time' => time(),
        ])->save();
        CurrencyMatch::reguard();
        return $result ? $this->success('保存成功') : $this->error('保存失败');
    }

    public function delMatch($id)
    {
        $result = CurrencyMatch::destroy($id);
        return $result ? $this->success('删除成功') : $this->error('删除失败');
    }
}
