<?php

namespace Chat\Framework\Model;

//use Chat\Framework\Model\DbTable as DbTable;
use Chat\Framework\Model\File as DbTable;
//use Chat\Framework\Db\DB as DB;

class Resource {
    
    public static function initTable($init) {
        return DbTable::getArray($init);
    }

    public static function load($id) {
        return DbTable::load($id);
    }
    
    public static function loadByFieldName($field_name, $field_value){
        return true;
    }

    public static function save($init, $data, $query) {
        return DBTable::save($init, $data, $query);
    }
    
    public static function collection($init, $data, $query){
        return DbTable::collection($init, $data, $query);
    }
    
    public static function getObject(){
        return DbTable::getObject();
    }
    
    

}
