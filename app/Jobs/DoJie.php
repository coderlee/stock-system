<?php

namespace App\Jobs;

use App\Agent;
use Illuminate\Support\Facades\DB;
use App\LeverTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DoJie implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        DB::table('settings')->where('key' , 'dojie')->update(['value' => 1]);


        LeverTransaction::where('status' , LeverTransaction::CLOSED)
            ->where('settled' , 0)
            ->chunk(100, function($lever_transactions) {
                foreach ($lever_transactions as $key => $trade) {
                    try {
                        DB::transaction(function ($tr) use ($trade) {
                            //取出该用户的代理商关系数组
                            $_p_arr = Agent::getUserParentAgent($trade->user_id);
                            if (!empty($_p_arr)) {
                                foreach ($_p_arr as $k=>$val){

                                    //如果收益比例存在异常，比如手动修改数据等，则不发放收益
                                    if (bccomp($val['pro_loss'] , 0)  === 1){
                                        //盈亏收益 . 头寸收益是反的，需要取相反数
                                        $_base_money = bcmul($trade->fact_profits , -1 , 8 );
                                        $change = bcmul($_base_money , $val['pro_loss']/100 , 8 );

                                        Agent::change_agent_money(
                                            Agent::getAgentById($val['agent_id']) ,
                                            1  ,
                                            $change,
                                            $trade->id,
                                            '您的下级'.$trade->user_id.'的订单产生的寸头收益为'.$change.'。订单编号为'.$trade->id,
                                            $trade->user_id
                                        );
                                    }

                                    //如果手续费比例存在异常，比如手动修改数据等，则不发放收益
                                    if (bccomp($val['pro_ser'] , 0)  === 1){
                                        //手续费收益
                                        $change = bcmul($trade->trade_fee , $val['pro_ser']/100 , 8 );

                                        Agent::change_agent_money(
                                            Agent::getAgentById($val['agent_id']) ,
                                            2  ,
                                            $change,
                                            $trade->id,
                                            '您的下级'.$trade->user_id.'的订单产生的手续费收益为'.$change.'。订单编号为'.$trade->id,
                                            $trade->user_id
                                        );
                                    }

                                }
                            }
                            DB::table('lever_transaction')->where('id' , $trade->id)->update(['settled' =>1]);
                        });
                    } catch (\Exception $e) {
                        echo 'File :' . $e->getFile() . PHP_EOL;
                        echo 'Line :' . $e->getLine() . PHP_EOL;
                        echo 'Msg :' . $e->getMessage(). PHP_EOL;
                    }
                }
            });

        DB::table('settings')->where('key' , 'dojie')->update(['value' => 0]);

    }
}
