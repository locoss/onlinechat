<?php

namespace Chat\Framework\Model;


class File {

    
    protected static $init;

    CONST SAVE_FILE = 1;

   

    protected static function saveFile($init, $data) {
        
        $filename_schema = __DIR__ . '/Database/' . $init . '_schema.json';
        $schema = file_get_contents($filename_schema);
        $table_schema = json_decode($schema, true);
        
        
        $filename_database = __DIR__ . '/Database/' . $init . '.json';
        $database = file_get_contents($filename_database);
        $table_database = json_decode($database, true);
        
        
        

        if (is_array($table_database)) {
            array_push($table_database, $data);
        } else {
            $data['id'] = 1;
            $table_database = array($data);
        }

        file_put_contents($filename_database, json_encode($table_database));
    }

 

}
