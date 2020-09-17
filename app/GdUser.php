<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class GdUser extends Model
{
    protected $table = 'gd_user';
    public $timestamps = false;
    
    public function user(){
        return $this->belongsTo(Users::class,'uid','id');
    }
}