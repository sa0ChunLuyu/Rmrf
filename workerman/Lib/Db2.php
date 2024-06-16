<?php

namespace Workerman\Lib;
class Db2
{
    public static function i($db, $table, $array)
    {
        $insertArr = array();
        $sql = "insert into `{$table}`(";
        $sql1 = 'values(';
        foreach ($array as $key => $value) {
            $sql .= "`{$key}`,";
            $sql1 .= "?,";
            $insertArr[] = $value;
        }
        $sql = trim($sql, ',');
        $sql1 = trim($sql1, ',');
        $sql .= ")" . $sql1 . ")";
        $db->querysql($sql, $insertArr);
        return $db->getinsertid();
    }

    public static function u($db, $table, $array, $where, $where_a = [])
    {
        $updateArr = array();
        $sql = "update `{$table}` set ";
        foreach ($array as $key => $value) {
            $sql .= "`{$key}`=?,";
            $updateArr[] = $value;
        }
        $sql = trim($sql, ',');
        $where = ' ' . $where;
        $sql .= $where;
        $updateArr = array_merge($updateArr, $where_a);
        return $db->querysql($sql, $updateArr);
    }

    public static function d($db, $table, $where, $where_a = [])
    {
        $sql = "delete from `{$table}` " . $where;
        $db->querysql($sql, $where_a);
    }
}