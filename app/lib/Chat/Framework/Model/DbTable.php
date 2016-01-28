<?php

namespace Chat\Framework\Model;

use Chat\Framework\Db\DB as DB;

class DbTable {

    protected static $table;
    protected static $_data;
    CONST SAVE_FILE = 1;

    public static function getArray($init) {
        self::fillTable($init);
        return self::$table;
    }

    public static function getArrayById($id) {
        $_array = array(
            1 => array('name' => 'Alex', 'email' => 'lidhen@list.ru'),
        );

        return (isset($_array[$id])) ? $_array[$id] : null;
    }

    protected static function fillTable($init) {

        /* $table = array(
          'users' => array('id' => '', 'name' => '', 'gravatar' => '', 'last_activity' => ''),
         * 'chat' => array('id' => '', 'author' => '', 'gravatar' => '','text' => '', 'ts'=> '', 'file' => '')
          );
         */
        $string = file_get_contents(__DIR__ . '/Database/' . $init . '_schema.json');
        $table = json_decode($string, true);
        self::$table = $table[$init];
    }

    public static function save($init, $keys, $values) {
        if (self::SAVE_FILE != 1) {
            self::saveFile($init, $keys, $values);
        } else {
            self::saveDb($init, $keys, $values);
        }
    }

    protected static function saveFile($init, $data) {
       
        $filename_schema = __DIR__ . '/Database/' . $init . '_schema.json';
        $filename_database = __DIR__ . '/Database/' . $init . '.json';
         
        $schema = file_get_contents($filename_schema);
        
        $table_schema = json_decode($schema, true);
        
        $database = file_get_contents($filename_database);
        $table_database = json_decode($database, true);
        
        
        if(is_array($table_database)){
            array_push($table_database, $data);
        }else{
            $data['id'] = 1;
            $table_database = array($data);
        }
                
        file_put_contents($filename_database, json_encode($table_database));
        
    }

    protected static function saveDb($init, $data) {
        
        self::convertData($data);
        $cdata = self::$_data;
        
        
        $query = "
			INSERT INTO " . $init . " (" . $cdata['keys'] . ")
			VALUES (
				" . $cdata['values'] . "	
		)";

        DB::query($query);
    }
    
    
    public static function convertData($data){
        $keys = '';
        foreach ($data as $key => $value) {
            $keys .= $key . ', ';
            $values .= "'" . DB::esc($value) . "',";
        }

        $keys = substr(trim($keys), 0, -1);
        $values = substr(trim($values), 0, -1);
        
        self::$_data =  array('keys' => $keys, 'values' => $values);
    }

}
