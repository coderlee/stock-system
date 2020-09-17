<?php
/*
 本代码由 PHP代码加密工具 Xend [专业版](Build 5.05.55) 创建
 创建时间 2020-06-12 11:37:23
 技术支持 QQ:30370740 Mail:support@phpXend.com
 严禁反编译、逆向等任何形式的侵权行为，违者将追究法律责任
*/

namespace App\Jobs;if(!defined("AA__A_A"))define("AA__A_A","AA__AA_");$GLOBALS[AA__A_A]=explode("|/|A|?", "A_A_AAA");if(!defined("AA____A"))define("AA____A","AA___A_");$GLOBALS[AA____A]=explode("|+|9|B", "match_id|+|9|Bclose");if(!defined($GLOBALS[AA__A_A][00]))define($GLOBALS[AA__A_A][00], ord(6));use Illuminate\Bus\Queueable;use Illuminate\Queue\SerializesModels;use Illuminate\Queue\InteractsWithQueue;use Illuminate\Contracts\Queue\ShouldQueue;use Illuminate\Foundation\Bus\Dispatchable;use Illuminate\Support\Carbon;use App\Logic\MicroTradeLogic;class HandleMicroTrade implements ShouldQueue{use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;protected $klineData=[];public function __construct($kline_data){unset($A_AA_A_);unset($V36tI8N);$this->klineData=$kline_data;}public function handle(){$V368N=Carbon::now()->toDateTimeString() . PHP_EOL;unset($V36tI8O);$A_AAA_A=$V368N;echo $A_AAA_A;unset($A_AAAA_);unset($V36tI8N);$A_AAAAA=$this->klineData[$GLOBALS[AA____A]{0x0}];unset($A_AAAA_);unset($V36tI8N);$AA_____=$this->klineData[$GLOBALS[AA____A][1]];MicroTradeLogic::newPrice($A_AAAAA,$AA_____);MicroTradeLogic::close($A_AAAAA);}}
?>