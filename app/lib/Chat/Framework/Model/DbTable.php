<?php

namespace Chat\Framework\Model;

use Chat\Framework\Db\DB as DB;

class DbTable {

    protected static $table;
    protected static $_data;
    protected static $query;
    protected static $init;

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
        
        return self::saveDb($data);
        
    }
    
    public static function collection($init, $data, $query){
        self::$query = $query;
        self::$init = $init;
        
        self::convertData($data);

        self::getQuery();
        
        $query = self::$query;
        
        $result =  DB::query($query); 
        
        while ($user = $result->fetch_object()) {
            $users[] = $user;
        }
        return $users;
    }

    protected static function saveDb($data) {
        $init = self::$init;

        self::convertData($data);
        self::getQuery();

        return DB::query(self::$query);
        
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
                if (isset($cdata['sort'])) {
                    $query .= " " . $cdata['sort'];
                }

                if (isset($cdata['limit'])) {
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
    
    public static function getObject(){
        return DB::getMySQLiObject();
    }

}
