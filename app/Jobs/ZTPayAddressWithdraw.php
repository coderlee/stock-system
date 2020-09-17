<?php


namespace App\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Logic\WalletLogic;

class ZTPayAddressWithdraw implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $address;
    public function __construct($address)
    {
        $this->address = $address;
    }


    public function handle(){
        // if(WalletLogic::isInQueue($this->address)){
        $res = WalletLogic::withdraw($this->address);
        if($res){
            WalletLogic::delWithdrawQueue($this->address);
        }
        }
    // }
}