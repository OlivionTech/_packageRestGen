<?php namespace Bramf\CrudGenerator\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class RestTestCommand extends Command{
    const EXCEPTION_TABLES = [
        'users','crud_route_groups','migrations'
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rest:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD controller,model and routes for all tables and generate open api annotations';

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
        $this->info('OpenApi annotations created successfully');
    }
}