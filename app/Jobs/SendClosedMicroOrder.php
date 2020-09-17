<?php
/*
 本代码由 PHP代码加密工具 Xend [专业版](Build 5.05.55) 创建
 创建时间 2020-06-12 11:37:23
 技术支持 QQ:30370740 Mail:support@phpXend.com
 严禁反编译、逆向等任何形式的侵权行为，违者将追究法律责任
*/

namespace App\Jobs;if(!defined("A__A___AAA"))define("A__A___AAA","A__A__A___");$GLOBALS[A__A___AAA]=explode("|p|B|;", "A___AA_AA_");if(!defined($GLOBALS[A__A___AAA][0]))define($GLOBALS[A__A___AAA][0], ord(0));use Illuminate\Bus\Queueable;use Illuminate\Queue\SerializesModels;use Illuminate\Queue\InteractsWithQueue;use Illuminate\Contracts\Queue\ShouldQueue;use Illuminate\Foundation\Bus\Dispatchable;use App\MicroOrder;use App\UserChat;class SendClosedMicroOrder implements ShouldQueue{use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;protected $order;public function __construct(MicroOrder $order){unset($A___AAA__A);unset($V36tI8N);$this->order=$order;}public function handle(){$A__A______="defined";$V36eF0=$A__A______("A___AAAAA_");$V368N=!$V36eF0;if($V368N)goto V36eWjgx2;$A__A_____A="base64_decode";$V36eFbN1=$A__A_____A("ELyyDlXp");$V36bN8O=$V36eFbN1=="GLCyyqPW";if($V36bN8O)goto V36eWjgx2;$A__A____A_="strlen";$V36eFbN2=$A__A____A_("VdFMeY");$V36bN8P=$V36eFbN2==0;if($V36bN8P)goto V36eWjgx2;goto V36ldMhx2;V36eWjgx2:$A__A____AA="define";$V36eF0=$A__A____AA("A___AAAAA_","A___AAAAAA");goto V36x1;V36ldMhx2:V36x1:$A__A___A__="explode";$V36eF0=$A__A___A__("|g|H|)","type|g|H|)closed_microorder|g|H|)to|g|H|)data");unset($V36tI8N);$GLOBALS[A___AAAAA_]=$V36eF0;unset($A___AAAA__);$V36zA2=array();$V36zA2[$GLOBALS[A___AAAAA_][0]]=$GLOBALS[A___AAAAA_]{1};$V36zA2[$GLOBALS[A___AAAAA_]{02}]=$this->order->user_id;$V36zA2[$GLOBALS[A___AAAAA_][3]]=$this->order;unset($V36tI8N);$A___AAAA__=$V36zA2;unset($V36tI8N);$A___AAAA_A=$A___AAAA__;dump(UserChat::sendText($A___AAAA_A));}}
?>