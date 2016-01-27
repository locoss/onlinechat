<?php

namespace Chat\App\Core\View;

class Layout {

    public $template;
    
    public function __construct() {
        include(__DIR__ . '/Layout/layout.phtml');
    }
    
    
    public function getContent($template_name){
        include (__DIR__ . '/Template/' . $template_name . '.phtml');
    }

}
