<?php

namespace App\Console\Commands;

use App\Currency;
use App\Setting;
use App\Users;
use App\UsersWallet;
use App\Utils\RPC;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MakeOneWallet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make_wallet {id : id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '钱包项目上币执行脚本';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $id = $this->argument('id');
        // $is_execute = Setting::getValueByKey('currency_'.$id,0);
        // if ($is_execute == 1){
        //     return $this->error('该币上币脚本正在运行中');
        // }elseif($is_execute == 2){
        //     return $this->error('该币上币脚本已经运行过');
        // }
        Setting::updateValueByKey('currency_'.$id,1);

        DB::beginTransaction();

        try{
            $currency = Currency::find($id);
            if (!empty($currency)){
                $this->info('开始执行上币脚本--'.Carbon::now()->toDateTimeString());
                $count = Users::count();
                $i = 1;
                $this->info('共有 '.$count.' 个用户需要添加新的钱包地址');
                foreach (Users::whereRaw('1')->cursor() as $user){
                   
                    $users_wallet = UsersWallet::where('user_id',$user->id)->where('currency',$id)->first();
                    if (empty($users_wallet)){
                        $this->info('开始生成第 '.$i.'/'.$count.' 个用户的钱包地址,用户 id 为：'.$user->id);
                        //先生成eth的地址
                        $memorizing_words = $user->memorizing_words;
                        $pay_password = $user->pay_password;
                        $eth_address_url = 'http://47.92.171.137:3000/word/generateeth?content='.$memorizing_words.'&password='.$pay_password;
                        $eth_address = RPC::apihttp($eth_address_url);
                        $eth_address = @json_decode($eth_address, true);

                        //生成erc20地址
                        $address_url = 'http://47.92.171.137:3000/word/getaddress_mll?password=swl910101&user_id='.$user->id;
                        $address = RPC::apihttp($address_url);
                        $address = @json_decode($address, true);

                        $userWallet = new UsersWallet();
                        $userWallet->user_id = $user->id;
                        if ($currency->type == 'btc') {
                            $userWallet->address = $address["contentbtc"];
                            $userWallet->eth_address = $eth_address["contentbtc"];
                        } else {
                            $userWallet->eth_address = $eth_address["content"];
                            $userWallet->address = $address["content"];
                        }
                        $userWallet->currency = $currency->id;
                        $userWallet->create_time = time();
                        $userWallet->save();
                    }else{
//                            Setting::updateValueByKey('currency_'.$id,2);
                        DB::rollback();
                        $this->error('第 '.$i.'/'.$count.' 个用户有此币种钱包,用户 id 为：'.$user->id);
                        break;//发现一条全员退出
                    }
                    $i++;
                }
                Setting::updateValueByKey('currency_'.$id,2);
                DB::commit();
                $this->info('执行成功');
            }
        }catch (\Exception $exception){
            DB::rollback();
            return $this->error($exception->getMessage());
        }


    }
}
