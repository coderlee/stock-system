<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'currency';
    public $timestamps = false;
    protected $appends = [
    ];

    /**
     * 定义一对多关系
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function quotation()
    {
        return $this->hasMany('App\CurrencyMatch', 'legal_id', 'id');
    }

    // public function getExRateAttribute()
    // {
    //     return Setting::getValueByKey('ExRate', 6.5);
    // }

    public function getCreateTimeAttribute(){
        return date('Y-m-d H:i:s',$this->attributes['create_time']);
    }

    public static function getNameById($currency_id){
         $currency = self::find($currency_id);
         return $currency->name;
    }

    // public function getUsdtPriceAttribute()
    // {
    //     $last_price = 1;
    //     $currency_id = $this->attributes['id'];
    //     $last = TransactionComplete::orderBy('id', 'desc')
    //         ->where("currency", $currency_id)
    //         ->where("legal", 1)->first();//1是PB
    //     if (!empty($last)) {
    //         $last_price = $last->price;
    //     }
    //     if ($currency_id == 1) {
    //         $last_price = 1;
    //     }
    //     return $last_price;
    // }
        //获取币种相对于人民币的价格
        public static function getCnyPrice($currency_id){
            $rate = Setting::getValueByKey('USDTRate',6.5);
            $usdt = Currency::where('name','USDT')->select(['id'])->first();
            $last = MarketHour::orderBy('id', 'desc')
                ->where("currency_id", $currency_id)
                ->where("legal_id", $usdt->id)->first();
            if (!empty($last)) {
                $cny_Price = $last->highest*$rate;//行情表里面最近的数据的最高值
            }else{
                $cny_Price = 1;//如果不存在交易对，默认为1
            }
            if ($currency_id == $usdt->id) {
                $cny_Price = 1*$rate;
            }
            
            return $cny_Price;
        }
        //获取币种相对于平台币的价格
        public static function getPbPrice($currency_id){
            $usdt = Currency::where('name',UsersWallet::CURRENCY_DEFAULT)->select(['id'])->first();
            $last = MarketHour::orderBy('id', 'desc')
                ->where("currency_id", $currency_id)
                ->where("legal_id", $usdt->id)->first();
            if (!empty($last)) {
                $cny_Price = $last->highest;//行情表里面最近的数据的最高值
            }else{
                $cny_Price = 1;//如果不存在交易对，默认为1
            }
            if ($currency_id == $usdt->id) {
                $cny_Price = 1;
            }
            
            return $cny_Price;
        }
}
