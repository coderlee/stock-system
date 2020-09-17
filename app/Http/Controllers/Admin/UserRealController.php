<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use App\Users;
use App\UserReal;

use App\IdCardIdentity;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Request;

class UserRealController extends Controller
{
    
    public function index(){
        return view("admin.userReal.index");
    }
    //用户列表
    public function list(Request $request)
    {
        $limit = $request->get('limit', 10);
        $account = $request->get('account', '');
     
        $list = new UserReal();
        if (!empty($account)) {
            $list = $list->whereHas('user', function($query) use ($account) {
            $query->where("phone",'like','%'.$account.'%')->orwhere('email','like','%'.$account.'%');
             } );
        }

        $list = $list->orderBy('id', 'desc')->paginate($limit);
        return response()->json(['code' => 0, 'data' => $list->items(), 'count' => $list->total()]);
    }

     public function detail(Request $request){
        
        $id = $request->get('id',0);
        if (empty($id)){
            return $this->error("参数错误");
        }
      
        $result = UserReal::find($id);

        return view('admin.userReal.info',['result'=>$result]);
    }

     public function del(Request $request){
        $id = $request->get('id');
        $userreal = UserReal::find($id);
        if(empty($userreal)){
            $this->error("认证信息未找到");
        }
        try {

            $userreal->delete();
            return $this->success('删除成功');
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }
//状态审核
    public function auth(Request $request){
        $id = $request->get('id',0);

        $userreal = UserReal::find($id);
        if (empty($userreal)){
            return $this->error('参数错误');
        }
        if ($userreal->review_status == 1){
            $userreal->review_status = 2;
        }else if($userreal->review_status == 2){
            $userreal->review_status = 1;
        }else{
            $userreal->review_status = 1;
        }
        try{
            $userreal->save();
            return $this->success('操作成功');
        }catch (\Exception $exception){
            return $this->error($exception->getMessage());
        }
    }

}
?>