<?php


namespace App\Jobs;


use App\Logic\GdLogic;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class GdOrderSubmit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $params;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        extract($this->params);
        //获取大师的随从
        $slave = DB::table('gd_order')->where('gd_user_id',$master_id)->where('status',1)->get();
        // var_dump($slave);
        foreach($slave as $s){
                    GdLogic::followMicroTrade($s->id,$match_id,$currency_id,$seconds,$price,$type);

            // GdLogic::followMicroTrade($s->id,$master_id,$currency_id,$seconds,$price,$s->value,$type);
        }
    }
}
