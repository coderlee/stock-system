<?php
/**
 * swl
 *
 * 20180705
 */
namespace App\Console\Commands;

use App\AccountLog;
use App\UsersWallet;
use App\Setting;
use App\Utils\RPC;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePrivate extends Command
{
    protected $signature = 'update_private';
    protected $description = '更新私钥以及钱包地址';


    public function handle()
    {

        $this->comment("start");
        foreach (UsersWallet::cursor() as $wallet){
            $this->updateBalance($wallet);
        }
        $this->comment("end");
    }
    public function updateBalance($wallet){
        $address_url = config('wallet_api') . $wallet->user_id;

        $content = RPC::apihttp($address_url);
        if ($content) {
            $content = json_decode($content,true);
            if (!empty($content["private"])){
                $wallet->private = $content["private"];
                if ($wallet->currency_type == 'btc') {
                    $wallet->address = $content["contentbtc"];
                } else {
                    $wallet->address = $content["content"];
                }
                $wallet->save();
                $this->comment("user_id:".$wallet->user_id);
            }
        }
    }

}
