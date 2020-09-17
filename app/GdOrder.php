<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class GdOrder extends Model
{
    protected $table = 'gd_order';
    const STATUS_ACTIVE =1;

    public function getMyFollower($uid){
        $res = $this->where([
            'status' => self::STATUS_ACTIVE,
            'gd_user_id' => $uid
        ])->get();
        return $res;
    }
}