<?php
/**
 * 提币控制器
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\UsersWalletOut;
use App\UsersWallet;
use App\AccountLog;
use App\Currency;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Utils\RPC;
class CashbController extends Controller
{
    public function index(){
        return view('admin.cashb.index');
    }
    public function cashbList(Request $request){
        $limit = $request->get('limit', 20);
        $userWalletOut = new UsersWalletOut();
        $userWalletOutList = $userWalletOut->orderBy('id', 'desc')->paginate($limit);
        return $this->layuiData($userWalletOutList);
    }
    public function show(Request $request)
    {
        $id = $request->get('id','');
        if(!$id){return $this->error('参数小错误');}
        $walletout = UsersWalletOut::find($id);
        return view('admin.cashb.edit',['wallet_out'=> $walletout]);
    }
    public function done(Request $request){
        $id = $request->get('id', '');
        $method = $request->get('method', '');
        $notes = $request->get('notes', '');
        if (!$id) {
            return $this->error('参数错误');
        }
        $wallet_out = UsersWalletOut::find($id);
        $number = $wallet_out->number;
        $real_number = $wallet_out->number*(1-$wallet_out->rate/100);
        $user_id = $wallet_out->user_id;
        $currency = $wallet_out->currency;
        $currency_type = $wallet_out->currency_type;
        $user_wallet = UsersWallet::where('user_id',$user_id)->where('currency', $currency)->first();
        $currencyModel = Currency::find($currency);
        $contract_address = $currencyModel->contract_address;
        $total_account =  $currencyModel->total_account;
        $key =  $currencyModel->key;
        DB::beginTransaction();
        try{
            if($method=='done'){//确认提币
                //以太坊确认提币后。返回成功执行后台操作ldh
                $eth_address = $wallet_out->address;
                if($currency_type == 'eth'){
                    if(empty($total_account) ||empty($key)){
                        return $this->error('请检查您的币种设置');
                    }
                    $address_url = 'http://47.92.171.137:8999/web3/transfer?from_address='.$total_account.'&toaddress='.$eth_address.'&transfer_value='.$wallet_out->real_number.'&privates='.$key;
                    // return $address_url;
                }else if($currency_type == 'erc20'){
                    if(empty($total_account) ||empty($key) ||empty($contract_address)){
                        return $this->error('请检查您的币种设置');
                    }
                    $eth_address = substr($eth_address, 2);
                    $address_url = "http://47.92.171.137:8999/web3/transfer/oec?toaddress=".$eth_address."&transfer_value=".$wallet_out->real_number."&contract_address=".$contract_address."&fromeaddress=".$total_account."&privates=".$key;
                    // return $address_url;
                }else if($currency_type == 'btc'){
                    DB::rollBack();
                    return $this->error('基于BTC提币功能暂未开放');
                }else{
                    DB::rollBack();
                    return $this->error('币种类型错误，禁止提币');
                }
                
                $lian = RPC::apihttp($address_url);
                //  var_dump( $lian );
                //  return $lian;
                $lian = @json_decode($lian, true);
                // return $lian;
                if($lian["error"]!="0"){
                    DB::rollBack();
                    return $this->error('提币失败');
                }



                $wallet_out->status = 2;//提币成功状态
                $wallet_out->notes = $notes;//反馈的信息
                $wallet_out->save();
                $user_wallet->lock_change_balance = $user_wallet->lock_change_balance - $number;
                $user_wallet->save();//钱包锁定余额减少
                AccountLog::insertLog([
                    'user_id' => $user_id,
                    'value' => $number,
                    'currency' => $currency,
                    'info' => '提币成功',
                    'type' => AccountLog::WALLETOUTDONE,
                ]);
            }else{
                $wallet_out->status = 3;//提币失败状态
                $wallet_out->notes = $notes;//反馈的信息
                $wallet_out->save();
                $user_wallet->lock_change_balance = $user_wallet->lock_change_balance - $number;
                $user_wallet->change_balance = $user_wallet->lock_change_balance + $number;//失败时把余额解锁
                $user_wallet->save();//钱包锁定余额减少 余额增加
                AccountLog::insertLog([
                    'user_id' => $user_id,
                    'value' => $number,
                    'currency' => $currency,
                    'info' => '提币失败',
                    'type' => AccountLog::WALLETOUTBACK,
                ]);
            }
            DB::commit();
            return $this->success('操作成功');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->error($ex->getMessage());
        }
    }
  
    //导出用户列表至excel
    public function csv()
    {
        $data = USersWalletOut::all()->toArray();
        return Excel::create('提币记录', function ($excel) use ($data) {
            $excel->sheet('提币记录', function ($sheet) use ($data) {
                $sheet->cell('A1', function ($cell) {
                    $cell->setValue('ID');
                });
                $sheet->cell('B1', function ($cell) {
                    $cell->setValue('账户名');
                });
                $sheet->cell('C1', function ($cell) {
                    $cell->setValue('虚拟币');
                });
                $sheet->cell('D1', function ($cell) {
                    $cell->setValue('提币数量');
                });
                $sheet->cell('E1', function ($cell) {
                    $cell->setValue('手续费');
                });
                $sheet->cell('F1', function ($cell) {
                    $cell->setValue('实际提币');
                });
                $sheet->cell('G1', function ($cell) {
                    $cell->setValue('提币地址');
                });
                $sheet->cell('H1', function ($cell) {
                    $cell->setValue('反馈信息');
                });
                $sheet->cell('I1', function ($cell) {
                    $cell->setValue('状态');
                });
                $sheet->cell('J1', function ($cell) {
                    $cell->setValue('提币时间');
                });
                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        $i = $key + 2;
                        if($value['status']==1){
                            $value['status']='申请提币';
                        }else if($value['status'] == 2){
                            $value['status'] = '提币成功';
                        }else{
                            $value['status'] = '提币失败';
                        }
                        $sheet->cell('A' . $i, $value['id']);
                        $sheet->cell('B' . $i, $value['account_number']);
                        $sheet->cell('C' . $i, $value['currency_name']);
                        $sheet->cell('D' . $i, $value['number']);
                        $sheet->cell('E' . $i, $value['rate']);
                        $sheet->cell('F' . $i, $value['real_number']);
                        $sheet->cell('G' . $i, $value['address']);
                        $sheet->cell('H' . $i, $value['notes']);
                        $sheet->cell('I' . $i, $value['status']);
                        $sheet->cell('I' . $i, $value['create_time']);
                    }
                }
            });
        })->download('xlsx');
    }

    
}
