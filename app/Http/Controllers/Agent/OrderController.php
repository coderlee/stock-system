<?php

namespace App\Http\Controllers\Agent;

use App\Agent;
use App\AgentMoneylog;
use App\Currency;
use App\Levertolegal;
use App\Setting;
use App\LeverTransaction;
use App\TransactionOrder;
use App\Users;
use App\UsersWallet;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;


use Maatwebsite\Excel\Facades\Excel;



/**
 * 该类处理所有的订单与结算。
 * Class ReportController
 * @package App\Http\Controllers\Agent
 */
class OrderController extends Controller{


    public function order_list(Request $request){

        $limit = $request->input("limit", 10);
        $id = $request->input("id", 0);
        $username = $request->input("username", '');
        $agentusername = $request->input("agentusername", '');
        $status = $request->input("status", 10);
        $type = $request->input("type", 0);

        $start = $request->input("start", '');
        $end = $request->input("end", '');

        $_all_orders = $this->get_my_all_orders();

        $where = [];
        if ($id > 0){
            if (!in_array($id , $_all_orders['ids'])){
                return $this->error('该ID的订单并不属于您的团队');
            }else{
                $where[] = ['id' , '=' , $id];
            }
        }
        if (!empty($username)){

            if (!in_array($username , $_all_orders['account_number'])){
                return $this->error('该用户并不属于您的团队');
            }else{
                $s = DB::table('users')->where('account_number' , $username)->first();
                if ($s !== null){
                    $where[] = ['user_id' , '=' , $s->id];
                }
            }

        }
        if ($status  != 10   && in_array($status , [LeverTransaction::ENTRUST,LeverTransaction::TRANSACTION,LeverTransaction::CLOSED,LeverTransaction::CANCEL,LeverTransaction::CLOSING])){
            $where[] = ['status' , '=' , $status];
        }
        if ($type > 0 && in_array($type , [1,2])){
            $where[] = ['type' , '=' , $type];
        }
        if (!empty($start) && !empty($end)) {
            $where[] = ['create_time' , '>' , strtotime($start . ' 0:0:0')];
            $where[] = ['create_time' , '<' , strtotime($end . ' 23:59:59')];
        }

        $_self = Agent::getAgent();
        if ($_self === null){
            return $this->outmsg('发生错误！请重新登录');
        }
        $sons = $this->get_my_sons();

        if (!empty($agentusername)){
            $s = DB::table('agent')->where('username' , $agentusername)->first();

            if ($s===null){
                return $this->error('该代理商不存在');
            }

            if (!in_array($s->id , $sons['all_agent'])){
                return $this->error('该代理商并不属于您的团队');
            }else{

                $p_s_s = $this->get_my_sons($s->id);

                if (!empty($p_s_s)){
                    $order_list = TransactionOrder::whereIn('status' , [LeverTransaction::ENTRUST,LeverTransaction::TRANSACTION,LeverTransaction::CLOSED,LeverTransaction::CANCEL,LeverTransaction::CLOSING])->whereIn('user_id' , $p_s_s['all'])->where($where)->paginate($limit);
                }else{

                    $order_list = TransactionOrder::whereIn('status' , [LeverTransaction::ENTRUST,LeverTransaction::TRANSACTION,LeverTransaction::CLOSED,LeverTransaction::CANCEL,LeverTransaction::CLOSING])->whereIn('user_id' , $sons['all'])->where($where)->paginate($limit);

                }
            }
        }else{

            $order_list = TransactionOrder::whereIn('status' , [LeverTransaction::ENTRUST,LeverTransaction::TRANSACTION,LeverTransaction::CLOSED,LeverTransaction::CANCEL,LeverTransaction::CLOSING])->whereIn('user_id' , $sons['all'])->where($where)->paginate($limit);


        }

        return $this->layuiData($order_list);
    }

