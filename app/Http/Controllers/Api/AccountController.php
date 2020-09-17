<?php

namespace App\Http\Controllers\Api;

use App\AccountLog;
use App\Transaction;
use App\Users;
use Illuminate\Support\Facades\Input;

class AccountController extends Controller{

    public function list(){
        $address = Users::getUserId(Input::get('address',''));
        $limit = Input::get('limit','12');
        $page = Input::get('page','1');
        if (empty($address)) return $this->error("参数错误");

        $user = Users::where("id",$address)->first();
        if (empty($user)) return $this->error("数据未找到");


        $data = AccountLog::where("user_id",$user->id)->orderBy('id', 'DESC')->paginate($limit);
        return $this->success(array(
            "user_id"=>$user->id,
            "data"=>$data->items(),
            "limit"=>$limit,
            "page"=>$page,
        ));
    }

}
?>