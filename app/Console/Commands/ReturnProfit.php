<?php
/*
 本代码由 PHP代码加密工具 Xend [专业版](Build 5.05.55) 创建
 创建时间 2020-06-24 11:44:51
 技术支持 QQ:30370740 Mail:support@phpXend.com
 严禁反编译、逆向等任何形式的侵权行为，违者将追究法律责任
*/

namespace App\Console\Commands;if(!defined("A__A__AA__A"))define("A__A__AA__A","A__A__AA_A_");$GLOBALS[A__A__AA__A]=explode("|I|H|[", "A__A___AA__");if(!defined("A__A__A_A_A"))define("A__A__A_A_A","A__A__A_AA_");$GLOBALS[A__A__A_A_A]=explode("|W|z|x", "lever_transaction|W|z|xuser_id|W|z|xuser_id");if(!defined($GLOBALS[A__A__AA__A]{0x0}))define($GLOBALS[A__A__AA__A]{0x0}, ord(37));use Illuminate\Console\Command;use App\DAO\FactprofitsDAO;use Illuminate\Support\Facades\DB;class ReturnProfit extends Command{protected $signature="\x72\x65\x74\x75\x72\x6E\x3A\x70\x72\x6F\x66\x69\x74";protected $description="\xE8\xBF\x94\xE8\xBF\x98\xE6\x9D\xA0\xE6\x9D\x86\xE4\xBA\xA4\xE6\x98\x93\xE4\xBA\x8F\xE6\x8D\x9F";public function __construct(){parent::__construct();}public function handle(){$G378O=new FactprofitsDAO();unset($G37tI8P);$A__A__A___A=$G378O;unset($G37tI8O);$A__A__A__A_=DB::table($GLOBALS[A__A__A_A_A][0])->select($GLOBALS[A__A__A_A_A][0x1])->groupBy($GLOBALS[A__A__A_A_A]{0x2})->get();foreach($A__A__A__A_ as $A__A__A__AA=>$A__A__A_A__){var_dump($A__A__A_A__->user_id);var_dump($A__A__A___A::Profit_loss_release($A__A__A_A__->user_id));}}}
?>