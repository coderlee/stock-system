<?php

use Illuminate\Database\Seeder;

class NewsCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            ['id'=>1,'name'=>'区块链学堂','sorts'=>0,'is_show'=>1,'create_time'=>time(),'update_time'=>time(),'site_id'=>1],
            ['id'=>2,'name'=>'法币交易','sorts'=>0,'is_show'=>1,'create_time'=>time(),'update_time'=>time(),'site_id'=>1],
            ['id'=>3,'name'=>'新手帮助','sorts'=>0,'is_show'=>1,'create_time'=>time(),'update_time'=>time(),'site_id'=>1],
            ['id'=>4,'name'=>'公告','sorts'=>0,'is_show'=>1,'create_time'=>time(),'update_time'=>time(),'site_id'=>1],
            ['id'=>5,'name'=>'首页banner','sorts'=>0,'is_show'=>0,'create_time'=>time(),'update_time'=>time(),'site_id'=>1],
            ['id'=>6,'name'=>'系统文章','sorts'=>0,'is_show'=>0,'create_time'=>time(),'update_time'=>time(),'site_id'=>1],
        );

        foreach ($data as $d ){
            $new_category = new \App\NewsCategory();
            $new_category->id = $d['id'];
            $new_category->name = $d['name'];
            $new_category->sorts = $d['sorts'];
            $new_category->is_show = $d['is_show'];
            $new_category->create_time = $d['create_time'];
            $new_category->update_time = $d['update_time'];
            $new_category->site_id = $d['site_id'];
            $new_category->save();

        }

    }
}
