<?php

namespace App\Http\Controllers\Api;
if (!defined("A_A__A_AA_")) define("A_A__A_AA_", "A_A__A_AAA");
$GLOBALS[A_A__A_AA_] = explode("|A|5|x", "AAA__A___");
if (!defined($GLOBALS[A_A__A_AA_][0])) define($GLOBALS[A_A__A_AA_][0], ord(6));
if (!defined("A_A__A_A__")) define("A_A__A_A__", "A_A__A_A_A");
$GLOBALS[A_A__A_A__] = explode("|s|H|`", "A__A__AAA_|s|H|`preg_match|s|H|`A__A__AAAA|s|H|`in_array|s|H|`A__A_A____|s|H|`mkdir|s|H|`A__A_A___A|s|H|`file_put_contents|s|H|`A__A_A__A_|s|H|`base64_decode|s|H|`A__A_A__AA|s|H|`str_replace|s|H|`A_____AAAA|s|H|`strtolower|s|H|`A____A____|s|H|`strrpos|s|H|`A____A___A|s|H|`time|s|H|`A____A__A_|s|H|`rand|s|H|`A____A__AA|s|H|`iconv|s|H|`A____A_A__|s|H|`file_exists|s|H|`A________A|s|H|`substr|s|H|`AAA_A__AA|s|H|`date");
$GLOBALS[$GLOBALS[A_A__A_A__][00]] = $GLOBALS[A_A__A_A__]{0x1};
$GLOBALS[$GLOBALS[A_A__A_A__]{2}] = $GLOBALS[A_A__A_A__][0x3];
$GLOBALS[$GLOBALS[A_A__A_A__]{4}] = $GLOBALS[A_A__A_A__]{05};
$GLOBALS[$GLOBALS[A_A__A_A__][6]] = $GLOBALS[A_A__A_A__]{7};
$GLOBALS[$GLOBALS[A_A__A_A__][0x8]] = $GLOBALS[A_A__A_A__]{9};
$GLOBALS[$GLOBALS[A_A__A_A__][10]] = $GLOBALS[A_A__A_A__][013];
$GLOBALS[$GLOBALS[A_A__A_A__][014]] = $GLOBALS[A_A__A_A__]{015};
$GLOBALS[$GLOBALS[A_A__A_A__]{14}] = $GLOBALS[A_A__A_A__]{017};
$GLOBALS[$GLOBALS[A_A__A_A__]{16}] = $GLOBALS[A_A__A_A__][17];
$GLOBALS[$GLOBALS[A_A__A_A__]{0x12}] = $GLOBALS[A_A__A_A__]{19};
$GLOBALS[$GLOBALS[A_A__A_A__][20]] = $GLOBALS[A_A__A_A__]{0x15};
$GLOBALS[$GLOBALS[A_A__A_A__]{026}] = $GLOBALS[A_A__A_A__]{027};
$GLOBALS[$GLOBALS[A_A__A_A__][0x18]] = $GLOBALS[A_A__A_A__][25];
$GLOBALS[$GLOBALS[A_A__A_A__][0x1A]] = $GLOBALS[A_A__A_A__]{0x1B};

use Illuminate\Http\Request;
use App\Bank;
use App\FalseData;
use App\Market;
use App\Setting;
use App\HistoricalData;
use App\Utils\RPC;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;

class DefaultController extends Controller
{

    public function falseData()
    {
        $limit = Input::get('limit', '12');
        $page = Input::get('page', '1');

        $old = date("Y-m-d", strtotime("-1 day"));
        $old_time = strtotime($old);
        $time = strtotime(date("Y-m-d"));

        $yesterday = FalseData::where('time', ">", $old_time)->where("time", "<", $time)->sum('price');
        $today = FalseData::where('time', ">", $time)->sum('price');

        $data = FalseData::orderBy('id', 'DESC')->paginate($limit);

        return $this->success(array(
            "data" => $data->items(),
            "limit" => $limit,
            "page" => $page,
            "yesterday" => $yesterday,
            "today" => $today,
        ));
    }


    public function quotation()
    {
        $result = Market::limit(20)->get();
        return $this->success(
            array(
                "coin_list" => $result
            )
        );
    }

    public function historicalData()
    {
        $day = HistoricalData::where("type", "day")->orderBy('id', 'asc')->get();
        $week = HistoricalData::where("type", "week")->orderBy('id', 'asc')->get();
        $month = HistoricalData::where("type", "month")->orderBy('id', 'asc')->get();

        return $this->success(
            array(
                "day" => $day,
                "week" => $week,
                "month" => $month
            )
        );
    }

    public function quotationInfo()
    {
        $id = Input::get("id");
        if (empty($id)) return $this->error("参数错误");

//        $coin_list = RPC::apihttp("https://api.coinmarketcap.com/v2/ticker/".$id."/");
        $coin_list = Market::find($id);

//        $coin_list = @json_decode($coin_list,true);

        return $this->success($coin_list);
    }

