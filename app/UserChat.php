<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Utils\RPC;

class UserChat extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_chat';
    public $timestamps = false;
    protected $appends = ['from_avatar','from_nickname'];

    public function getFromAvatarAttribute()
    {
        return $this->hasOne('App\Users','id','from_user_id')->value('head_portrait');
    }

    public function getFromNicknameAttribute()
    {
        return $this->hasOne('App\Users','id','from_user_id')->value('account_number');
    }

    public static function sendChat($data)
    {
         if (empty($data)) {
            return "fail-kong";
        }
       // echo("sendChat");
        $worker_push_url = config('app.worker_push_url') ?? \Request::server('HTTP_HOST');
        $http_worker_port = config('app.http_worker_port');
        $push_api_url = "http://" . $worker_push_url . ":" . $http_worker_port . '/';
       //$push_api_url = "http://" . $worker_push_url . ":2120";
       // echo($push_api_url);
      //  return RPC::http_post($push_api_url, $data); 
       // print_r($data);
       
       
        // 推送的url地址，使用自己的服务器地址
        $push_api_url = "http://127.0.0.1:2121";
       
         $post_data=$data;
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $push_api_url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        var_export($return);

    }

    public static function getFace($value = "")
    {
        $data = array(
            "0"=>"微笑",
            "1"=>"嘻嘻",
            "2"=>"哈哈",
            "3"=>"可爱",
            "4"=>"可怜",
            "5"=>"抠鼻",
            "6"=>"吃惊",
            "7"=>"害羞",
            "8"=>"挤眼",
            "9"=>"闭嘴",
            "10"=>"鄙视",
            "11"=>"爱你",
            "12"=>"泪",
            "13"=>"偷笑",
            "14"=>"亲亲",
            "15"=>"生病",
            "16"=>"太开心",
            "17"=>"白眼",
            "18"=>"右哼哼",
            "19"=>"左哼哼",
            "20"=>"嘘",
            "21"=>"衰",
            "22"=>"委屈",
            "23"=>"吐",
            "24"=>"哈欠",
            "25"=>"抱抱",
            "26"=>"怒",
            "27"=>"疑问",
            "28"=>"馋嘴",
            "29"=>"拜拜",
            "30"=>"思考",
            "31"=>"汗",
            "32"=>"困",
            "33"=>"睡",
            "34"=>"钱"
        );
        if(empty($value))
            return $data;
        else{
            foreach ($data as $index=>$a){
                if($value == $a){
                    return "<img src=http://m.zhonghexinshang.net.cn/vendor/layim/src/images/face/".$index.".gif>";
                }
            }
        }
    }
}
