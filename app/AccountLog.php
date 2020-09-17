<?php
/**
 * Created by PhpStorm.
 * User: swl
 * Date: 2018/7/3
 * Time: 10:23
 */

namespace App;
use App\Users;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class AccountLog extends Model
{
    protected $table = 'account_log';
    public $timestamps = false;
    const CREATED_AT = 'created_time';
    protected $appends = [
        'account_number',
        'account',
        'currency_name',//币种
        'before',//交易前
        'after',//交易后
        'transaction_info'//交易信息
    ];

    const ADMIN_LEGAL_BALANCE = 1;//后台调节法币账户余额
    const ADMIN_LOCK_LEGAL_BALANCE = 2;//后台调节法币账户锁定余额
    const ADMIN_CHANGE_BALANCE = 3;//后台调节币币账户余额
    const ADMIN_LOCK_CHANGE_BALANCE = 4;//后台调节币币账户锁定余额
    const ADMIN_LEVER_BALANCE = 5;//后台调节杠杆账户余额
    const ADMIN_LOCK_LEVER_BALANCE = 6;//后台调节杠杆账户锁定余额

    const WALLET_CURRENCY_OUT = 7;//提币记录
    const WALLET_CURRENCY_IN = 8;//充币记录

    const WALLET_LEGAL_OUT = 9;
    const WALLET_LEGAL_IN = 10;
    const WALLET_CHANGE_IN = 11;//法币划入记录
    const WALLET_CHANGE_OUT = 12;//法币划出记录
    const WALLET_CHANGE_LEVEL_OUT = 13;//法币划出记录
    const WALLET_CHANGE_LEVEL_IN = 14;//法币划出记录
    const WALLET_LEVEL_IN = 15;
    const WALLET_LEVEL_OUT = 16;


    const INVITATION_TO_RETURN = 33;//邀请返佣

    const LEGAL_DEAL_SEND_SELL = 60;//商家发布法币出售
    const LEGAL_DEAL_USER_SELL = 61;//出售给商家法币
    const LEGAL_USER_BUY = 62;//用户购买商家法币成功
    const LEGAL_SELLER_BUY = 63;//商家购买用户法币成功
    const LEGAL_DEAL_USER_SELL_CANCEL = 64;//出售给商家法币-取消
    const INTO_TRA_FB = 65;//美丽链法币转入(imc) 
    const INTO_TRA_BB = 66;//美丽链币币转入(imc) 
    const INTO_TRA_GG = 67;//美丽链杠杆转入(imc) 
    const ADMIN_SELLER_BALANCE = 70;//后台调节商家余额
    const LEGAL_DEAL_BACK_SEND_SELL = 71;//商家撤回发布法币出售
    const LEGAL_DEAL_ERROR_SEND_SELL = 72;//商家撤回发布法币出售
    const LEGAL_DEAL_AUTO_CANCEL = 68;//自动取消法币交易

    /* const BUY_BLOCK_CHAIN = 1;//购买区块链
     const ADJUDT_SUB_BALANCE = 2;//后台调节账户余额
     const TRANSFER_IN = 3;//转入
     const TRANSFER_OUT = 4;//转出
     const ETH_EXCHANGE = 5;//以太币兑换

     const USER_BONUS = 6;//日均收益
     const ECOLOGY_BONUS = 7;//更新生态推广奖励
     const AGENT_REWARD = 8;//代理商管理奖励

     const ADMIN_LOCK_BALANCE = 9; //后台调节锁定余额
     const ADMIN_REMAIN_LOCK_BALANCE = 10; //后台调节锁定余额相应剩余锁定余额

     const LOCK_BALANCE = 11; //锁仓增加
     const LOCK_REMAIN_BALANCE = 12; //锁仓减少
     const ACCEPTOR_SELL = 13; //用户提现承兑申请
     const ACCEPTOR_RECHARGE = 14; //用户充值承兑确认
     const ACCEPTOR_RECHARGE_VAR = 15; //用户充值承兑手续费
     const ACCEPTOR_RECHARGE_DEC = 16; //确认用户充值，承兑商充值额度减少
     const ACCEPTOR_CASH_INC = 17; //确认用户充值，承兑商提现额度增加
     const ACCEPTOR_CASH_DEC = 18; //确认用户提现，承兑商提现额度减少
     const ACCEPTOR_RECHARGE_INC = 19; //确认用户提现，承兑商充值额度增加
     const ACCEPTOR_SELL_RETURN = 20; //取消用户提现承兑申请
     const ACCEPTOR_CASH_RETURN = 91; //取消用户提现承兑,承兑商提现额度增加

     */
    const TRANSACTIONOUT_SUBMIT_REDUCE = 21; //提交卖出，扣除

    const TRANSACTIONIN_REDUCE = 22; //买入扣除
    const TRANSACTIONIN_SELLER = 23; //扣除卖方
    const TRANSACTIONIN_SUBMIT_REDUCE = 24; //提交买入，扣除

    const TRANSACTIONIN_REDUCE_ADD = 25; //买方增加币
    const TRANSACTIONIN_SELLER_ADD = 26; //卖方增加cny

    const TRANSACTIONIN_REVOKE_ADD = 27; //撤销增加
    const TRANSACTIONOUT_REVOKE_ADD = 28; //撤销增加

    const TRANSACTION_FEE = 29; //卖出手续费

    const LEVER_TRANSACTION = 30; //杠杆交易扣除保证金
    const LEVER_TRANSACTION_ADD = 31; //平仓增加
    const LEVER_TRANSACTION_FROZEN = 32; //爆仓冻结
    const LEVER_TRANSACTION_OVERNIGHT = 34; //隔夜费
    const LEVER_TRANSACTION_FEE = 35; //交易手续费
    const LEVER_TRANSACTIO_CANCEL = 36; //杠杆交易取消

    const WALLETOUT = 99; //用户申请提币
    const WALLETOUTDONE = 100; //用户提币成功
    const WALLETOUTBACK = 101; //用户提币失败
    const TRANSACTIONIN_IN_DEL = 102;//取消买入交易
    const TRANSACTIONIN_OUT_DEL = 103;//取消买出交易

    const CHANGE_LEVER_BALANCE = 104;//杠杆交易账户变化


    const SELLER_BACK_SEND = 299;//杠杆交易账户变化
    const CHANGEBALANCE = 401; //转账
    const LTC_IN = 301; //来自矿机的转账
    const LTC_SEND = 302; //转账余额至矿机

    const ETH_EXCHANGE = 200; //充币增加余额

    //c2c交易
    const C2C_DEAL_SEND_SELL = 201;//用户发布法币出售
    const C2C_DEAL_AUTO_CANCEL = 202;//自动取消c2c法币交易
    const C2C_DEAL_USER_SELL = 203;//出售给用户法币
    const C2C_USER_BUY = 204;//用户购买法币成功
    const C2C_DEAL_BACK_SEND_SELL = 205;//商家撤回发布法币出售

    const WALLET_LEGAL_LEVEL_OUT = 206;//法币(c2c)转入杠杆
    const WALLET_LEGAL_LEVEL_IN = 207;//法币(c2c)转入杠杆
    const WALLET_LEVEL_LEGAL_OUT = 208;//杠杆转入法币(c2c)
    const WALLET_LEVEL_LEGAL_IN = 209;//杠杆转入法币(c2c)
    const WALLET_DONGJIEGANGGAN=210;
    const WALLET_JIEDONGGANGGAN=211;//审核不通过解冻杠杆冻结


    
    public function getAccountNumberAttribute()
    {
        return $this->hasOne('App\Users', 'id', 'user_id')->value('account_number');
    }
     public function getAccountAttribute()
    {   
        $value=$this->hasOne('App\Users', 'id', 'user_id')->value('phone');
        if(empty($value)){
            $value=$this->hasOne('App\Users', 'id', 'user_id')->value('email');
        }
        return $value;
    }
    public function getCreatedTimeAttribute()
    {
        $value = $this->attributes['created_time'];
        return $value ? date('Y-m-d H:i:s', $value ) : '';
    }
    public function getBeforeAttribute()
    {
        return $this->walletLog()->value('before');
    }
    public function getAfterAttribute()
    {
        return $this->walletLog()->value('after');
    }
    public function getTransactionInfoAttribute(){
        $type1 = [
            '0'=> '未知','1' => '法币交易','2' => '币币交易', '3' => '杠杆交易'
        ];
        $type2 = ['','[锁定]'];
        $balance_type = $this->walletLog()->value('balance_type');
        $lock_tpye = $this->walletLog()->value('lock_type');
        array_key_exists($balance_type,$type1)?:$balance_type = 0;
        array_key_exists($lock_tpye,$type2)?:$lock_tpye = 0;
        return $type1[$balance_type].$type2[$lock_tpye];

    }
    public function getCurrencyNameAttribute()
    {
        return $this->hasOne('App\Currency', 'id', 'currency')->value('name');
    }

    public static function insertLog($data = array(),$data2 = array())
    {
        $data = is_array($data) ? $data : func_get_args();
        $log = new self();
        $log->user_id = $data['user_id'] ?? false;;
        $log->value = $data['value'] ?? '';
        $log->created_time = $data['created_time'] ?? time();
        $log->info = $data['info'] ?? '';
        $log->type = $data['type'] ?? 0;
        $log->currency = $data['currency'] ?? 0;
        $data_wallet['balance_type'] = $data2['balance_type']?? 0;
        $data_wallet['wallet_id'] = $data2['wallet_id']?? 0;
        $data_wallet['lock_type'] = $data2['lock_type']?? 0;
        $data_wallet['before'] = $data2['before']?? 0;
        $data_wallet['change'] = $data2['change']?? 0;
        $data_wallet['after'] = $data2['after']?? 0;
        $data_wallet['memo'] = $data['info']?? 0;
        $data_wallet['create_time'] = $data2['create_time']?? time();
        //dd($data_wallet);
        try {
            DB::transaction(function () use ($log,$data_wallet) {
            $log->save();
            $log->walletLog()->create($data_wallet);
            });
            return true;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
            return false;
        }
    }


    public static function newinsertLog($data = array(),$data2 = array())
    {
        $data = is_array($data) ? $data : func_get_args();
        $log = new self();
        $log->user_id = $data['user_id'] ?? false;;
        $log->value = $data['value'] ?? '';
        $log->created_time = $data['created_time'] ?? time();
        $log->info = $data['info'] ?? '';
        $log->type = $data['type'] ?? 0;
        $log->currency = $data['currency'] ?? 0;
//        $data_wallet['balance_type'] = $data2['balance_type']?? 0;
//        $data_wallet['wallet_id'] = $data2['wallet_id']?? 0;
//        $data_wallet['lock_type'] = $data2['lock_type']?? 0;
//        $data_wallet['before'] = $data2['before']?? 0;
//        $data_wallet['change'] = $data2['change']?? 0;
//        $data_wallet['after'] = $data2['after']?? 0;
//        $data_wallet['memo'] = $data['info']?? 0;
//        $data_wallet['create_time'] = $data2['create_time']?? time();
        //dd($data_wallet);
        try {
            DB::transaction(function () use ($log) {
                $log->save();
//                $log->walletLog()->create($data_wallet);
            });
            return true;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
            return false;
        }
    }


    public static function getTypeInfo($type)
    {
        switch ($type) {
            
            case self::ADMIN_LEGAL_BALANCE:
                return '后台调节法币账户余额';
                break;
            case self::ADMIN_LOCK_LEGAL_BALANCE:
                return '后台调节法币账户锁定余额';
                break;
            case self::ADMIN_CHANGE_BALANCE:
                return '后台调节币币账户余额';
                break;
            case self::ADMIN_LOCK_CHANGE_BALANCE:
                return '后台调节币币账户锁定余额';
                break;
            case self::ADMIN_LEVER_BALANCE:
                return '后台调节合约账户余额';
                break;
            case self::ADMIN_LOCK_LEVER_BALANCE:
                return '后台调节合约账户锁定余额';
                break;
            case self::WALLET_LEGAL_OUT:
                return '法币账户转出至交易账户';
                break;
            case self::WALLET_LEGAL_IN:
                return '交易账户转入至法币账户';
                break;
            case self::WALLET_CHANGE_OUT:
                return '交易账户转出至法币账户';
                break;
            case self::WALLET_CHANGE_IN:
                return '法币账户转入交易账户';
                break;
            case self::WALLET_CHANGE_LEVEL_IN:
                return '合约账户转入交易账户';
                break;
            case self::WALLET_CHANGE_LEVEL_OUT:
                return '交易账户转出至合约账户';
                break;
            case self::WALLET_LEVEL_OUT:
                return '合约账户转出至交易账户';
                break;
            case self::WALLET_LEVEL_IN:
                return '交易账户转入合约账户';
                break;
            case self::INVITATION_TO_RETURN:
                return '邀请返佣金';
                break;
            case self::WALLETOUT:
                return '用户提币';
                break;
            case self::TRANSACTIONIN_IN_DEL:
                return '取消买入交易';
                break;
            case self::TRANSACTIONIN_OUT_DEL:
                return '取消卖出交易';
                break;
            case self::INTO_TRA_FB:
                return '美丽链法币交易余额转入';
                break;
            case self::INTO_TRA_BB:
                return '美丽链币币交易余额转入';
                break;
             case self::INTO_TRA_GG:
                return '美丽链合约交易余额转入';
                break;
            case self::WALLET_LEGAL_LEVEL_OUT:
                return '法币转入合约,法币减少';
                break;
            case self::WALLET_LEGAL_LEVEL_IN:
                return '法币转入合约，合约增加';
                break;
            case self::WALLET_LEVEL_LEGAL_OUT:
                return '合约转法币审核通过,合约减少';
                break;
            case self::WALLET_LEVEL_LEGAL_IN:
                return '合约转法币审核通过，法币增加';
                break;
            case self::WALLET_DONGJIEGANGGAN:
                return '合约转法币,冻结合约转化值';
                break;
            case self::WALLET_JIEDONGGANGGAN:
                return '合约转法币,审核不通过解冻';
                break;
            default:
                return '暂无此类型';
                break;
        }
    }

    /*public static function getTypeInfo($type)
    {
        switch ($type) {
            case self::BUY_BLOCK_CHAIN:
                return '购买区块链';
                break;
            case self::ADJUDT_SUB_BALANCE:
                return '后台调节账户余额';
                break;
            case self::ADMIN_LOCK_BALANCE:
                return '后台调节锁定余额';
                break;
            case self::ADMIN_REMAIN_LOCK_BALANCE:
                return '后台调节锁定余额变动剩余锁定余额';
                break;
            case self::ACCEPTOR_SELL:
                return '用户提现承兑申请';
                break;
            case self::ACCEPTOR_RECHARGE:
                return '用户充值承兑确认';
                break;
            case self::ACCEPTOR_RECHARGE_VAR:
                return '用户充值承兑手续费';
                break;
            case self::ACCEPTOR_RECHARGE_DEC:
                return '确认用户充值，承兑商充值额度减少';
                break;
            case self::ACCEPTOR_CASH_INC:
                return '确认用户充值，承兑商提现额度增加';
                break;
            case self::ACCEPTOR_CASH_DEC:
                return '确认用户提现，承兑商提现额度减少';
                break;
            case self::ACCEPTOR_RECHARGE_INC:
                return '确认用户提现，承兑商充值额度增加';
                break;
            case self::ACCEPTOR_SELL_RETURN:
                return '取消用户提现承兑申请';
                break;
            default:
                return '暂无此类型';
                break;
        }
    }*/

    public function user()
    {
        return $this->belongsTo('App\Users', 'user_id', 'id');
    }
    //关联钱包记录模型
    public function walletLog(){
        return $this->hasOne('App\WalletLog','account_log_id','id')->withDefault();
    }
}