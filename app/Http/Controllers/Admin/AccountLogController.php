<?php

namespace App\Http\Controllers\Admin;
use App\AccountLog;
use App\Currency;
use App\Users;
use Symfony\Component\HttpFoundation\Request;

class AccountLogController extends Controller{

    public function index(){
        //获取type类型
        $type=array(
           AccountLog::ADMIN_LEGAL_BALANCE         => '后台调节法币账户余额',
           AccountLog::ADMIN_LOCK_LEGAL_BALANCE         => '后台调节法币账户锁定余额',
           AccountLog::ADMIN_CHANGE_BALANCE         => '后台调节币币账户余额',
           AccountLog::ADMIN_LOCK_CHANGE_BALANCE         => '后台调节币币账户锁定余额',
           AccountLog::ADMIN_LEVER_BALANCE         => '后台调节合约账户余额',
           AccountLog::ADMIN_LOCK_LEVER_BALANCE         => '后台调节合约账户锁定余额',
           AccountLog::WALLET_CURRENCY_OUT         => '法币账户转出至交易账户',
           AccountLog::WALLET_CURRENCY_IN         => '交易账户转入至法币账户',
           AccountLog::TRANSACTIONOUT_SUBMIT_REDUCE         => '提交卖出，扣除',
           AccountLog::TRANSACTIONIN_REDUCE        => '买入扣除',

           

           AccountLog::INVITATION_TO_RETURN         => '邀请返佣金',
        );
        $currency_type = Currency::all();
        return view("admin.account.index",[
            'types' => $type,
            'currency_type' => $currency_type
        ]);
    }

    public function lists(Request $request){
        $limit = $request->get('limit',10);
        $account= $request->get('account','');
        $start_time = strtotime($request->get('start_time',0));
        $end_time = strtotime($request->get('end_time',0));
        $currency = $request->get('currency_type',0);
        $type= $request->get('type',0);

        $list = New AccountLog();
        if(!empty($currency)){
            $list = $list->where('currency',$currency);
        }
        if (!empty($type)) {
             $list = $list->where('type',$type);
        }
        if(!empty($start_time)){
            $list = $list->where('created_time','>=',$start_time);
        }
        if(!empty($end_time)){
            $list = $list->where('created_time','<=',$end_time);
        }
        //根据关联模型的时间
        /*if(!empty($start_time)){
            $list = $list->whereHas('walletLog', function ($query) use ($start_time) {
                $query->where('create_time','>=',$start_time);
            });
        }
        if(!empty($end_time)){
            $list = $list->whereHas('walletLog', function ($query) use ($end_time) {
                $query->where('create_time','<=',$end_time);
            });
        }*/
        if (!empty($account)) {
           $list = $list->whereHas('user', function($query) use ($account) {
            $query->where("phone",'like','%'.$account.'%')->orwhere('email','like','%'.$account.'%');
             } );
        }

      /* if (!empty($account_number)) {
            $list = $list->whereHas('user', function($query) use ($account_number) {
            $query->where('account_number','like','%'.$account_number.'%'); 
             } );
        }*/

        $list = $list -> orderBy('id','desc')->paginate($limit);
        //dd($list->items());
        return response()->json(['code' => 0, 'data' => $list->items(), 'count' => $list->total()]);
    }

    public function view(Request $request){
        $id = $request->get('id',null);
        $results = new AccountLog();
        $results = $results->where('id',$id)->first();
        if(empty($results)){
            return $this->error('无此记录');
        }
        return view('admin.account.viewDetail',['results'=>$results]);
    }

  






}
?>