<?php

use Illuminate\Database\Seeder;

class BankTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            ['name'=>'工商银行'],
            ['name'=>'建设银行'],
            ['name'=>'中国银行'],
            ['name'=>'农业银行'],
            ['name'=>'交通银行'],
            ['name'=>'邮政储蓄'],
            ['name'=>'招商银行'],
            ['name'=>'光大银行'],
            ['name'=>'广发银行'],
            ['name'=>'华夏银行'],
            ['name'=>'浦发银行'],
            ['name'=>'兴业银行'],
            ['name'=>'农村商业银行'],
        );

        foreach ($data as $d){
            $bank = new \App\Bank();
            $bank->name = $d['name'];
            $bank->save();
        }
    }
}
