<?php

namespace Chat\App\Core\View;

class Layout {

    public $template;
    protected $_content;
    protected $_head;
    
    public function __construct() {
        
    }
    
    
    public function getContent(){
        
         include ($this->_content);
    }
    
    public function getHead(){
        include ($this->_head);
    }
    
    public function setContent($content){
        $this->_content = __DIR__ . '/Template/' . $content .  '.phtml';
        return $this;
    }
    
    public function setHead($head){
        $this->_head = __DIR__ . '/Template/' . $head .  '.phtml';
        return $this;
    }
    
    public function generate(){
        include(__DIR__ . '/Layout/layout.phtml');
    }
    
    
    

}
