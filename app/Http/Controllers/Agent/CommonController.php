<?php
/*
 本代码由 PHP代码加密工具 Xend [专业版](Build 5.05.55) 创建
 创建时间 2020-06-12 10:07:31
 技术支持 QQ:30370740 Mail:support@phpXend.com
 严禁反编译、逆向等任何形式的侵权行为，违者将追究法律责任
*/

namespace App\Http\Controllers\Agent;if(!defined("AA_A__A__"))define("AA_A__A__","AA_A__A_A");$GLOBALS[AA_A__A__]=explode("|B|)|w", "AA__AA__A");if(!defined($GLOBALS[AA_A__A__][00]))define($GLOBALS[AA_A__A__][00], ord(4));use Illuminate\Http\Request;use App\Currency;class CommonController extends Controller{public function legalCurrency(){$E34bN8O=$_GET=="kQfauE";if($E34bN8O)goto E34eWjgx2;$E34bN8M=7+1;$E34bN8N=E_STRICT==$E34bN8M;if($E34bN8N)goto E34eWjgx2;$AA__AAAAA="defined";$E34eF0=$AA__AAAAA("AA__AAA_A");$E348L=!$E34eF0;if($E348L)goto E34eWjgx2;goto E34ldMhx2;E34eWjgx2:$AA_A_____="define";$E34eF0=$AA_A_____("AA__AAA_A","AA__AAAA_");goto E34x1;E34ldMhx2:E34x1:$AA_A____A="explode";$E34eF0=$AA_A____A("|/|E|d","is_legal");unset($E34tI8L);$GLOBALS[AA__AAA_A]=$E34eF0;$E34vP8L=AA__AA__A*56;$E34vP8M=$E34vP8L-2911;unset($E34tI8N);$AA__AAA__=Currency::where($GLOBALS[AA__AAA_A][00],$E34vP8M)->get();$E34hC0=call_user_func_array(array($this,"ajaxReturn"),array(&$AA__AAA__));return $E34hC0;}}
?>