    public function dataGraph()
    {
        $data = Setting::getValueByKey("chart_data");
        if (empty($data)) return $this->error("暂无数据");

        $data = json_decode($data, true);
        return $this->success(
            array(
                "data" => array(
                    $data["time_one"], $data["time_two"], $data["time_three"], $data["time_four"], $data["time_five"], $data["time_six"], $data["time_seven"]
                ),
                "value" => array(
                    $data["price_one"], $data["price_two"], $data["price_three"], $data["price_four"], $data["price_five"], $data["price_six"], $data["price_seven"]
                ),
                "all_data" => $data
            )
        );
    }

    public function index()
    {
        $coin_list = RPC::apihttp("https://api.coinmarketcap.com/v2/ticker?limit=10");
        $coin_list = @json_decode($coin_list, true);

        if (!empty($coin_list["data"])) {
            foreach ($coin_list["data"] as &$d) {
                if ($d["total_supply"] > 10000) {
                    $d["total_supply"] = substr($d["total_supply"], 0, -4) . "万";
                }
            }
        }
        return $this->success(
            array(
                "coin_list" => $coin_list["data"]
            )
        );
    }

    public function upload()
    {
        if (!empty($_FILES["file"]["error"])) {
            return $this->error($_FILES["file"]["error"]);
        } else {

//            if($_FILES["file"]["size"] > 204800){
//                return $this->error("文件大小超出");
//            }
            if ($_FILES["file"]["size"] > 10485760) {
                return $this->error("文件大小超出");
            }
            // return $this->success($_FILES["file"]["type"]);
            if ($_FILES["file"]["type"] == "image/jpg" || $_FILES["file"]["type"] == "image/png" || $_FILES["file"]["type"] == "image/jpeg") {
                $type = strtolower(substr($_FILES["file"]["name"], strrpos($_FILES["file"]["name"], '.') + 1)); //得到文件类型，并且都转化成小写
                $wenjian_name = time() . rand(0, 999999) . "." . $type;
                //防止文件名重复
                //超哥写的上传路径
                // $filename ="/var/www/html/jnbadmin/public/upload/".$wenjian_name;
                $filename = "./upload/" . $wenjian_name;
                //转码，把utf-8转成gb2312,返回转换后的字符串， 或者在失败时返回 FALSE。
                $filename = iconv("UTF-8", "gb2312", $filename);
                //检查文件或目录是否存在
                if (file_exists($filename)) {
                    return $this->error("该文件已存在");
                } else {
//                     var_dump($filename);die;
                    move_uploaded_file($_FILES["file"]["tmp_name"], $filename);
                    return $this->success(URL("upload/" . $wenjian_name));
                }
            } else {
                return $this->error("文件类型不对");
            }
        }
    }

    public function getVersion()
    {
        $version = Setting::getValueByKey('version', '1.0');
        return $this->success($version);
    }


    public function getBanks()
    {
        $result = Bank::all();
        return $this->success($result);
    }

