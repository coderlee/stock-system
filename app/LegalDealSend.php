<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LegalDealSend extends Model
{
    protected $table = 'legal_deal_send';
    public $timestamps = false;
    protected $appends = [
        'seller_name',
        'currency_name',
        'limitation',
        'way_name',
        'currency_logo',
    ];

    public function getCreateTimeAttribute(){
        return date('Y-m-d H:i:s',$this->attributes['create_time']);
    }

    public function getSellerNameAttribute(){
        return $this->hasOne('App\Seller','id','seller_id')->value('name');
    }

    public function getCurrencyNameAttribute(){
        return $this->hasOne('App\Currency','id','currency_id')->value('name');
    }

    public function getCurrencyLogoAttribute(){
        return $this->hasOne('App\Currency','id','currency_id')->value('logo');
    }

    public function getLimitationAttribute(){
        return array('min'=>bc_mul($this->attributes['min_number'] , $this->attributes['price'],5),
                     'max'=>bc_mul($this->attributes['max_number'] , $this->attributes['price'],5)
        );
    }

    public function getWayNameAttribute(){
        if ($this->attributes['way'] == 'ali_pay'){
            return '支付宝';
        }elseif ($this->attributes['way'] == 'we_chat'){
            return '微信';
        }elseif ($this->attributes['way'] == 'bank'){
            return Seller::find($this->attributes['seller_id'])->bank_name;
        }
    }

    //该发布信息下是否有未完成的订单
    public static function isHasIncompleteness($id){
        $is_deal = LegalDeal::where('legal_deal_send_id', $id)->pluck('is_sure')->toArray();
//        var_dump($is_deal);die;
        if (in_array(0,$is_deal)) {
            return true;
        }else{
            return false;
        }
    }

    //撤回发布
    public static function sendBack($id){
        //找到撤回的交易记录
        $send = self::find($id);
        try{
            $results = LegalDeal::where('legal_deal_send_id',$id)
                ->where('is_sure',3)->first();
            if (!empty($results)){
                throw new \Exception('该发布信息下有已付款的订单，请确认再撤回');
            }
            $results = LegalDeal::where('legal_deal_send_id',$id)
                ->where('is_sure',0)->get();
            if (!empty($results)){
                foreach ($results as $result){

                    LegalDeal::cancelLegalDealById($result->id,AccountLog::LEGAL_DEAL_AUTO_CANCEL);

                }
                $send->is_done = 1;
                $send->save();
            }
            $legal_send = self::find($id);
            if (!empty($legal_send)){
                $seller = Seller::find($legal_send->seller_id);
                if (!empty($seller)){
                    $seller->increment('seller_balance',$legal_send->surplus_number);
                    AccountLog::insertLog(['user_id'=>$seller->user_id,'value'=>$legal_send->surplus_number,'info'=>'商家主动撤回发布','type'=>AccountLog::SELLER_BACK_SEND]);
                }
            }
            return true;
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
            return false;
        }

    }
}
