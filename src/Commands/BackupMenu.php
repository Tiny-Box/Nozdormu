<?php

namespace TinyBox\Nozdormu\Commands;

use Illuminate\Console\Command;
use TinyBox\Nozdormu\TableDumper;
use TinyBox\Nozdormu\Utils;

class BackupMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voyager:backup:menu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'backup voyager menu table data';

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
        $menusDumper = new TableDumper('menus');
        $menusFileName = $menusDumper->backup();
        $itemsDumper = new TableDumper('menu_items');
        $itemsFileName = $itemsDumper->backup();

        $outputFileName = 'backup-voyager-menu-' . date('d-m-Y-h:i:s') . '.sql';

        Utils::mergeSQLFile([$menusFileName, $itemsFileName], $outputFileName, TRUE);
    }
}
