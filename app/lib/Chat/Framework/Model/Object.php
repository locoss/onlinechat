<?php 

namespace Chat\Framework\Model;

class Object  {
    protected $_data;

    public function __call($method, $args) {
        switch (substr($method, 0, 3)) {
            case 'get' :
                $key = $this->_format(substr($method, 3));
                $data = $this->getData($key, isset($args[0]) ? $args[0] : null);
                return $data;

            case 'set' :
                $key = $this->_format(substr($method, 3));
                $result = $this->setData($key, isset($args[0]) ? $args[0] : null);
                return $result;
        }
        echo 'This Method is undefined';
    }

    protected function _format($name) {
        $result = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
        return $result;
    }

    protected function getData($key = null) {
        if(!$key){
            return $this->_data;
        }
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    protected function setData($key, $value) {
        if(isset($this->_data[$key]))
            $this->_data[$key] = $value;
    }
    
    protected function insertData($data){
        $this->_data = $data;
    }
    
}