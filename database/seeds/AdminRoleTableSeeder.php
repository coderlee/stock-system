<?php

use Illuminate\Database\Seeder;

class AdminRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin_role = new App\AdminRole();
        $admin_role->name = '超级管理员';
        $admin_role->is_super = 1;
        $admin_role->save();
    }
}
