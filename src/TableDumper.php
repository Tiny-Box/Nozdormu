<?php

namespace TinyBox\Nozdormu;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TableDumper
{
    protected $table;

    protected $path;

    protected $fileName;

    protected $output;

    public function __construct($table)
    {
        $this->table = $table;
        $this->path = base_path() . '/database/';
        $this->fileName = 'backup-table-' . $table . '-' . date('d-m-Y-h:i:s') . '.sql';
        $this->output = '';
    }

    public function backup()
    {
        $this->output .= "\n" . Utils::getExecutableComments('dumper_header') . "\n";
        $this->output .= $this->getTablestructure();
        $this->output .= $this->getInsertSQL();
        $this->output .= "\n" . Utils::getExecutableComments('dumper_footer') . "\n";
        $this->save();
        return $this->fileName;
    }

    private function getInsertSQL()
    {
        $insertSQL = "\n" . str_replace('table_name', $this->table, Utils::getExecutableComments('before_insert')) . "\n";

        $insertSQL .= 'INSERT INTO `'."{$this->table}"."` (";
        $columnData = DB::select("SHOW COLUMNS FROM {$this->table}");
        $columnDataCount = count($columnData);

        $columnTypeList = [];
        foreach ($columnData as $index => $item) {
            $insertSQL .= "`" . $item->Field . "`";
            $columnTypeList []= ['field' => $item->Field, 'type' => $item->Type];
            if ($index < $columnDataCount - 1) {
                $insertSQL .= ", ";
            }
        }
        $insertSQL .= ")".' VALUES';

        $fetchData = DB::select("SELECT * FROM {$this->table}");
        $dataCount = count($fetchData);

        if ($dataCount == 0) return '';

        foreach ($fetchData as $dataIndex => $item) {
            $insertSQL .= "\n(";
            $itemWithArray = get_object_vars($item);
            foreach ($columnTypeList as $columnIndex => $columnItem) {
                $insertSQL .= "\"" . $this->getQuoteField($itemWithArray, $columnItem['field'], $columnItem['type']) . "\"";
                if ($columnIndex < $columnDataCount - 1) {
                    $insertSQL .= ", ";
                }
            }

            if ($dataIndex < $dataCount - 1) {
                $insertSQL .= "),";
            } else {
                $insertSQL .= ");";
            }
        }

        $insertSQL .= "\n" . str_replace('table_name', $this->table, Utils::getExecutableComments('after_insert')) . "\n";

        return $insertSQL;
    }

    private function getQuoteField($itemWithArray, $field, $type)
    {
        $numTypeList = ['tinyint','smallint','mediumint','int','bigint','float','double','decimal','real'];
        $columnValue = $itemWithArray[$field] ?? 'NULL';
        if (!in_array($type, $numTypeList)) {
            $columnValue = addslashes($columnValue);
        }
        return $columnValue;
    }


    private function getTableStructure()
    {
        $dropTableSQL = "\n DROP TABLE IF EXISTS {$this->table};";
        $fetchData = DB::select("SHOW CREATE TABLE {$this->table}");
        // 为什么既然是stdclass还要设计这种property。。。。
        $createTableSQL = get_object_vars($fetchData[0])['Create Table'];
        $createTableSQL = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $createTableSQL);
        return $dropTableSQL . "\n\n" . $createTableSQL . ";\n\n";
    }

    private function save()
    {
        Storage::put($this->fileName, $this->output);
    }

}
