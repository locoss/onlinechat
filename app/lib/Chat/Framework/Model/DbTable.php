<?php

namespace Chat\Framework\Model;

use Chat\Framework\Db\DB as DB;

class DbTable {

    protected static $table;
    protected static $_data;
    protected static $query;
    protected static $init;

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

    public static function save($init, $data, $query) {
        self::$query = $query;
        self::$init = $init;

        if (self::SAVE_FILE != 1) {
            return self::saveFile($data);
        } else {
            return self::saveDb($data);
        }
    }
    
    public static function collection($init, $data, $query){
        self::$query = $query;
        self::$init = $init;
        
        self::convertData($data);

        self::getQuery();
        $query = self::$query;
        
        echo $query;
        $object = DB::query($query);
        
        
        
        var_dump($object->fetch_object());
        die();
    }

    protected static function saveFile($data) {
        $init = self::$init;
        $filename_schema = __DIR__ . '/Database/' . $init . '_schema.json';
        $filename_database = __DIR__ . '/Database/' . $init . '.json';

        $schema = file_get_contents($filename_schema);

        $table_schema = json_decode($schema, true);

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

    protected static function saveDb($data) {
        $init = self::$init;

        self::convertData($data);

        self::getQuery();
        $query = self::$query;

        return DB::query($query);
        
    }

    public static function getQuery() {
        $init = self::$init;
        $cdata = self::$_data;
        switch (self::$query) {
            case 'save':
                
                self::$query = "
			INSERT INTO " . $init . " (" . $cdata['keys'] . ")
			VALUES (
				" . $cdata['values'] . "	
		)";
                break;

            case 'update':
                self::$query = "
			INSERT INTO " . $init . " (" . $cdata['keys'] . ")
			VALUES (
				" . $cdata['values'] . "
			) ON DUPLICATE KEY UPDATE last_activity = NOW()";
                break;

            case 'collection':
                $query = "SELECT * FROM " . $init;
                if (isset($cdata['order'])) {
                    $query .= " ORDER BY " . $cdata['order'];
                }
                if ($cdata['sort']) {
                    $query .= " " . $cdata['sort'];
                }

                if ($cdata['limit']) {
                    $query .= " LIMIT " . $cdata['limit'];
                }

                self::$query = $query;
                break;

            case 'delete':
                self::$query = "DELETE FROM " . $init . " WHERE " . $cdata['keys'] . " = " . $cdata['values'] . "";
                break;

            default:
                throw new \Exception('Wrong query');
        }
    }

    public static function convertData($data) {

        if (is_array($data)) {
            $keys = '';
            $values = '';

            if (self::$query != 'collection') {


                foreach ($data as $key => $value) {
                    //if ($value != '') {
                    $keys .= $key . ', ';
                    $values .= "'" . DB::esc($value) . "',";
                    //}
                }

                $keys = substr(trim($keys), 0, -1);
                $values = substr(trim($values), 0, -1);

                self::$_data = array('keys' => $keys, 'values' => $values);
            } else {
                self::$_data = $data;
            }
        } else {
            self::$_data = DB::esc($data);
        }
    }

}
