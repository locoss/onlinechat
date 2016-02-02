<?php

namespace Chat\App\Core\Model;

use Chat\Framework\Db\DB as DB;

class Chat {

    protected $text = '', $author = '', $gravatar = '';

    public function __construct(array $options) {

        foreach ($options as $k => $v) {
            if (isset($this->$k)) {
                $this->$k = $v;
            }
        }
    }

    public function save() {
        DB::query("
			INSERT INTO chat (author, gravatar, text)
			VALUES (
				'" . DB::esc($this->author) . "',
				'" . DB::esc($this->gravatar) . "',
				'" . DB::esc($this->text) . "'
		)");



        return DB::getMySQLiObject();
    }
    
    
    
    /*
     * 
     * namespace Chat\App\Core\Model;

use Chat\Framework\Db\DB as DB;
use Chat\Framework\Model\AModel as AModel;

class Chat extends AModel {

    public function __construct() {
        $this->_init('chat');
        parent::__construct();
    }

}

     */

}
