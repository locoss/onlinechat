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
            $this->_resource = $returned_data;
            $this->_object = Resource::getObject();
        }
        
        return $this;
    }
    
    public function getResponse(){
        $collection = array();
        foreach($this->_resource as $object){
            $collection[] = $object;
        }

        $response = array(
            $this->init => $collection,
            'total' => count($collection)
        );

        return json_encode($response);
    }

    

    public function save() {
        $data = $this->getData();       
        if(!$this->query){
            $this->query = 'save';
        }
        $this->_resource = Resource::save($this->init, $data, $this->query);
        //$this->_object = DB::getMySQLiObject();
        $this->_object = Resource::getObject();
        
        return $this;
    }

    public function update() {
        $this->query = 'update';
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

    public function getCollection($array) {
        $this->query = 'collection';
        $this->_data = $array;
        
        $this->collection();
        return $this;
       
    }
    
    public function getObject(){
        return $this->_object;
    }
    
    public function getResource(){
        return $this->_resource;
    }

}
