<?php

namespace Chat\Framework\Db;

class DBFile {

    private static $instance;
    private $database;
    private $fpath;
    private $object;
    private $_init;

    private function __construct($init) {
        $this->_init = $init;
        $filename_schema = __DIR__ . '/Database/' . $init . '_schema.json';
        $schema = file_get_contents($filename_schema);
        $table_schema = json_decode($schema, true);


        $filename_database = __DIR__ . '/Database/' . $init . '.json';
        $database = file_get_contents($filename_database);
        $table_database = json_decode($database, true);

        $this->database = $table_database;
        $this->fpath = $filename_database;
    }

    public static function init($init) {
        if (self::$instance instanceof self) {
            return false;
        }

        self::$instance = new self($init);
    }

    public static function getCollection() {

        $table_database = self::$instance->database;
        $object_array = array();
        if (is_array($table_database)) {
            foreach ($table_database as $key => $value) {
                $object = new \stdClass();
                foreach ($value as $property => $prop_value) {
                    $object->$property = $prop_value;
                }
                $object_array[] = $object;
            }
        }
        return $object_array;
    }

    public static function getObject() {
        return self::$instance->object;
    }

    public static function save($data) {
        $table_database = self::$instance->database;
        $object = new \stdClass('affected_rows');
        $object->insert_id = 1;
        if (is_array($table_database)) {
            foreach ($table_database as $row) {
                if (isset($row['name'])) {
                    if ($row['name'] == $data['name']) {
                        $object->affected_rows = 0;
                        //\Chat\Framework\Bootstrap::register('object', $affected_rows);
                        self::$instance->object = $object;
                        return false;
                    }
                }
            }
        }
        
        if(self::$instance->_init == 'chat'){
            $data['ts'] = time();
        }

        if (is_array($table_database)) {
            $data['id'] = count($table_database) + 1;
            array_push($table_database, $data);
        } else {
            $data['id'] = 1;
            $table_database = array($data);
        }

        file_put_contents(self::$instance->fpath, json_encode($table_database));
        foreach ($data as $prop => $pro_value) {
            $object->$prop = $pro_value;
        }
        $object->insert_id = count($table_database) + 1;
        $object->affected_rows = 1;
        self::$instance->object = $object;
        //\Chat\Framework\Bootstrap::register('object', $affected_rows);
        $returned_object = array();
        foreach ($table_database as $property => $value) {
            $object = new \stdClass();
            $object->$property = $value;
            $returned_object[] = $object;
        }
        self::$instance->database = $returned_object;

        return self::$instance->database;
    }

    public static function update($data) {
        $table_database = self::$instance->database;

        if (is_array($table_database)) {
            foreach ($data as $data_key => $data_value) {
                foreach ($table_database as $key => $value) {
                    if ($value[$data_key] == $data_value) {
                        $object = new \stdClass();
                        foreach ($value as $property => $prop_value) {
                            $object->$property = $prop_value;
                        }
                    }
                }
            }
            $file = fopen(self::$instance->fpath, 'wb');
            fwrite($file, json_encode($table_database));
            fclose($file);
            return array($object);
        }
    }

    public static function delete($data) {
        $table_database = self::$instance->database;
        if (is_array($table_database)) {
            foreach ($data as $data_key => $data_value) {
                foreach ($table_database as $key => $value) {
                    if ($value[$data_key] == $data_value) {
                        unset($table_database[$key]);
                    }
                }
            }
            $file = fopen(self::$instance->fpath, 'wb');
            fwrite($file, json_encode($table_database));
            fclose($file);

            self::$instance->database = $table_database;
        }

        return self::$instance->database;
    }

}
