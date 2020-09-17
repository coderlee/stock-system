<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
          ['key'=>'version','value'=>'1.0','notes'=>'版本号'],
          ['key'=>'chain_option','value'=>'a:13:{s:16:"calculate_switch";s:1:"1";s:21:"convertPowerpackRatio";s:3:"100";s:19:"reward_initial_time";s:10:"2018-08-18";s:11:"trade_limit";s:6:"a:0:{}";s:20:"calculate_off_prompt";s:64:"收益暂未开始，预计2018年8月18日上线，敬请关注";s:22:"powerpack_static_ratio";s:373:"a:5:{i:0;a:3:{s:3:"min";s:1:"1";s:3:"max";s:2:"11";s:5:"ratio";s:3:"0.1";}i:1;a:3:{s:3:"min";s:2:"11";s:3:"max";s:3:"101";s:5:"ratio";s:3:"0.2";}i:2;a:3:{s:3:"min";s:3:"101";s:3:"max";s:4:"1001";s:5:"ratio";s:3:"0.3";}i:3;a:3:{s:3:"min";s:4:"1001";s:3:"max";s:5:"10001";s:5:"ratio";s:4:"0.35";}i:4;a:3:{s:3:"min";s:5:"10001";s:3:"max";s:6:"100001";s:5:"ratio";s:4:"0.38";}}";s:16:"hold_reawrd_send";s:1:"1";s:17:"share_reawrd_send";s:1:"0";s:16:"team_reawrd_send";s:1:"0";s:21:"prizepool_expire_time";s:2:"48";s:13:"decline_ratio";s:2:"50";s:22:"decline_compensate_day";s:3:"100";s:24:"compensation_reawrd_send";s:1:"1";}','notes'=>''],
          ['key'=>'ExRate','value'=>'1','notes'=>'PB汇率'],
          ['key'=>'change_rate','value'=>'10','notes'=>'交易手续费'],
          ['key'=>'USDTRate','value'=>'6.5','notes'=>'USDT汇率'],
        );
        foreach ($data as $d){
            $settings = new \App\Setting();
            $settings->key = $d['key'];
            $settings->value = $d['value'];
            $settings->notes = $d['notes'];
            $settings->save();
        }
    }
}
