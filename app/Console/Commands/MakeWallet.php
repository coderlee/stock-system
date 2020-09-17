<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Users;
use App\UsersWallet;

class MakeWallet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:wallet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成钱包';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Users::chunk(1000, function ($users) {
            foreach ($users as $key => $user) {
                UsersWallet::makeWallet($user->id);
                $this->info('用户id' . $user->id . '生成钱包完成');
            }
        });
        $this->info('全部生成完成');
    }
}
