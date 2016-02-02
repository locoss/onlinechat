<?php

namespace Chat\Framework\View;

class View {

    protected $template;
    protected $layout;

    public function __construct() {
        //$this->generate();
    }

    public function generate($layout) {
        $layout = $this->layout;
        //$view_object = new $layout();
        $layout->generate();
    }

    public function setLayout() {
        $config = \Chat\Framework\Bootstrap::getConfig();
        $view_info = $config['view'];
        if ($view_info) {
            $this->layout = new $view_info['layout']();
            //$this->setTemplate($view_info['template']);
        }
        return $this;
        //return $this->generate();
    }

    protected function setTemplate($template_path_file) {
        $this->template = $template_path_file;
    }

    public function getLayout() {
        return $this->layout;
    }

    public function getTemplate() {
        return $this->template;
    }

}
