<?php
/**
 * swl
 *
 * 20180705
 */
namespace App\Console\Commands;

use App\AccountLog;
use App\UsersWallet;
use App\Users;
use App\Utils\RPC;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Currency;

class UpdateBalance extends Command
{
    protected $signature = 'update_balance';
    protected $description = '更新用户余额';


    public function handle()
    {
        $datas = UsersWallet::get();

        $this->comment("start");
        foreach ($datas as $d){

            $this->updateBalance($d);
        }
        $this->comment("end");
    }
    public function updateBalance($data){
        $currency = Currency::find($data->currency);
        if(empty($currency)){
            return false;
        }
        if (!empty($data->address)){
            $address = $data->address;
            if($currency->type=='eth'){
                $url = "https://api.etherscan.io/api?module=account&action=balance&address=".$address."&tag=latest&apikey=YourApiKeyToken";
            }else if($currency->type=='erc20'){
                $url = "https://api.etherscan.io/api?module=account&action=tokenbalance&contractaddress=".$currency->contract_address."&address=".$address."&tag=latest&apikey=579R8XPDUY1SHZNEZP9GA4FEF1URNC3X45".rand(1,10000);
            }else if($currency->type=='btc'){
                $url ='';
            }

            if($currency->type!='btc'){//类型等于btc的币种暂时不更新余额ldh
                $content = RPC::apihttp($url);
                if ($content){
                    DB::beginTransaction();
                    try {
                        $content = json_decode($content,true);
                        if (isset($content["message"]) && $content["message"] == "OK"){
                            $content["result"] = $content["result"] / 1000000000000000000;

                            if ($content["result"] > $data->old_balance){
                                $result = $content["result"] - $data->old_balance;
                                $data->change_balance = $data->change_balance + $result;
                                $data->old_balance = $content["result"];
                                $data->save();
                                AccountLog::insertLog(array("user_id"=>$data->user_id,"value"=>$result,"type"=>AccountLog::ETH_EXCHANGE,"info"=>"充币增加",'currency'=>$currency->id));
                                $this->comment($data->user_id."：增加有效可用余额".$result);
                            }else{
                                $this->comment($data->user_id."无增加");
                            }
                        }
                        DB::commit();
                    } catch (\Exception $ex) {
                        DB::rollback();
                        $this->comment($ex->getMessage());
                    }
                }
            }

        }
    }

}
