<?php

namespace TinyBox\Nozdormu\Commands;

use Illuminate\Console\Command;
use TinyBox\Nozdormu\TableDumper;
use TinyBox\Nozdormu\Utils;

class BackupUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voyager:backup:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'backup user table data';

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
        $usersDumper = new TableDumper('users');
        $usersFileName = $usersDumper->backup();
        $userRoleDumper = new TableDumper('user_roles');
        $userRoleFileName = $userRoleDumper->backup();

        $outputFileName = 'backup-voyager-user-' . date('d-m-Y-h:i:s') . '.sql';

        Utils::mergeSQLFile([$userRoleFileName, $usersFileName], $outputFileName, TRUE);
    }
}
