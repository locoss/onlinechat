<?php

namespace Chat\Framework\Model;

use Chat\Framework\Model\Resource as Resource;
use Chat\Framework\Model\Object as Object;
use Chat\Framework\Db\DB as DB;

class AModel extends Object {

    protected $init;

    public function __construct() {
        $this->insertData(Resource::initTable($this->init));
        return $this;
    }

    public function load($id) {
        $this->insertData(Resource::load($id));
        return $this;
    }

    public function save() {
        $keys = '';
        foreach ($this->getData() as $key => $value) {
            $keys .= $key . ', ';
            $values .= "'" . DB::esc($value) . "'," ;
        }

        $keys = substr(trim($keys), 0, -1);
        $values = substr(trim($values), 0, -1);
        $query = "
			INSERT INTO " . $this->init . " (" . $keys . ")
			VALUES (
				" . $values . "
				
		)";
        
        DB::query($query);

        return $this;
    }

    public function update() {

        $keys = '';
        foreach ($this->getData() as $key => $value) {
            $keys .= $key . ', ';
            $values .= "'" . DB::esc($value) . "'," ;
        }

        $keys = substr(trim($keys), 0, -1);
        $values = substr(trim($values), 0, -1);
        $query = "
			INSERT INTO " . $this->init . " (" . $keys . ")
			VALUES (
				" . $values . "
			) ON DUPLICATE KEY UPDATE last_activity = NOW()";


        DB::query($query);
    }

    protected function _init($table) {
        $this->init = $table;
    }

}
