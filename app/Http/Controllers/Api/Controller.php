<?php

namespace App\Http\Controllers\Api;

use App\Users;
use App\Token;
use Closure;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public $user_id;

   /*  public function __construct($_init = true)
    {
        if ($_init) {
            $token = Token::getToken();
            $this->user_id = Token::getUserIdByToken($token);
        }
    } */
    public function error($message)
    {
        header('Content-Type:application/json');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        header('Access-Control-Allow-Headers:x-requested-with,content-type,Authorization');
        return response()->json(['type' => 'error', 'message' => $message]);
    }
    public function success($message)
    {
        header('Content-Type:application/json');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        header('Access-Control-Allow-Headers:x-requested-with,content-type,Authorization');
        return response()->json(['type' => 'ok', 'message' => $message]);
    }

    public function pageDate($paginateObj){
        $results = array('data'=>$paginateObj->items(),'page'=>$paginateObj->currentPage(),'pages'=>$paginateObj->lastPage(),'total'=>$paginateObj->total());
        return $this->success($results);
    }
}