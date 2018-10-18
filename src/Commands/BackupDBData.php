<?php

namespace TinyBox\Nozdormu\Commands;

use Illuminate\Console\Command;
use TinyBox\Nozdormu\TableDumper;
use TinyBox\Nozdormu\Utils;

class BackupDBData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nozdormu:backup:dbdata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
        // 这么写很蠢，但是初版嘛，就先这样了，等之后加个Facade
        $typesDumper = new TableDumper('data_types');
        $typesFileName = $typesDumper->backup();
        $rowsDumper = new TableDumper('data_rows');
        $rowsFileName = $rowsDumper->backup();

        $outputFileName = 'backup-voyager-dbdata-' . date('d-m-Y-h:i:s') . '.sql';

        Utils::mergeSQLFile([$typesFileName, $rowsFileName], $outputFileName, TRUE);
    }
}
