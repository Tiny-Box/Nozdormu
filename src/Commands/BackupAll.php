<?php

namespace TinyBox\Nozdormu\Commands;

use Illuminate\Console\Command;
use TinyBox\Nozdormu\TableDumper;
use TinyBox\Nozdormu\Utils;

class BackupAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nozdormu:backup:all';

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
        $tableList = ['data_types', 'data_rows', 'menus', 'menu_items', 'users', 'user_roles', 'settings'];
        $dumperFileList = [];
        foreach ($tableList as $table) {
            $dumper = new TableDumper($table);
            $dumperFileList []= $dumper->backup();
        }

        $outputFileName = 'backup-voyager-all-' . date('d-m-Y-h:i:s') . '.sql';

        Utils::mergeSQLFile($dumperFileList, $outputFileName, TRUE);
    }
}