    /**
     *获取统计数据
     */
    public function get_order_account(Request $request){

        $id = $request->input("id", 0);
        $username = $request->input("username", '');
        $agentusername = $request->input("agentusername", '');
        $status = $request->input("status", 10);
        $type = $request->input("type", 0);

        $start = $request->input("start", '');
        $end = $request->input("end", '');

        $_all_orders = $this->get_my_all_orders();

        $where = [];
        if ($id > 0){
            if (!in_array($id , $_all_orders['ids'])){
                return $this->error('该ID的订单并不属于您的团队');
            }else{
                $where[] = ['id' , '=' , $id];
            }
        }
        if (!empty($username)){

            if (!in_array($username , $_all_orders['account_number'])){
                return $this->error('该用户并不属于您的团队');
            }else{
                $s = DB::table('users')->where('account_number' , $username)->first();
                if ($s !== null){
                    $where[] = ['user_id' , '=' , $s->id];
                }
            }

        }
        if ($status  != 10   && in_array($status , [LeverTransaction::ENTRUST,LeverTransaction::TRANSACTION,LeverTransaction::CLOSED,LeverTransaction::CANCEL,LeverTransaction::CLOSING])){
            $where[] = ['status' , '=' , $status];
        }
        if ($type > 0 && in_array($type , [1,2])){
            $where[] = ['type' , '=' , $type];
        }
        if (!empty($start) && !empty($end)) {
            $where[] = ['create_time' , '>' , strtotime($start . ' 0:0:0')];
            $where[] = ['create_time' , '<' , strtotime($end . ' 23:59:59')];
        }

        $_self = Agent::getAgent();
        if ($_self === null){
            return $this->outmsg('发生错误！请重新登录');
        }
        $sons = $this->get_my_sons();

        if (!empty($agentusername)){
            $s = DB::table('agent')->where('username' , $agentusername)->first();
            if ($s===null){
                return $this->error('该代理商不存在');
            }


            if (!in_array($s->id , $sons['all_agent'])){
                return $this->error('该代理商并不属于您的团队');
            }else{

                $p_s_s = $this->get_my_sons($s->id);

                if (!empty($p_s_s)){
                    //总订单数
                    $_count = TransactionOrder::whereIn('status' , [LeverTransaction::ENTRUST,LeverTransaction::TRANSACTION,LeverTransaction::CLOSED,LeverTransaction::CANCEL,LeverTransaction::CLOSING])->whereIn('user_id' , $p_s_s['all'])->where($where)->count();
                    //头寸收益
                    $_toucun = TransactionOrder::whereIn('status' , [LeverTransaction::CLOSED])->whereIn('user_id' , $p_s_s['all'])->where($where)->sum('fact_profits');
                    //手续费收益
                    $_shouxu = TransactionOrder::whereIn('status' , [LeverTransaction::CLOSED])->whereIn('user_id' , $p_s_s['all'])->where($where)->sum('trade_fee');
                }else{
                    //总订单数
                    $_count = TransactionOrder::whereIn('status' , [LeverTransaction::ENTRUST,LeverTransaction::TRANSACTION,LeverTransaction::CLOSED,LeverTransaction::CANCEL,LeverTransaction::CLOSING])->whereIn('user_id' , $sons['all'])->where($where)->count();
                    //头寸收益
                    $_toucun = TransactionOrder::whereIn('status' , [LeverTransaction::CLOSED])->whereIn('user_id' , $sons['all'])->where($where)->sum('fact_profits');
                    //手续费收益
                    $_shouxu = TransactionOrder::whereIn('status' , [LeverTransaction::CLOSED])->whereIn('user_id' , $sons['all'])->where($where)->sum('trade_fee');
                }
            }
        }else{
            //总订单数
            $_count = TransactionOrder::whereIn('status' , [LeverTransaction::ENTRUST,LeverTransaction::TRANSACTION,LeverTransaction::CLOSED,LeverTransaction::CANCEL,LeverTransaction::CLOSING])->whereIn('user_id' , $sons['all'])->where($where)->count();
            //头寸收益
            $_toucun = TransactionOrder::whereIn('status' , [LeverTransaction::CLOSED])->whereIn('user_id' , $sons['all'])->where($where)->sum('fact_profits');
            //手续费收益
            $_shouxu = TransactionOrder::whereIn('status' , [LeverTransaction::CLOSED])->whereIn('user_id' , $sons['all'])->where($where)->sum('trade_fee');
        }
        //手续费收益
        $_guoye = TransactionOrder::whereIn('status' , [LeverTransaction::CLOSED])->whereIn('user_id' , $sons['all'])->where($where)->sum('overnight_money');
        $data = [];
        $data['_num'] = $_count;
        $data['_toucun'] = $_toucun;
        $data['_shouxu'] = bc_mul(-1 , $_shouxu);

        $_sjok = bc_add($data['_toucun'] , $data['_shouxu']);

        $data['_all'] = $_sjok;  //bc_add($_sjok , $_guoye);

//        $data['chujin'] = TransactionOrder::whereIn('status' , [LeverTransaction::ENTRUST,LeverTransaction::TRANSACTION,LeverTransaction::CLOSED,LeverTransaction::CANCEL,LeverTransaction::CLOSING])->where('type' , 2)->whereIn('user_id' , $sons['all'])->where($where)->sum('price');
        $data['chujin'] = Levertolegal::whereIn('user_id' , $sons['all'])->where('type' , 1)->where('status' , 2)->sum('number');
//        $data['rujin'] = TransactionOrder::whereIn('status' , [LeverTransaction::ENTRUST,LeverTransaction::TRANSACTION,LeverTransaction::CLOSING])->where('type' , 1)->whereIn('user_id' , $sons['all'])->where($where)->sum('price');
        $data['rujin'] = Levertolegal::whereIn('user_id' , $sons['all'])->where('type' , 2)->where('status' , 2)->sum('number');

        $data['lock'] = TransactionOrder::whereIn('status' , [LeverTransaction::ENTRUST,LeverTransaction::TRANSACTION,LeverTransaction::CLOSED,LeverTransaction::CANCEL,LeverTransaction::CLOSING])->whereIn('user_id' , $sons['all'])->where($where)->sum('origin_caution_money');
        $Currency = Currency::where('name' , 'USDT')->first();
        $data['locklage'] = UsersWallet::where('currency' , $Currency->id)->whereIn('user_id' , $sons['all'])->sum('lock_legal_balance');
       

        return $this->ajaxReturn($data);
    }


