<?php
/*
 本代码由 PHP代码加密工具 Xend [专业版](Build 5.05.55) 创建
 创建时间 2020-06-24 11:44:51
 技术支持 QQ:30370740 Mail:support@phpXend.com
 严禁反编译、逆向等任何形式的侵权行为，违者将追究法律责任
*/

namespace App\Console\Commands;if(!defined("A_A_AA____A"))define("A_A_AA____A","A_A_AA___A_");$GLOBALS[A_A_AA____A]=explode("|G|f|*", "A_A_A_A_AAA");if(!defined($GLOBALS[A_A_AA____A][0]))define($GLOBALS[A_A_AA____A][0], ord(3));use App\Utils\RPC;use App\Users;use App\UsersWallet;use App\HuobiSymbol;use Illuminate\Console\Command;use GuzzleHttp\Client;class UpdateHuobiSymbol extends Command{protected $signature="\x75\x70\x64\x61\x74\x65\x5F\x48\x75\x6F\x62\x69\x5F\x53\x79\x6D\x62\x6F\x6C";protected $description="\xE6\x9B\xB4\xE6\x96\xB0\xE7\x81\xAB\xE5\xB8\x81\xE4\xBA\xA4\xE6\x98\x93\xE5\xAF\xB9";public function handle(){$G37bN8R="__file__"==5;if($G37bN8R)goto G37eWjgx2;$G37bN8P=5==="";unset($G37tIbN8Q);$G37IItr=$G37bN8P;if($G37IItr)goto G37eWjgx2;$G378O=!defined("A_A_A_AAA_A");if($G378O)goto G37eWjgx2;goto G37ldMhx2;G37eWjgx2:define("A_A_A_AAA_A","A_A_A_AAAA_");goto G37x1;G37ldMhx2:G37x1:unset($G37tI8O);$GLOBALS[A_A_A_AAA_A]=explode("|~|/|3","start1|~|/|3api.huobi.br.com/v1/common/symbols|~|/|3data|~|/|3end");$this->comment($GLOBALS[A_A_A_AAA_A]{00});unset($G37tI8O);$A_A_A_AA_A_=$GLOBALS[A_A_A_AAA_A]{01};$G378O=new Client();unset($G37tI8P);$A_A_A_AA_AA=$G378O;unset($G37tI8O);$A_A_A_AAA__=$A_A_A_AA_AA->get($A_A_A_AA_A_)->getBody()->getContents();unset($G37tI8O);$A_A_A_AAA__=json_decode($A_A_A_AAA__,true);HuobiSymbol::getSymbolsData($A_A_A_AAA__[$GLOBALS[A_A_A_AAA_A]{2}]);$this->comment($GLOBALS[A_A_A_AAA_A]{3});}}
?>