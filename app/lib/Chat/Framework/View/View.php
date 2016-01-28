<?php

namespace Chat\Framework\View;

class View {

    protected $template;
    protected $layout;

    public function __construct() {
        //$this->generate();
    }

    public function generate() {
    $layout =  $this->layout;
    $view_object = new $layout();
		
    }
    
    
    public function setLayout($view_info){
        if($view_info){
            
            $this->layout = $view_info['layout'];
            $this->setTemplate($view_info['template']);
        }
        //return $this->generate();
    }
    
    protected function setTemplate($template_path_file){
        $this->template = $template_path_file;
        
    }
    
    public function getLayout(){
        return $this->layout;
    }
    
    public function getTemplate(){
        return $this->template;
    }

}
