<?php namespace Olivion\RestGen\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Olivion\RestGen\Builders\Model;
use Olivion\RestGen\Builders\Controller;

class RestTestCommand extends Command{
    const EXCEPTION_TABLES = [
        'crud_route_groups','migrations','error_logs'
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
        $this->params = [];
        $this->tables = [];
    }


    /**
     * generate crud controller,model and routes
     */
    private function crud(){
        foreach($this->tables as $table){

            // $this->output->writeln('<info>'.$table->service_name.': '.$table->table_name.'</info>');

            $params = $this->prepareParams($table);
            $this->line('CRUD for '.$table->table_name);
            // (new Model($params))->build();
            (new Controller($params))->build();
        }
        $this->newLine();
    }

    /**
     * get all tables names, excluding exception tables 
     */
    private function getTableNames(){

        $this->tables = DB::table('pg_tables AS t')
        ->leftJoin('pg_class as c',function(\Illuminate\Database\Query\JoinClause $join){
            $join->on([
                ['c.relname','=','t.tablename']
            ]);
        })
        ->leftJoin('pg_description as d',function(\Illuminate\Database\Query\JoinClause $join){
            $join->on([
                ['d.objoid','=','c.oid']
            ]);
        })
        ->where([
            't.schemaname' => 'public'
        ])
        ->get(['t.tablename as table_name','d.description AS service_name']);
    }

    /**
     * prepare params for crud
     */
    private function prepareParams($table){
        $service_name = Str::ucfirst($table->service_name);
        $controllerName = implode('',array_map(function($part){
            return Str::ucfirst(Str::singular($part));
        },explode("_",$table->table_name)));
        
        $params['service_name'] = $service_name;
        $params['controller_name'] = $controllerName.'Controller';
        $params['model_name'] = $controllerName;
        $params['crud_url'] = '/api/'.str_replace('_','/',Str::singular($table->table_name));
        $params['table_name'] = $table->table_name;
        $params['author'] = 'test';
        return $params;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Start');
        $this->getTableNames();
        $this->crud();
        $this->info('Completed!');
    }
}