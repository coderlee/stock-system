<?php
/*
 本代码由 PHP代码加密工具 Xend [专业版](Build 5.05.55) 创建
 创建时间 2020-06-24 11:44:51
 技术支持 QQ:30370740 Mail:support@phpXend.com
 严禁反编译、逆向等任何形式的侵权行为，违者将追究法律责任
*/

namespace App\Console\Commands;if(!defined("A_A_AA_A_A_"))define("A_A_AA_A_A_","A_A_AA_A_AA");$GLOBALS[A_A_AA_A_A_]=explode("|F|Q|=", "A_A_AA___AA");if(!defined("A_A_AA__AA_"))define("A_A_AA__AA_","A_A_AA__AAA");$GLOBALS[A_A_AA__AA_]=explode("|i|0|y", "开始任务|i|0|yqueues:lever:update|i|0|y结束任务");if(!defined($GLOBALS[A_A_AA_A_A_]{0}))define($GLOBALS[A_A_AA_A_A_]{0}, ord(2));use Illuminate\Console\Command;use Illuminate\Support\Facades\DB;use Illuminate\Support\Facades\Log;use App\{LeverTransaction};use App\Jobs\LeverClose;class UpdateLever extends Command{protected $signature="\x72\x65\x6D\x6F\x76\x65\x5F\x74\x61\x73\x6B";protected $description="\xE7\xA7\xBB\xE9\x99\xA4\xE7\xA7\xAF\xE5\x8E\x8B\xE4\xBB\xBB\xE5\x8A\xA1";public function handle(){$this->comment($GLOBALS[A_A_AA__AA_]{00});\Illuminate\Support\Facades\Redis::del($GLOBALS[A_A_AA__AA_][1]);$this->comment($GLOBALS[A_A_AA__AA_][2]);}}
?>