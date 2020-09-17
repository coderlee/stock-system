<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Workerman\Worker;

class WebSocketClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:client {worker_command} {--mode=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'websocket client';

    protected $worker;

    protected $events = [
        'onWorkerStart',
        'onConnect',
        'onMessage',
        'onClose',
        'onError',
        'onBufferFull',
        'onBufferDrain',
        'onWorkerStop',
        'onWorkerReload'
    ];

    protected $callback_class;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        
        
        echo("命令初始化\n\r");
        parent::__construct();
        $class_name = config('websocket.client.callback_class');
        $process_num = config('websocket.client.process_num');
        $this->callback_class = new $class_name();
        $this->worker = new Worker();
        $this->worker->count = $process_num;
       // echo($this->worker->id);
     //  file_put_contents("log.txt", $this->worker->id . PHP_EOL, FILE_APPEND);
       // exit();
        $this->worker->name = 'Huobi Websocket';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->initWorker();
        $this->bindEvent();
        $this->worker->runAll();
    }

    protected function initWorker()
    {
        echo("initWorker\n\r");
        global $argv;
        
        $argv[1] = $command = $this->argument('worker_command');
        $mode = $this->option('mode');
      //  print_r($mode);
        isset($mode) && $argv[2] = '-' . $mode;
       // print_r($argv);
    }

    protected function bindEvent()
    {
        echo("bindEvent\n\r");
        foreach ($this->events as $key => $event) {
            method_exists($this->callback_class, $event) && $this->worker->$event = [$this->callback_class, $event];
        }
    }
}
