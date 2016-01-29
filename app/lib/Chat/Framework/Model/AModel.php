<?php

namespace Chat\Framework\Model;

use Chat\Framework\Model\Resource as Resource;
use Chat\Framework\Model\Object as Object;
use Chat\Framework\Db\DB as DB;

class AModel extends Object {

    protected $init;
    protected $query;
    protected $delete_id;

    public function __construct() {
        $this->insertData(Resource::initTable($this->init));
        return $this;
    }
    
    protected function _init($table) {
        $this->init = $table;
    }

    

    public function save() {
        $data = $this->getData();       
        if(!$this->query){
            $this->query = 'save';
        }
        
        Resource::save($this->init, $data, $this->query);
        

        return $this;
    }

    public function update() {
        $this->query = 'update';
        $this->save();
        return $this;
    }
    
    public function delete($delete_id) {
        $this->query = 'delete';
        $this->delete_id = $delete_id;
        $data = $this->_data[$delete_id];
        
        foreach($this->_data as $key => $value){
            if($value != ''){
                $this->_data[$key] = '';
            }
            
        }
        $this->_data = array($this->delete_id => $data); 
        $this->save();
    }

    public function load($id) {
        $this->insertData(Resource::load($id));
        return $this;
    }

    public function getCollection($order_by, $sort, $limit) {
        $query = "SELECT * FROM " . $this->init;

        if ($order_by) {
            $query .= " ORDER BY " . $order_by;
        }
        if ($sort) {
            $query .= " " . $sort;
        }

        if ($limit) {
            $query .= " LIMIT " . $limit;
        }


        return DB::query($query);
    }

}
