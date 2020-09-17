<?php

use Illuminate\Database\Seeder;

class CurrencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            ['name'=>'BTC','type'=>'btc'],
            ['name'=>'ETH','type'=>'eth'],
            ['name'=>'USDT','type'=>'btc'],
            ['name'=>'PB','type'=>'erc20'],
        );

        foreach ($data as $d){
            $currency = new \App\Currency();
            $currency->name = $d['name'];
            $currency->is_display = 1;
            $currency->is_lever = 0;
            $currency->is_legal = 1;
            $currency->is_match = 1;
            $currency->show_legal = 1;
            $currency->type = $d['type'];
            $currency->save();
        }
    }
}
