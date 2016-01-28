<?php

namespace Chat\Framework\Controller;

use Chat\Framework\View\View as View;

//use Chat\Framework\Model\Model as Model;


class Front {

    protected $view;
    protected $model;
    protected $config;

    public function __construct($config) {
        //$this->view = new View();
        //$this->model = new Model();
        $this->config = $config;
        $this->dispatch();
    }

    public function dispatch() {
        $request_uri = substr(strtolower($_SERVER["REQUEST_URI"]), 1);
		$server_host = $_SERVER['HTTP_HOST'];
        
		$request = explode('/', $request_uri);
		if($server_host === 'localhost'){
			array_shift($request);
			
		}
		
        $routers = $this->config['routers'];
        if (array_key_exists($request[0], $routers)) {
            $class_name = $routers[$request[0]];
            $action = $request[1] . "Action";
            $controller = new $class_name($action);
        }else{
			$controller = new \Chat\App\Core\Controller\Index('indexAction');
		}
        if ($controller->view) {
            $this->view = $controller->view;
            $this->setLayout();
            $this->generateLayout();
        }
		if($controller->response){
			echo $controller->response;
		}
    }

    public function setLayout() {
        $view = $this->getView();
        $view->setLayout($this->config['view']);
    }

    public function generateLayout() {
        $view = $this->getView();
        $view->generate();
    }

    protected function getView() {
        return $this->view;
    }

}
