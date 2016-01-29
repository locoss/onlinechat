<?php

namespace Chat\Framework\Model;

use Chat\Framework\Model\DbTable as DbTable;
use Chat\Framework\Db\DB as DB;

class Resource {

    public static function load($id) {
        return DbTable::getArrayById($id);
    }

    public static function initTable($init) {
        return DbTable::getArray($init);
    }

    public static function save($init, $data, $query) {
        return DBTable::save($init, $data, $query);
    }
    
    

}
