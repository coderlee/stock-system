<?php

use Illuminate\Database\Seeder;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = new App\Admin();
        $admin->username = 'admin';
        $admin->password = App\Users::MakePassword('123456');
        $admin->role_id = 1;
        $admin->save();

    }
}
