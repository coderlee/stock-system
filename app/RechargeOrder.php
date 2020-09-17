<?php
/**
 * Created by PhpStorm.
 * User: swl
 * Date: 2018/7/3
 * Time: 10:23
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Session;
class RechargeOrder extends Model
{
    protected $table = 'recharge_order';
    public $timestamps = false;
    /* protected $hidden = [];
     protected $appends = ['account'];

     public function getCreateTimeAttribute()
    {
        $value = $this->attributes['create_time'];
        return $value ? date('Y-m-d H:i:s', $value ) : '';
    }
    public function getAccountAttribute()
    {
    
        $res=$this->belongsTo('App\Users', 'user_id', 'id')->value('phone');
        if(empty($res)){
             $res=$this->belongsTo('App\Users', 'user_id', 'id')->value('email');
        }
        return $res;
        
    }
    public function user()
    {
        return $this->belongsTo('App\Users', 'user_id', 'id');
    }   */
    public static function create_order_sn()
    {
        // return session('user_id');
        mt_srand((double) microtime() * 1000000);

        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
}