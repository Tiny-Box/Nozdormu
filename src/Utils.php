<?php

namespace TinyBox\Nozdormu;

use Illuminate\Support\Facades\Storage;

class Utils
{
    public static function mergeSQLFile($mergeFileList, $outputFileName, $deleteOrigin = FALSE)
    {
        $mergeContent = '';

        foreach ($mergeFileList as $fileName) {
            $mergeContent .= Storage::get($fileName);
            $mergeContent .= "\n\n-- ------------------------------------------------- \n\n";
        }

        Storage::put($outputFileName, $mergeContent);

        if ($deleteOrigin) Storage::delete($mergeFileList);
    }

    // 我之前想了一下要不要把它抽到一个单独的文件中去
    // 后来觉得就应该放在这里，提醒我这么丑的玩意儿要早点清理
    protected static $excutableComment = [
        'dumper_header' => "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
    /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
    /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
    /*!40101 SET NAMES utf8 */;
    /*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
    /*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
    /*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
    ",
        'dumper_footer' => "/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
    /*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
    /*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
    /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
    /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
    /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
    ",

        'before_insert' => "/*!40000 ALTER TABLE `table_name` DISABLE KEYS */;",
        'after_insert' => "/*!40000 ALTER TABLE `table_name` DISABLE KEYS */;"
    ];

    // 在dump的时候，会有一些excutable comment, 因为初版是使用古典的方法拼出的SQL
    // 所以在这里配一下需要的excutable comment
    public static function getExecutableComments($field)
    {
        return self::$excutableComment[$field];
    }
}
