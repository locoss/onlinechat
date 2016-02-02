<?php
namespace Chat\Framework\Model\AbstractData;
use Chat\Framework\Model\AbstractData\ResourceInterface as ResourceInterface;

abstract class AbstractData implements ResourceInterface{
    protected static $table;
    protected static $_data;
    protected static $query;
    protected static $init;
}