    /**
     * 获取该用户的团队所有的订单
     */
    public function get_my_all_orders(){
        $_self = $this->get_my_sons();

        $all = TransactionOrder::whereIn('status' , [LeverTransaction::ENTRUST,LeverTransaction::TRANSACTION,LeverTransaction::CLOSED,LeverTransaction::CANCEL,LeverTransaction::CLOSING])->whereIn('user_id' , $_self['all'])->get()->toArray();

        $data = [];
        $ids = [];
        $account_numbers = [];

        if (!empty($all)){
            foreach ($all as $key => $value){
                $ids[] = $value['id'];

                $info = DB::table('users')->where('id' , $value['user_id'])->first();
                if ($info) {
                    $account_numbers[] = $info->account_number;
                }
            }
            $data['ids'] = $ids;
            $data['account_number'] = $account_numbers;
        }

        return $data;

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    //导出订单记录Excel
    public function order_excel(Request $request){
        $limit = $request->input("limit", 10);
        $id = $request->input("id", 0);
        $username = $request->input("username", '');
        $agentusername = $request->input("agentusername", '');
        $status = $request->input("status", 10);
        $type = $request->input("type", 0);

        $start = $request->input("start", '');
        $end = $request->input("end", '');

        $where = [];
        if ($id > 0){
            $where[] = ['id' , '=' , $id];
        }
        if (!empty($username)){
            $s = DB::table('users')->where('account_number' , $username)->first();
            if ($s !== null){
                $where[] = ['user_id' , '=' , $s->id];
            }
        }
        if ($status  != 10   && in_array($status , [LeverTransaction::ENTRUST,LeverTransaction::TRANSACTION,LeverTransaction::CLOSED,LeverTransaction::CANCEL,LeverTransaction::CLOSING])){
            $where[] = ['status' , '=' , $status];
        }
        if ($type > 0 && in_array($type , [1,2])){
            $where[] = ['type' , '=' , $type];
        }
        if (!empty($start) && !empty($end)) {
            $where[] = ['create_time' , '>' , strtotime($start . ' 0:0:0')];
            $where[] = ['create_time' , '<' , strtotime($end . ' 23:59:59')];
        }

        $_self = Agent::getAgent();
        if ($_self === null){
            return $this->outmsg('发生错误！请重新登录');
        }
        $sons = $this->get_my_sons();

        if (!empty($agentusername)){
            $s = DB::table('agent')->where('username' , $agentusername)->first();

            if (!in_array($s->id , $sons['all'])){
                return $this->error('该代理商并不属于您的团队');
            }else{

                $p_s_s = $this->get_my_sons($s->id);

                if (!empty($p_s_s)){
                    $order_list = TransactionOrder::whereIn('status' , [LeverTransaction::ENTRUST,LeverTransaction::TRANSACTION,LeverTransaction::CLOSED,LeverTransaction::CANCEL,LeverTransaction::CLOSING])->whereIn('user_id' , $p_s_s['all'])->where($where)->get()->toArray();
                }else{

                    $order_list = TransactionOrder::whereIn('status' , [LeverTransaction::ENTRUST,LeverTransaction::TRANSACTION,LeverTransaction::CLOSED,LeverTransaction::CANCEL,LeverTransaction::CLOSING])->whereIn('user_id' , $sons['all'])->where($where)->get()->toArray();

                }
            }
        }else{

            $order_list = TransactionOrder::whereIn('status' , [LeverTransaction::ENTRUST,LeverTransaction::TRANSACTION,LeverTransaction::CLOSED,LeverTransaction::CANCEL,LeverTransaction::CLOSING])->whereIn('user_id' , $sons['all'])->where($where)->get()->toArray();


        }

        $data=$order_list;
        return Excel::create('订单数据', function ($excel) use ($data)
        {
            $excel->sheet('订单数据', function ($sheet) use ($data)
            {
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('ID');
                });
                $sheet->cell('B1', function ($cell) {
                    $cell->setValue('用户名');
                });
                $sheet->cell('C1', function ($cell) {
                    $cell->setValue('上级代理商');
                });
                $sheet->cell('D1', function ($cell) {
                    $cell->setValue('等级');
                });
                $sheet->cell('E1', function ($cell) {
                    $cell->setValue('交易类型');
                });
                $sheet->cell('F1', function ($cell) {
                    $cell->setValue('当前状态');
                });
                $sheet->cell('G1', function ($cell) {
                    $cell->setValue('原始价格');
                });
                $sheet->cell('H1', function ($cell) {
                    $cell->setValue('开仓价格');
                });
                $sheet->cell('I1', function ($cell) {
                    $cell->setValue('当前价格');
                });
                $sheet->cell('J1', function ($cell) {
                    $cell->setValue('手数');
                });
                $sheet->cell('K1', function ($cell) {
                    $cell->setValue('倍数');
                });
                $sheet->cell('L1', function ($cell) {
                    $cell->setValue('初始保证金');
                });
                $sheet->cell('M1', function ($cell) {
                    $cell->setValue('当前可用保证金');
                });
                $sheet->cell('N1', function ($cell) {
                    $cell->setValue('创建时间');
                });
                $sheet->cell('O1', function ($cell) {
                    $cell->setValue('价格刷新时间');
                });
                $sheet->cell('P1', function ($cell) {
                    $cell->setValue('平仓时间');
                });
                $sheet->cell('Q1', function ($cell) {
                    $cell->setValue('完成时间');
                });
                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        if($value['type']==1)
                        {
                            $value['type']="买入";
                        }
                        else{
                            $value['type']="卖出";
                        }
                        if($value['status']==0)
                        {
                            $value['status']="挂单中";
                        }elseif($value['status']==1)
                        {
                            $value['status']="交易中";
                        }
                        elseif($value['status']==2)
                        {
                            $value['status']="平仓中";
                        }
                        elseif($value['status']==3)
                        {
                            $value['status']="已平仓";
                        }
                        elseif($value['status']==4)
                        {
                            $value['status']="已撤单";
                        }

                        $i = $key + 2;
                        $sheet->cell('A' . $i, $value['id']);
                        $sheet->cell('B' . $i, $value['user_name']);
                        $sheet->cell('C' . $i, $value['parent_agent_name']);
                        $sheet->cell('D' . $i, $value['agent_level']);
                        $sheet->cell('E' . $i, $value['type']);
                        $sheet->cell('F' . $i, $value['status']);
                        $sheet->cell('G' . $i, $value['origin_price']);
                        $sheet->cell('H' . $i, $value['price']);
                        $sheet->cell('I' . $i, $value['update_price']);//当前价格
                        $sheet->cell('J' . $i, $value['share']);//手数
                        $sheet->cell('K' . $i, $value['multiple']);
                        $sheet->cell('L' . $i, $value['origin_caution_money']);//初始保证金
                        $sheet->cell('M' . $i, $value['caution_money']);
                        $sheet->cell('N' . $i, $value['create_time']);//创建时间
                        $sheet->cell('O' . $i, $value['update_time']);
                        $sheet->cell('P' . $i, $value['handle_time']);//平仓时间
                        $sheet->cell('Q' . $i, $value['complete_time']);

                    }
                }
                ob_end_clean();
            });
        })->download('xlsx');
    }


    //导出订单记录Excel
    public function user_excel(Request $request){

        $id             = request()->input('id', 0);
        $parent_id            = request()->input('parent_id', 0);
        $account_number = request()->input('account_number', '');
        $start = request()->input('start', '');
        $end = request()->input('end', '');

        $users = new Users();

        if ($id) {
            $users = $users->where('id', $id);
        }
        if ($parent_id > 0) {
            $users = $users->where('agent_note_id', $parent_id);
        }
        if ($account_number) {
            $users = $users->where('account_number', $account_number);
        }
        if (!empty($start) && !empty($end)){
            $users->whereBetween('time' , [strtotime($start.' 0:0:0') , strtotime($end.' 23:59:59')]);
        }

        $my_agent_list=Agent::getLevel4AgentId(Agent::getAgentId(),[Agent::getAgentId()]);

        $users = $users->whereIn('agent_note_id', $my_agent_list);

        $data = $users->get()->toArray();
//var_dump($data);die;

//        var_dump($data);die;
//        $data = TransactionOrder::leftjoin("users","lever_transaction.user_id","=","users.user_id")->all()->toArray();
        return Excel::create('用户列表', function ($excel) use ($data)
        {
            $excel->sheet('用户列表', function ($sheet) use ($data)
            {
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('ID');
                });
                $sheet->cell('B1', function ($cell) {
                    $cell->setValue('用户名');
                });
                $sheet->cell('C1', function ($cell) {
                    $cell->setValue('用户身份');
                });
                $sheet->cell('D1', function ($cell) {
                    $cell->setValue('上级代理商');
                });
                $sheet->cell('E1', function ($cell) {
                    $cell->setValue('USDT余额');
                });
                $sheet->cell('F1', function ($cell) {
                    $cell->setValue('保证金');
                });
                $sheet->cell('G1', function ($cell) {
                    $cell->setValue('邮箱');
                });
                $sheet->cell('H1', function ($cell) {
                    $cell->setValue('邀请码');
                });
                $sheet->cell('I1', function ($cell) {
                    $cell->setValue('加入时间');
                });

                if (!empty($data)) {
                    foreach ($data as $key => $value) {

                        $i = $key + 2;
                        $sheet->cell('A' . $i, $value['id']);
                        $sheet->cell('B' . $i, $value['account_number']);
                        $sheet->cell('C' . $i, $value['my_agent_level']);
                        $sheet->cell('D' . $i, $value['parent_name']);
                        $sheet->cell('E' . $i, $value['usdt']);
                        $sheet->cell('F' . $i, $value['caution_money']);
                        $sheet->cell('G' . $i, $value['email']);
                        $sheet->cell('H' . $i, $value['extension_code']);
                        $sheet->cell('I' . $i, $value['create_date']);//当前价格


                    }
                }
                ob_end_clean();
            });
        })->download('xlsx');
    }

    public function jie_list(Request $request){

        $limit = $request->input("limit", 10);
        $id = $request->input("id", 0);
        $username = $request->input("username", '');

        $where = [];
        if ($id > 0){
            $where[] = ['id' , '=' , $id];
        }
        if (!empty($username)){
            $s = DB::table('users')->where('account_number' , $username)->first();
            if ($s !== null){
                $where[] = ['son_user_id' , '=' , $s->id];
            }
        }

        $_self = Agent::getAgent();
        if ($_self === null){
            return $this->outmsg('发生错误！请重新登录');
        }

        $order_list = AgentMoneylog::where('agent_id' , $_self->id)->where($where)->paginate($limit);

        return $this->layuiData($order_list);
    }


    //入金：所有法币余额转合约账户的总和
    //出金：


    /**
     * 订单详情
     */
    public function order_info ( Request $request ){
        $order_id = $request->input("order_id", 0);

        if ($order_id >0 ){
            $sons = $this->get_my_sons();

            $orderinfo = TransactionOrder::where('id' , $order_id)->whereIn('user_id' , $sons['all'])->first();

            if ($orderinfo == null){
                return $this->error('订单编号错误或者您无权查看订单详情');
            }else{
                $data['info'] = $orderinfo;
                return $this->ajaxReturn($data);
            }

        }else{
            return $this->error('非法参数');
        }

    }

    /**
     * 获取我的所有的下级。包括所有的散户和各级代理商
     */
    public function get_my_sons( $agent_id = 0 ){

        if ($agent_id === 0){
            $_self = Agent::getAgent();
        }else{
            $_self = Agent::getAgentById($agent_id);
        }

        $_one = [];
        $_one_sons = [];
        $_two = [];
        $_two_sons = [];
        $_three = [];
        $_three_sons = [];
        $_four = [];
        $_four_sons = [];
        switch ($_self->level){
            case 0 :
                $_one = DB::table('agent')->where('level' , 1)->select('user_id' , 'id')->get()->toArray();
                $_two = DB::table('agent')->where('level' , 2)->select('user_id' , 'id')->get()->toArray();
                $_three = DB::table('agent')->where('level' , 3)->select('user_id' , 'id')->get()->toArray();
                $_four = DB::table('agent')->where('level' , 4)->select('user_id' , 'id')->get()->toArray();
                break;

            case 1:
                $_two = DB::table('agent')->where('parent_agent_id' , $_self->id)->get()->toArray();
                $_one_sons = DB::table('users')->where('agent_id' , 0)->where('agent_note_id' , $_self->id)->get()->toArray();

                if (!empty($_two)){
                    foreach ($_two as $key => $value){
                        $_a = DB::table('agent')->where('parent_agent_id' , $value->id)->get()->toArray();
                        $_b = DB::table('users')->where('agent_id' , 0)->where('agent_note_id' , $value->id)->get()->toArray();
                        $_three = array_merge($_three , $_a);
                        $_two_sons = array_merge($_two_sons , $_b);
                    }
                }

                if (!empty($_three)){
                    foreach ($_three as $key => $value){
                        $_a = DB::table('agent')->where('parent_agent_id' , $value->id)->get()->toArray();
                        $_b = DB::table('users')->where('agent_id' , 0)->where('agent_note_id' , $value->id)->get()->toArray();
                        $_four = array_merge($_four , $_a);
                        $_three_sons = array_merge($_three_sons , $_b);
                    }
                }

                if (!empty($_four)){
                    foreach ($_four as $key=>$value){
                        $_b = DB::table('users')->where('agent_id' , 0)->where('agent_note_id' , $value->id)->get()->toArray();
                        $_three_sons = array_merge($_three_sons , $_b);
                    }
                }

                break;
            case 2:
                $_three = DB::table('agent')->where('parent_agent_id' , $_self->id)->get()->toArray();
                $_two_sons = DB::table('users')->where('agent_id' , 0)->where('agent_note_id' , $_self->id)->get()->toArray();
                if (!empty($_two)){
                    foreach ($_two as $key => $value){
                        $_a = DB::table('agent')->where('parent_agent_id' , $value->id)->get()->toArray();
                        $_b = DB::table('users')->where('agent_id' , 0)->where('agent_note_id' , $value->id)->get()->toArray();
                        $_four = array_merge($_four , $_a);
                        $_three_sons = array_merge($_three_sons , $_b);
                    }
                }

                if (!empty($_four)){
                    foreach ($_four as $key=>$value){
                        $_b = DB::table('users')->where('agent_id' , 0)->where('agent_note_id' , $value->id)->get()->toArray();
                        $_four_sons = array_merge($_four_sons , $_b);
                    }
                }
                break;
            case 3:
                $_four = DB::table('agent')->where('parent_agent_id' , $_self->id)->get()->toArray();
                $_three_sons = DB::table('users')->where('agent_id' , 0)->where('agent_note_id' , $_self->id)->get()->toArray();

                if (!empty($_four)){
                    foreach ($_four as $key=>$value){
                        $_b = DB::table('users')->where('agent_id' , 0)->where('agent_note_id' , $value->id)->get()->toArray();
                        $_four_sons = array_merge($_four_sons , $_b);
                    }
                }
                break;
            case 4:
                $_four_sons = DB::table('users')->where('agent_id' , 0)->where('agent_note_id' , $_self->id)->get()->toArray();
                break;

        }

        if ($_self->level == 0  && $_self->is_admin ==1){
            $san_user = DB::table('users')->where('agent_id' , 0)->get()->toArray();  //所有的散户
            $san_user = $this->sel_agent_arr($san_user);
        }else{
            $a = $this->sel_agent_arr($_one_sons);
            $b = $this->sel_agent_arr($_two_sons);
            $c = $this->sel_agent_arr($_three_sons);
            $d = $this->sel_agent_arr($_four_sons);
            $san_user = array_merge($a , $b , $c , $d);
        }


        $data = [];
        $data['san'] = $san_user;
        $data['one'] = $this->sel_arr($_one);
        $data['one_agent'] = $this->sel_agent_arr($_one);
        $data['two'] = $this->sel_arr($_two);
        $data['two_agent'] = $this->sel_agent_arr($_two);
        $data['three'] = $this->sel_arr($_three);
        $data['three_agent'] = $this->sel_agent_arr($_three);
        $data['four'] = $this->sel_arr($_four);
        $data['four_agent'] = $this->sel_agent_arr($_four);
        $all = array_merge($data['san'] , $data['one'] , $data['two'] , $data['three'] , $data['four']);
        $data['all'] = !empty($all) ? $all : [0];

        $all_agent = array_merge($data['one_agent'] , $data['two_agent'] , $data['three_agent'] , $data['four_agent']);
        $data['all_agent'] = !empty($all_agent) ? $all_agent : [0];

        return $data;
    }

    /**
     * @param $san_user
     *
     */
    public function sel_arr($arr = array()){
        if (!empty($arr)){
            $new_arr = [];
            foreach ($arr as $k => $val){
                $new_arr[] = $val->user_id;
            }
            return $new_arr;
        }else{
            return [];
        }

    }

    /**
     * @param $san_user
     *
     */
    public function sel_agent_arr($arr = array()){
        if (!empty($arr)){
            $new_arr = [];
            foreach ($arr as $k => $val){
                $new_arr[] = $val->id;

            }
            return $new_arr;
        }else{
            return [];
        }

    }

}
