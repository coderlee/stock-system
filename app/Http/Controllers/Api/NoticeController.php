<?php


namespace App\Http\Controllers\Api;


use App\AccountLog;
use App\LegalDealSend;
use App\Logic\WalletLogic;
use App\MarketHour;
use App\Setting;
use App\Users;
use App\UsersWallet;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Jobs\ZTPayAddressWithdraw;
use Illuminate\Support\Carbon;

class NoticeController extends Controller
{
    public function walletNotify(Request $request){

        $param = $request->getContent();
        $param = json_decode($param,true);
        $data = $param['data'];
        $sign = $param['sign'];
        if(!$data || !$sign){
            // var_dump($request->getContent());
            // log_exception('钱包回调请求数据异常',$param);
                return $this->error('钱包回调请求数据异常');
        }
        $data['appid'] = WalletLogic::$appId;
        if(WalletLogic::getSign($data) != $sign){
            // log_exception('钱包签名异常',$param);
            return $this->error('钱包签名异常');
        }
        if($data['type'] == 2){
            //充值转出成功后 删除转出队列
            WalletLogic::delWithdrawQueue($data['from']);
            echo 'success';exit;
        }
        $address = $data['to'];

        $amount = $data['amount'];
        //先查询是否有值
        $record = DB::table('ztpay_log')->where('unique_key',$data['hash'])->first();
        if(!$record){
            if(WalletLogic::isChangeAddress($address)){
                echo 'success';exit; // 过滤掉充值零钱的消息
            }
            $legal = UsersWallet::where('address',$address)->orWhere('address_2',$address)
                ->lockForUpdate()
                ->first();
            if(!$legal){
                // log_exception('找不到钱包',$param);
                return $this->error('找不到钱包');
            }
            DB::beginTransaction();
            try{

                change_wallet_balance(
                    $legal,1,
                    $amount,
                    AccountLog::WALLET_CURRENCY_IN,
                    '充币记录',
                    false,
                    0,
                    0,
                    serialize([
                        'address_from' => $data['from'],
                        'address_to' => $data['to']
                    ]),
                    false,
                    true
                );
                DB::table('ztpay_log')->insert(['unique_key'=>$data['hash'],'body' => json_encode($param),'created_at'=>date("Y-m-d H:i:s")]);
                DB::commit();
            }catch (\Exception $e){
                DB::rollBack();
                throw $e;
            }
        }

//        $amount = bc_sub($data['amount'],$data['fee_amount'],8);

        try{
//            ZTPayAddressWithdraw::dispatch($address)->onConeection('redis')->onQueue('withdraw_coin')->delay(Carbon::now()->addMinutes(10));
        }catch (\Exception $e){
            // throw $e;
        }
        echo 'success';

        exit;
        return $this->success('');
    }

    public function test(){

        ZTPayAddressWithdraw::dispatch('0x532849ef5eec79dc6b6b81d945543690f5b0e031')->onConnection('redis')->onQueue('withdraw_coin')->delay(Carbon::now()->addMinutes(10));

        exit;

        $data = [
            'appid' => WalletLogic::$appId,
            'method' => 'get_balance',
            'name' => 'ETH',
            'address' => '0x9e7468c1acb6f6c47e01660f0909cac286f6da1c'
        ];
        $data['sign'] = WalletLogic::getSign($data);
        $http_client = new Client();
        $response = $http_client->post('https://sapi.ztpay.org/api/v2', [
            'form_params' => $data
        ]);
        $result = json_decode($response->getBody()->getContents());
        return $this->success($result);
    }
}
