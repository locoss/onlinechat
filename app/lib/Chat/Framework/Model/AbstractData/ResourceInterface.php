<?php
namespace Chat\Framework\Model\AbstractData;

interface ResourceInterface {
    
    public static function getArray($init);
    
    public static function collection($init, $data, $query);
    
    public static function load($id);
    
    public static function save($init, $data, $query);
    
    public static function getObject();
}
