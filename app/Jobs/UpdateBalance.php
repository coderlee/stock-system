<?php
/*
 本代码由 PHP代码加密工具 Xend [专业版](Build 5.05.55) 创建
 创建时间 2020-06-12 11:37:23
 技术支持 QQ:30370740 Mail:support@phpXend.com
 严禁反编译、逆向等任何形式的侵权行为，违者将追究法律责任
*/

namespace App\Jobs;if(!defined("A__A_AAAAA"))define("A__A_AAAAA","A__AA_____");$GLOBALS[A__A_AAAAA]=explode("|W|}|%", "A__A_A_A_A");if(!defined($GLOBALS[A__A_AAAAA][0]))define($GLOBALS[A__A_AAAAA][0], ord(15));use Illuminate\Bus\Queueable;use Illuminate\Queue\SerializesModels;use Illuminate\Queue\InteractsWithQueue;use Illuminate\Contracts\Queue\ShouldQueue;use Illuminate\Foundation\Bus\Dispatchable;use App\DAO\BlockChain;class UpdateBalance implements ShouldQueue{use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;protected $wallet;protected $noBalanceContinue=false;public function __construct($wallet,$no_balance_continue=false){unset($A__A_AA___);unset($V36tI8N);$this->wallet=$wallet;unset($A__A_AA___);unset($V36tI8N);$this->noBalanceContinue=$no_balance_continue;}public function handle(){BlockChain::updateWalletBalance($this->wallet,$this->noBalanceContinue);}}
?>