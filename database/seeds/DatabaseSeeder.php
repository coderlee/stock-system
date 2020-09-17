<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminTableSeeder::class,
            AdminRoleTableSeeder::class,
            SettingsTableSeeder::class,
            NewsCategoryTableSeeder::class,
            NewsTableSeeder::class,
            CurrencyTableSeeder::class,
        ]);
    }
}
