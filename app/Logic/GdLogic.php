<?php


namespace App\Logic;


use App\GdOrder;
use App\MicroOrder;
use Illuminate\Support\Facades\DB;

class GdLogic
{
    public static function followMicroTrade($gd_order_id,$match_id,$currency_id,$seconds,$price,$type){

        //判断当天跟单数是否超标
        $gdOrder = GdOrder::find($gd_order_id);
        if($gdOrder->status != 1){
            return false;
        }
        $dayCount = MicroOrder::where('user_id',$gdOrder->uid)->where('gd_order_id',$gd_order_id)->sum('number');
        if($dayCount + $gdOrder->value > $gdOrder->day_max_value){
            return false;
        }
        $order_data = [
            'user_id' => $gdOrder->uid,
            'type' => $type,
            'match_id' => $match_id,
            'currency_id' => $currency_id,
            'seconds' => $seconds,
            'price' => $price,
            'number' => $gdOrder->value,
            'use_insurance' => 0,
        ];
        // var_dump($order_data);exit;
        try{
            $order = MicroTradeLogic::addOrder($order_data);
            return true;
        }catch (\Exception $e){
            // var_dump($e->getMessage());
            return false;
        }
    }
}
