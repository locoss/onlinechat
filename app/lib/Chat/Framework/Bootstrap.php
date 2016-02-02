<?php

namespace Chat\Framework;

final class Bootstrap {

    protected static $config;
    protected $controller;
    static private $_registry  = array();

    public static function app() {
        //session_name('chat');
        session_start();
        $config = self::getConfig();
        \Chat\Framework\Db\DB::init($config['db']['connection']);

        $controller = new Controller\Front($config);
        //$controller->setLayout();
    }

    public static function setConfig() {
        $db = require_once BP . "/etc/db_config.php";
        $routers = require_once BP . "/etc/routers.php";
        $tables = require_once BP . "/etc/tables.php";

        self::$config = array(
            'view' => array(
                'layout' => '\Chat\App\Core\View\Layout',
                'template' => '\Chat\App\Core\View\Template'
            ),
            'db' => array(
                'connection' => $db,
                'tables' => $tables
            ),
            'routers' => $routers
        );
    }

    public static function getConfig() {
        if (!self::$config) {
            self::setConfig();
        }
        return self::$config;
    }

    public static function BaseUrl() {
        $prefix = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';

        return $prefix . URL;
    }
    
    public static function register($key, $data){
        self::$_registry[$key] = $data;
    }
    
    public static function registry($key){
        if(isset(self::$_registry[$key])){
            return self::$_registry[$key];
        }else{
            return false;
        }
    }
    
    public static function unregister($key){
         if(isset(self::$_registry[$key])){
            unset(self::$_registry[$key]);
            return true;
        }else{
            return false;
        }
    }

}
