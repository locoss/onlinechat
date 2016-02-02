<?php

namespace Chat\Framework\Model;
use Chat\Framework\Db\DBFile as DB;
use Chat\Framework\Model\AbstractData\AbstractData as AbstractData;
class File extends  AbstractData {

    
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
    
    
    public static function getArray($init){
        
    }
    
    public static function collection($init, $data, $query){
        self::$query = $query;
        self::$init = $init;
        DB::init($init);
        
        return self::getQuery();
    }
    
    public static function load($id){
        
    }
    
    public static function loadByFieldName($init, $field_name, $field_value){
        DB::init($init);
        return DB::loadByFieldName($field_name, $field_value);
    }
    
    public static function save($init, $data, $query){
        self::$query = $query;
        self::$init = $init;
        self::$_data = $data;
        
        return self::saveFile();
    }
    
    public static function getObject(){
       // return \Chat\Framework\Bootstrap::registry('object');
        
        return DB::getObject();
    }
    
   

    protected static function saveFile() {
        
        DB::init(self::$init);
        return self::getQuery();
        
        
        
        /*if (is_array($table_database)) {
            $data['id'] = count($table_database) + 1;
            array_push($table_database, $data);
        } else {
            $data['id'] = 1;
            $table_database = array($data);
        }
        $affected_rows = new \stdClass('affected_rows');
        if(file_put_contents($filename_database, json_encode($table_database))){
            
            $affected_rows->affected_rows = 1;
        }else{
            $affected_rows->affected_rows = 0;
        }
        
        \Chat\Framework\Bootstrap::register('object', $affected_rows);
        $returned_object = array();
        foreach($table_database as $property => $value){
            $object = new \stdClass();
            $object->$property = $value;
            $returned_object[] = $object;
        }
        
       return $returned_object;*/
    }
    
    
    
    protected static function getQuery(){
        switch(self::$query){
            case 'save':
                $result = DB::save(self::$_data);
                break;
            
            case 'update':
                $result = DB::update(self::$_data);
                break;
            
            case 'collection': 
                $result = DB::getCollection();
                break;
            
            case 'delete':
                $result = DB::delete(self::$_data);
                break;
            
            default:
                throw new \Exception('Wrong query');
                
        }
        
        return $result;
    }
 

}
