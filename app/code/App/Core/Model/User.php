<?php

namespace Chat\App\Core\Model;

use Chat\Framework\Db\DB as DB;

class User {

    protected $name = ''; 
	protected $gravatar = '';

    public function __construct(array $options) {
		
			foreach ($options as $k => $v) {
				
				if (isset($this->$k)) {
					$this->$k = $v;
				}
			}
    }
	
	public function test(){
		return 1;
	}

    public function save() {
		
        DB::query("
			INSERT INTO users (name, gravatar)
			VALUES (
				'" . DB::esc($this->name) . "',
				'" . DB::esc($this->gravatar) . "'
		)");
		
        return DB::getMySQLiObject();
    }

    public function update() {
        DB::query("
			INSERT INTO users (name, gravatar)
			VALUES (
				'" . DB::esc($this->name) . "',
				'" . DB::esc($this->gravatar) . "'
			) ON DUPLICATE KEY UPDATE last_activity = NOW()");
    }
	
	public function getUserFromSession(){
		if($_SESSION['user']){
			$this->name = $_SESSION['name'];
			$this->gravatar = $_SESSION['gravatar'];
			return true;
		}else{
			return false;
		}
	}
	
	
	public static function gravatarFromHash($hash, $size = 23) {
        return 'http://www.gravatar.com/avatar/' . $hash . '?size=' . $size . '&amp;default=' .
                urlencode('http://www.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?size=' . $size);
    }

}
