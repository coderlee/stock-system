<?php

namespace App\Http\Controllers\Api;

use App\Setting;
use App\Users;
use App\Utils\RPC;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SmsController extends Controller
{
    private $_sms_ip_check_expire_time = 60;
    /**
     * 发送短信
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {
        $ALIYUN_SMS_AK = env("ALIYUN_SMS_AK");
        $ALIYUN_SMS_AS = env("ALIYUN_SMS_AS");
        $ALIYUN_SMS_SIGN_NAME = env("ALIYUN_SMS_SIGN_NAME");
        $ALIYUN_SMS_VARIABLE = env("ALIYUN_SMS_VARIABLE");  //内容变量
        $tplId = env('ALIYUN_SMS_CODE');                //模版ID 模版CODE 格式为 SMS_140736882

        if(empty($tplId) || empty($ALIYUN_SMS_AK) || empty($ALIYUN_SMS_AS) || empty($ALIYUN_SMS_SIGN_NAME) || empty($ALIYUN_SMS_VARIABLE))
            return $this->error('系统配置错误，请联系系统管理员');

        Config::set("aliyunsms.access_key",$ALIYUN_SMS_AK);
        Config::set("aliyunsms.access_secret",$ALIYUN_SMS_AS);
        Config::set("aliyunsms.sign_name",$ALIYUN_SMS_SIGN_NAME);
        $mobile = Input::get('mobile','');
        if(empty($mobile))
            return $this->error('手机号不能为空');

        //检查1分钟内该ip是否发送过验证码
//        if ($this->checkSmsIp($request->ip().$mobile)) {
//            return $this->error('验证码发送过于频繁');
//        }

        $verification_code = $this->createSmsCode(6);
        $params = [
            $ALIYUN_SMS_VARIABLE => $verification_code
        ];

        try{
            $smsService = \App::make('Curder\LaravelAliyunSms\AliyunSms');
            $return = $smsService->send(strval($mobile), $tplId , $params);

            if($return->Message == "OK"){
                //记入session
                session(['sms_captcha'=>$verification_code]);
                session(['sms_mobile'=> $mobile]);

                //设置缓存key
//                $this->setSmsIpKey($request->ip().$mobile, $mobile);
                return $this->success("发送成功");
            }else{
                return $this->error($return->Message);
            }
        }catch (\ErrorException $e){
            return $this->error($e->getMessage());
        }
    }


    /**
     * 短信宝发送短信
     */
    public function smsBaoSend(Request $request){
        $mobile = $request->get('user_string');
        if (empty($mobile)) return $this->error('电话不能为空');
        $type = $request->get('type');//
        if ($type == 'forget') {
            $user = Users::getByString($mobile);
            if (empty($user)) return $this->error('账号错误');
        }else{
            $user = Users::getByString($mobile);
            if (!empty($user)) return $this->error('账号已存在');
        }
        
        /* $user = Users::getByString($mobile);
        if(!empty($user)) return $this->error('账号已存在'); */
        $username = Setting::getValueByKey('smsBao_username','shiwenlong');
        $password = Setting::getValueByKey('password','swl910101');
        if (empty($mobile))
            return $this->error('请填写手机号');
        $verification_code = $this->createSmsCode(4);
        $content = '【MTS】您的验证码为 ['.$verification_code.']，请勿泄漏。';
        $api = 'http://api.smsbao.com/sms';
        $send_url = $api."?u=".$username."&p=".md5($password)."&m=".$mobile."&c=".urlencode($content);
        $return_message = RPC::apihttp($send_url);
        if ($return_message == 0){
            session(['code'=> $verification_code]);
            return $this->success('发送成功');
        }else{
            $statusStr = array(
                "-1" => "参数不全",
                "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
                "30" => "密码错误",
                "40" => "账号不存在",
                "41" => "余额不足",
                "42" => "帐户已过期",
                "43" => "IP地址限制",
                "44" => "账号被禁用",
                "50" => "内容含有敏感词",
            );
            return $this->error($statusStr[$return_message]);
        }
    }

    /**
     * 检查1分钟内$ip是否发送过验证码
     * @param $ip
     * @return bool|\Illuminate\Http\JsonResponse
     */
    private function checkSmsIp($ip)
    {
        if (empty($ip)) {
            return $this->error('ip参数不正确');
        }

        return $this->checkSmsIpKey($ip);
    }

    /**
     * 生成短信验证码
     * @param int $num  验证码位数
     * @return string
     */
    public function createSmsCode($num=6) {
        //验证码字符数组
        $n_array = range(0, 9);
        //随机生成$num位验证码字符
        $code_array = array_rand($n_array, $num);
        //重新排序验证码字符数组
        shuffle( $code_array );
        //生成验证码
        $code = implode('', $code_array);
        return $code;
    }

    /**
     * 设置sms发送短信Ip缓存限制
     * @param $ip
     * @param $mobile
     */
    public function setSmsIpKey($ip, $mobile)
    {
        $key = Config::get('cache.keySmsIpCheck').$ip;
        Redis::setex($key,$this->_sms_ip_check_expire_time,$mobile);//已发送

    }

    /**
     * 检查sms发送短信Ip缓存限制
     * @param $ip
     * @return bool
     */
    public function checkSmsIpKey($ip)
    {
        $key = Config::get('cache.keySmsIpCheck').$ip;

        if (Redis::exists($key)){
            return true;
        }
        return false;
    }

    /**
     * 发送邮箱验证 composer 安装的phpmailer
     */
    public function sendMail(Request $request)
    {
        $email = $request->get('user_string');
        $type = $request->get('type');
        if (empty($email)) return $this->error('邮箱不能为空');

        if ($type == 'forget') {
            $user = Users::getByString($email);
            if (empty($user)) return $this->error('账号错误');
        } else {
            $user = Users::getByString($email);
            if (!empty($user)) return $this->error('账号已存在');
        } 
        //  从设置中取出值
        $username = Setting::getValueByKey('phpMailer_username', '862917967@qq.com');
        $host = Setting::getValueByKey('phpMailer_host', 'smtp.qq.com');
        $password = Setting::getValueByKey('phpMailer_password', 'nyknebsqlqqobbce');
        $port = Setting::getValueByKey('phpMailer_port', 465);
        //实例化phpMailer
        try{
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->CharSet = "utf-8";
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = "ssl";
            $mail->Host = $host;
            $mail->Port = 465;
            $mail->Username = $username;
            $mail->Password = $password;//去开通的qq或163邮箱中找,这里用的不是邮箱的密码，而是开通之后的一个token
            $mail->setFrom("862917967@qq.com", "[必火]");//设置邮件来源  //发件人
            $mail->Subject = "邮箱验证码"; //邮件标题
            $code = $this->createSmsCode(4);
            $mail->MsgHTML('您的郵箱驗証碼爲'.'【'. $code.'】' );   //邮件内容
            $mail->addAddress($email);  //收件人（用户输入的邮箱）
            $res = $mail->send();
            if ($res) {
                session(['code' => $code]);
                return $this->success('发送成功');
            } else {
                return $this->error('操作错误');
            }
        }catch (\Exception $exception){
            return $this->error($exception->getMessage());
        }

    }  
}
