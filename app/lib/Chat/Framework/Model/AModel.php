<?php

namespace Chat\Framework\Model;

use Chat\Framework\Model\Resource as Resource;
use Chat\Framework\Model\Object as Object;
use Chat\Framework\Db\DB as DB;

class AModel extends Object {

    protected $init;
    protected $query;
    protected $delete_id;
    protected $_resource;
    protected $_object;

    public function __construct() {
       // $this->insertData(Resource::initTable($this->init));
       // return $this;
    }
    
    protected function _init($table) {
        $this->init = $table;
    }
    
    protected function collection(){
        $data = $this->getData();
        $returned_data = Resource::collection($this->init, $data, $this->query);
        if($returned_data){
            $this->_data = $returned_data;
        }
        return $this;
    }

    

    public function save() {
        $data = $this->getData();       
        if(!$this->query){
            $this->query = 'save';
        }
        
        $this->_resource = Resource::save($this->init, $data, $this->query);

        $this->_object = DB::getMySQLiObject();
        
        return $this;
    }

    public function update() {
        $this->query = 'update';
        //$this->save();
        return $this->save();
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
        $this->query = 'collection';
        $this->_data = array(
            'order' => $order_by,
            'sort' => $sort,
            'limit' => $limit
        );
        
        $this->collection();
        
        return $this;
       
    }
    
    public function getMysqlObject(){
        return $this->_object;
    }
    
    public function getResource(){
        return $thix->_resource;
    }

}
