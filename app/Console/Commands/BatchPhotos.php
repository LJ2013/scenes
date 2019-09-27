<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BatchPhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'batch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $base = base_path();
        $script = base_path('bin/job.py');
        echo shell_exec("python $script $base");
    }
}
