<?php

/**
 * Created by PhpStorm.
 * User: swl
 * Date: 2018/7/3
 * Time: 10:23
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use App\Utils\RPC;
use Illuminate\Support\Facades\Config;

class UsersWallet extends Model
{
    protected $table = 'users_wallet';
    public $timestamps = false;
    /*const CREATED_AT = 'create_time';*/
    const CURRENCY_DEFAULT = "USDT";
    protected $appends = [
        'currency_name',
        'currency_type',
        'is_legal',
        'is_lever',
        'cny_price',
        'pb_price',
    ];

    public function getCreateTimeAttribute()
    {
        $value = $this->attributes['create_time'];
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }
    public function getCurrencyTypeAttribute()
    {
        return $this->hasOne('App\Currency', 'id', 'currency')->value('type');
    }
    // public function getExrateAttribute()
    // {
    //     // $value = $this->attributes['create_time'];
    //     return $ExRate = Setting::getValueByKey('ExRate',6.5);;
    // }

    public function getCurrencyNameAttribute()
    {
        return $this->hasOne('App\Currency', 'id', 'currency')->value('name');
    }

    public function getIsLegalAttribute()
    {
        return $this->hasOne('App\Currency', 'id', 'currency')->value('is_legal');
    }

    public function getIsLeverAttribute()
    {
        return $this->hasOne('App\Currency', 'id', 'currency')->value('is_lever');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency', 'currency', 'id');
    }

    public static function makeWallet($user_id)
    {
        $currency = Currency::all();
        $address_url = Config::get('wallet_api') . $user_id;
        exit($address_url);
        $address = RPC::apihttp($address_url);
        $address = @json_decode($address, true);

        foreach ($currency as $key => $value) {
            $userWallet = new self();
            $userWallet->user_id = $user_id;
            if ($value->type == 'btc') {
                $userWallet->address = $address["contentbtc"];
            } else {
                $userWallet->address = $address["content"];
            }
            $userWallet->currency = $value->id;
            $userWallet->create_time = time();
            $userWallet->save();//默认生成所有币种的钱包
        }
    }

    // public function getUsdtPriceAttribute()
    // {
    //     $last_price = 0;
    //     $currency_id = $this->attributes['currency'];
    //     $last = TransactionComplete::orderBy('id', 'desc')
    //         ->where("currency", $currency_id)
    //         ->where("legal", 1)->first();//1是pb
    //     if (!empty($last)) {
    //         $last_price = $last->price;
    //     }
    //     if ($currency_id == 1) {
    //         $last_price = 1;
    //     }
    //     return $last_price;
    // }
    public function getPbPriceAttribute()
    {
        $currency_id = $this->attributes['currency'];
        return Currency::getPbPrice($currency_id);
       
    }
    public function getCnyPriceAttribute()
    {
        $currency_id = $this->attributes['currency'];
        return Currency::getCnyPrice($currency_id);
       
    }
}
