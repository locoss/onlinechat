<?php 

namespace Chat\App\Core\Helper;

class Helper {
	
	
	public static function clean($value = "") {
		$value = trim($value);
		$value = stripslashes($value);
		$value = strip_tags($value);
		$value = htmlspecialchars($value);
		
		return $value;
	}
}