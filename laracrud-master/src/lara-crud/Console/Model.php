<?php
namespace LaraCrud\Console;

use Illuminate\Console\Command;
use LaraCrud\Crud\Model as ModelCrud;

class Model extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "laracrud:model {table} {name?} {--on=} {--off=}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Model class based on table';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $table = $this->argument('table');
            $modelName = $this->argument('name');
            $on = $this->option('on');
            $off = $this->option('off');

            //Overwrite existing Configuration file for this Model Instance
            if (!empty($on)) {
                $ons = explode(",", $on);
                foreach ($ons as $option) {
                    config(["laracrud.model.$option" => true]);
                }
            }
            if (!empty($off)) {
                $offs = explode(",", $off);
                foreach ($offs as $option) {
                    config(["laracrud.model.$option" => false]);
                }
            }
            if ($table == 'all') {
                $modelCrud = new ModelCrud();
            } else {
                if (strripos($table, ",")) {
                    $table = explode(",", $table);
                    ModelCrud::checkMissingTable($table);
                    foreach ($table as $tb) {
                        $modelCrud = new ModelCrud($table);
                        $modelCrud->save();
                    }
                } else {
                    ModelCrud::checkMissingTable($table);
                    $modelCrud = new ModelCrud($table, $modelName);
                    $modelCrud->save();
                }

            }

            $this->info('Model class successfully created');
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }
}