<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function ajaxReturn($data='', $info='', $status=0)
    {
        $result = array(
            'data' => $data,    //数据或其它信息
            'info' => $info,   //提示信息
            'status' => $status   //1成功， 0失败
        );
        return response()->json($result);
    }
    public function error($message)
    {
        return response()->json(['type' => 'error', 'message' => $message]);
    }
    public function success($message)
    {
        return response()->json(['type' => 'ok', 'message' => $message]);
    }


    public function layuiData($paginateObj)
    {
        return response()->json([
            'code' => 0,
            'msg' => '',
            'count' => $paginateObj->total(),
            'data' => $paginateObj->items(),
        ]);
    }

    public function layui_table($pagination)
    {
        $count = $pagination->total();
        $items = $pagination->items();
        $result = array(
            'code' => 0,
            'msg' => '',
            'count' => $count,
            'data' => $items,
        );
        return json_encode($result);
    }
}