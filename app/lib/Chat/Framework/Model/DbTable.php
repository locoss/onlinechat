<?php

namespace Chat\Framework\Model;

class DbTable{
    
    public static function getArray($init){
        $tables = array(
            'chat' => array('id' => '', 'gravatar' => ''), 
            'users'=> array('name' => '', 'gravatar' => '')
        );
        return $tables[$init] ;
    }
    public static function getArrayById($id){
        $_array = array(
            1 => array('name' => 'Alex', 'email' => 'lidhen@list.ru'),  
        );
        
        return (isset($_array[$id])) ? $_array[$id] : null;
    }
}
