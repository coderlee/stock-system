<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Locking::class,
        Commands\BonusAlgorithm::class,
        Commands\MonitorEthLog::class,
        Commands\HistoricalDatas::class,
        Commands\AutoCancelLegal::class,
        Commands\UpdateBalance::class,
        Commands\MakeOneWallet::class,
        //Commands\UpdateSortNum::class,
        Commands\AutoCancelC2C::class,
        Commands\AutoCancelC2CDeal::class,
        Commands\GetKline::class,
        Commands\GetMarket::class,
        Commands\GetKline_FiveMin::class,
        Commands\GetKline_ThirtyMin::class,
        Commands\GetKline_Hourly::class,
        Commands\GetKline_Daily::class,
        Commands\GetKline_Weekly::class,
        Commands\GetKline_Monthly::class,
        Commands\get_kline_data::class,
        
        
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('lever:overnight')->dailyAt('00:01'); //收取隔夜费 
        $schedule->command('historical_data')->daily();
        $schedule->command('auto_cancel_legal')->hourly()->appendOutputTo('./auto_cancel_legal.log');
        $schedule->command('update_balance')->daily()->withoutOverlapping();
        // $schedule->command('monitor_eth_log')->hourly();
        // $schedule->command('bonus_algorithm')->daily();
        //$schedule->command('locking')->daily();
       // $schedule->command('get_market')->everyFiveMinutes()->appendOutputTo('./market.txt');
      // $schedule->command('get_kline_data_fivemin')->everyMinute();
       /* while(true){
           echo("haha");
        $schedule->command('get_kline_data_fivemin')->everyMinute();
        $schedule->command('get_kline_data_thirtymin');
        $schedule->command('get_kline_data_hourly');
        $schedule->command('get_kline_data_daily');
        $schedule->command('get_kline_data_weekly');
        $schedule->command('get_kline_data_monthly');
       
        sleep(1);
       }   */
      //$schedule->command('get_kline_data_test');
       
        //$schedule->command('auto_cancel_c2c')->hourly()->appendOutputTo('./auto_cancel_c2c.log');
        //$schedule->command('auto_cancel_c2c_deal')->everyMinute()->appendOutputTo('./auto_cancel_c2c_deal.log');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
