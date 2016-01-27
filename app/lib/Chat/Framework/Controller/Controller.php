<?php
namespace Chat\Framework\Controller;

//use Chat/Framework\View\View;
//use Chat/Framework\Model\Model;


class Controller {
	
	public $view;
	public $model;
	
	public function __construct(){
		$this->view = $this->generate();
	}
	
	public function generate(){
		$this->layout = 'index.phtml';
		$this->template = '\Chat\App\Code\View\template\index.phtml';
		include $this->layout;
	}
}