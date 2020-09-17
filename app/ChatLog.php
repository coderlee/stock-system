<?php
/*
 本代码由 PHP代码加密工具 Xend [专业版](Build 5.05.55) 创建
 创建时间 2020-06-12 11:02:20
 技术支持 QQ:30370740 Mail:support@phpXend.com
 严禁反编译、逆向等任何形式的侵权行为，违者将追究法律责任
*/

namespace App;if(!defined("AA__A__AAA_A"))define("AA__A__AAA_A","AA__A__AAAA_");$GLOBALS[AA__A__AAA_A]=explode("|l|t|.", "AA__A__A__A_");if(!defined("AA__A__AA__A"))define("AA__A__AA__A","AA__A__AA_A_");$GLOBALS[AA__A__AA__A]=explode("|||a|~", "to_user");if(!defined("AA__A__A_AAA"))define("AA__A__A_AAA","AA__A__AA___");$GLOBALS[AA__A__A_AAA]=explode("|3|(|W", "from_user");if(!defined("AA__A__A_A_A"))define("AA__A__A_A_A","AA__A__A_AA_");$GLOBALS[AA__A__A_A_A]=explode("|m|~|]", "head_portrait");if(!defined($GLOBALS[AA__A__AAA_A][0x0]))define($GLOBALS[AA__A__AAA_A][0x0], ord(88));use Illuminate\Database\Eloquent\Model;class ChatLog extends Model{protected $table="\x63\x68\x61\x74\x5F\x6C\x6F\x67";protected $appends=["\x66\x72\x6F\x6D\x5F\x75\x73\x65\x72\x5F\x68\x65\x61\x64"];public function getFromUserHeadAttribute(){$Z35hC0=call_user_func_array(array($this,"from_user"),array());$Z35hC0=call_user_func_array(array($Z35hC0,"value"),array($GLOBALS[AA__A__A_A_A]{00}));return $Z35hC0;}public function from_user(){return $this->belongsTo(Users::class,$GLOBALS[AA__A__A_AAA]{0x0});}public function to_user(){return $this->belongsTo(Users::class,$GLOBALS[AA__A__AA__A]{0x0});}}
?>