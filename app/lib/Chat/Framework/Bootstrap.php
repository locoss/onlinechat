<?php
namespace Chat\Framework;

class Bootstrap {
	public static function app(){
		
		// собрать конфиг дб
		// собрать конфиг модулей
		// роутеры, таблицы и т.п.
		// отправить все это на контроллер
		
		$controller = new Controller\Controller();
	}
}