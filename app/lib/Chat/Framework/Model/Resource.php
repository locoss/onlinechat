<?php

namespace Chat\Framework\Model;

use Chat\Framework\Model\DbTable as DbTable;

class Resource{
    public static function load($id){
        return DbTable::getArrayById($id);
    }
    
    public static function initTable($init){
        return DbTable::getArray($init);
    }
}