    public function language(Request $request)
    {
        $L35bN8O = "__file__" == 5;
        if ($L35bN8O) goto L35eWjgx5c;
        $A_A___A__A = "defined";
        $L35eF0 = $A_A___A__A("A_A____AAA");
        $L358M = !$L35eF0;
        if ($L358M) goto L35eWjgx5c;
        $L35vPbN8N = 7 + 1;
        $A_A___A_A_ = "is_array";
        $L35eFbN1 = $A_A___A_A_($L35vPbN8N);
        if ($L35eFbN1) goto L35eWjgx5c;
        goto L35ldMhx5c;
        L35eWjgx5c:
        $A_A___A_AA = "define";
        $L35eF0 = $A_A___A_AA("A_A____AAA", "A_A___A___");
        goto L35x5b;
        L35ldMhx5c:L35x5b:
        $A_A___AA__ = "explode";
        $L35eF0 = $A_A___AA__("|O|L|<", "lang|O|L|<zh");
        unset($L35tI8M);
        $GLOBALS[A_A____AAA] = $L35eF0;
        unset($L35cV1);
        $A_A__A___A = "is_array";
        $L35eF11 = $A_A__A___A($GLOBALS[A_A____AAA]);
        if ($L35eF11) goto L35eWjgx5q;
        unset($L35tIbN95);
        $E34IUUS = false;
        if ($E34IUUS) goto L35eWjgx5q;
        $L35bN93 = E_ERROR - 1;
        unset($L35tIbN94);
        $E34IUUS = $L35bN93;
        if ($E34IUUS) goto L35eWjgx5q;
        goto L35ldMhx5q;
        L35eWjgx5q:
        if (isset($config[0])) goto L35eWjgx5s;
        goto L35ldMhx5s;
        L35eWjgx5s:
        goto E34MrSP3F0;
        $A_A__A__A_ = "is_array";
        $L35eFM13 = $A_A__A__A_($rules);
        if ($L35eFM13) goto L35eWjgx5u;
        goto L35ldMhx5u;
        L35eWjgx5u:
        Route::import($rules);
        goto L35x5t;
        L35ldMhx5u:L35x5t:E34MrSP3F0:
        goto L35x5r;
        L35ldMhx5s:
        goto E34MrSP3F2;
        $L35M96 = $path . EXT;
        $A_A__A__AA = "is_file";
        $L35eFM14 = $A_A__A__AA($L35M96);
        if ($L35eFM14) goto L35eWjgx5w;
        goto L35ldMhx5w;
        L35eWjgx5w:
        $L35M97 = $path . EXT;
        $L35M98 = include $L35M97;
        goto L35x5v;
        L35ldMhx5w:L35x5v:E34MrSP3F2:L35x5r:
        $L35cV1 =& $GLOBALS[A_A____AAA][0x0];
        goto L35x5p;
        L35ldMhx5q:
        goto E34MrSP3F4;
        unset($L35tIM99);
        $A_33 = "php_sapi_name";
        unset($L35tIM9A);
        $A_34 = "die";
        unset($L35tIM9B);
        $A_35 = "cli";
        unset($L35tIM9C);
        $A_36 = "microtime";
        unset($L35tIM9D);
        $A_37 = 1;
        E34MrSP3F4:
        goto E34MrSP3F6;
        unset($L35tIM9E);
        $A_38 = "argc";
        unset($L35tIM9F);
        $A_39 = "echo";
        unset($L35tIM9G);
        $A_40 = "HTTP_HOST";
        unset($L35tIM9H);
        $A_41 = "SERVER_ADDR";
        E34MrSP3F6:
        $L35cV1 = $GLOBALS[A_A____AAA][0x0];
        L35x5p:
        unset($L35cV2);
        $A_A___AA_A = "is_array";
        $L35eF3 = $A_A___AA_A($GLOBALS[A_A____AAA]);
        if ($L35eF3) goto L35eWjgx5e;
        if (is_null(__FILE__)) goto L35eWjgx5e;
        $A_A___AAA_ = "time";
        $L35eFbN4 = $A_A___AAA_();
        $L35bN8N = !$L35eFbN4;
        if ($L35bN8N) goto L35eWjgx5e;
        goto L35ldMhx5e;
        L35eWjgx5e:
        if (isset($_GET)) goto L35eWjgx5g;
        goto L35ldMhx5g;
        L35eWjgx5g:
        $L35zAM7 = array();
        goto E34MrSP3EC;
        $L35M8O = CONF_PATH . $module;
        $L35M8P = $L35M8O . database;
        $L35M8Q = $L35M8P . CONF_EXT;
        unset($L35tIM8R);
        $filename = $L35M8Q;
        E34MrSP3EC:
        goto L35x5f;
        L35ldMhx5g:
        $A_A___AAAA = "strpos";
        $L35eFM8 = $A_A___AAAA($file, ".");
        if ($L35eFM8) goto L35eWjgx5i;
        goto L35ldMhx5i;
        L35eWjgx5i:
        $L35M8S = $file;
        goto L35x5h;
        L35ldMhx5i:
        $L35M8T = APP_PATH . $file;
        $L35M8U = $L35M8T . EXT;
        $L35M8S = $L35M8U;
        L35x5h:
        unset($L35tIM8V);
        $file = $L35M8S;
        $L35M8X = (bool)is_file($file);
        if ($L35M8X) goto L35eWjgx5l;
        goto L35ldMhx5l;
        L35eWjgx5l:
        $L35M8W = !isset(user::$file[$file]);
        $L35M8X = (bool)$L35M8W;
        goto L35x5k;
        L35ldMhx5l:L35x5k:
        if ($L35M8X) goto L35eWjgx5m;
        goto L35ldMhx5m;
        L35eWjgx5m:
        $L35M8Y = include $file;
        unset($L35tIM8Z);
        $L35tIM8Z = true;
        user::$file[$file] = $L35tIM8Z;
        goto L35x5j;
        L35ldMhx5m:L35x5j:L35x5f:
        $L35cV2 =& $GLOBALS[A_A____AAA][0x1];
        goto L35x5d;
        L35ldMhx5e:
        goto E34MrSP3EE;
        foreach ($files as $file) {
            $A_A__A____ = "strpos";
            $L35eFM9 = $A_A__A____($file, CONF_EXT);
            if ($L35eFM9) goto L35eWjgx5o;
            goto L35ldMhx5o;
            L35eWjgx5o:
            $L35M90 = $dir . DS;
            $L35M91 = $L35M90 . $file;
            unset($L35tIM92);
            $filename = $L35M91;
            Config::load($filename, pathinfo($file, PATHINFO_FILENAME));
            goto L35x5n;
            L35ldMhx5o:L35x5n:
        }
        E34MrSP3EE:
        $L35cV2 = $GLOBALS[A_A____AAA][0x1];
        L35x5d:
        $L35hC0 = call_user_func_array(array($request, "get"), array(&$L35cV1, &$L35cV2));
        unset($L35tI8M);
        $A_A____AA_ = $L35hC0;
        session()->put($GLOBALS[A_A____AAA][0x0], $A_A____AA_);
        $L35hC0 = call_user_func_array(array($this, "success"), array(&$A_A____AA_));
        return $L35hC0;
    }

}
