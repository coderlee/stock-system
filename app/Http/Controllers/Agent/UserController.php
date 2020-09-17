<?php
/**
 * Created by PhpStorm.
 * User: YSX
 * Date: 2018/12/4
 * Time: 16:36
 */

namespace App\Http\Controllers\Agent;


use App\Agent;
use App\Users;
use Illuminate\Http\Request;
use App\Levertolegal;

class UserController extends Controller
{

    public function lists(Request $request)
    {
        $limit = $request->get('limit', 10);
        $id             = request()->input('id', 0);
        $parent_id            = request()->input('parent_id', 0);
        $account_number = request()->input('account_number', '');
        $start = request()->input('start', '');
        $end = request()->input('end', '');

        $users = new Users();

//        $list = new Users();
        $users=$users->leftjoin("user_real","users.id","=","user_real.user_id");


        if ($id) {
            $users = $users->where('users.id', $id);
        }
        if ($parent_id > 0) {
            $users = $users->where('users.agent_note_id', $parent_id);
        }
        if ($account_number) {
            $users = $users->where('users.account_number', $account_number);
        }
        if (!empty($start) && !empty($end)){
            $users->whereBetween('users.time' , [strtotime($start.' 0:0:0') , strtotime($end.' 23:59:59')]);
        }

        $my_agent_list=Agent::getLevel4AgentId(Agent::getAgentId(),[Agent::getAgentId()]);

        $users = $users->whereIn('users.agent_note_id', $my_agent_list);

//        $list = $users->select("users.*","user_real.card_id")->paginate($limit);
        $list = $users->select("users.*","user_real.card_id")->paginate($limit);
        foreach($list as $key=>$value)
        {
            $list["$key"]->chujin=Levertolegal::where("user_id","=",$value->id)->where("status","=",2)->where("type","=",2)->sum("number");//1:c2c转合约  2合约转c2c
            $list["$key"]->rujin=Levertolegal::where("user_id","=",$value->id)->where("status","=",2)->where("type","=",1)->sum("number");//1:c2c转合约  2合约转c2c
//            var_dump($list["$key"]->toArray());die;
        }


        return $this->layuiData($list);
    }



    public function huazhuan_lists(Request $request)
    {
        $limit = $request->get('limit', 10);
        $id             = request()->input('id', 0);
        $account_number = request()->input('account_number', '');
        $start = request()->input('start', '');
        $end = request()->input('end', '');
        $users = new Levertolegal();
        $users=$users->leftjoin("users","users.id","=","lever_tolegal.user_id");
        if ($id) {
            $users = $users->where('lever_tolegal.id', $id);
        }
        if ($account_number) {
            $users = $users->where('users.account_number', $account_number);
        }
        if (!empty($start) && !empty($end)){
            $users->whereBetween('lever_tolegal.add_time' , [strtotime($start.' 0:0:0') , strtotime($end.' 23:59:59')]);
        }
        $list = $users->select("lever_tolegal.*","users.account_number")->orderBy("add_time","desc")->paginate($limit);
//        var_dump($list->toArray());die;
        foreach($list as $key=>$value)
        {
            $list["$key"]["add_time"]=date("Y-m-d H:i:s",$value->add_time);
//            var_dump($value->type);
            if($value->type==1)
            {
                $list["$key"]->type="入金";
            }
            elseif($value->type==2)
            {
                $list["$key"]->type="出金";
            }
        }
        return $this->layuiData($list);
    }


    /**
     * 获取用户管理的统计
     * @param Request $r
     */
    public function get_user_num(Request $request){

        $id             = request()->input('id', 0);
        $account_number = request()->input('account_number', '');
        $parent_id            = request()->input('parent_id', 0);
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

        $my_agent_list = Agent::getLevel4AgentId(Agent::getAgentId(),[Agent::getAgentId()]);

        $users = $users->whereIn('agent_note_id', $my_agent_list);

        $obj = $users->get()->toArray();

        $_daili = 0;
        $_usdt = 0.00;
        $_caution_money = 0.00;
        foreach ($obj as $key => $value) {
            if ($value['agent_id'] > 0) {
                $_daili++;
            }
            $_usdt = bc_add($_usdt , $value['usdt']);
            $_caution_money = bc_add($_caution_money , $value['caution_money']);
        }

        $data = [];
        $data['_num'] = count($obj);
        $data['_daili'] = $_daili;
        $data['_usdt'] = $_usdt;
        $data['_caution_money'] = $_caution_money;


        return $this->ajaxReturn($data);
    }


    public function get_my_invite_code(){

        $_self = Agent::getAgent();

        if ($_self == null){
            $this->outmsg('超时');
        }

        $use = Users::getById($_self->user_id);

        return $this->ajaxReturn(['invite_code'=>$use->extension_code , 'is_admin'=>$_self->is_admin]);
    }


}