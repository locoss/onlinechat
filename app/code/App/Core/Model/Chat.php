<?php

namespace Chat\App\Core\Model;

use Chat\Framework\Db\DB as DB;
use Chat\Framework\Model\AModel as AModel;


class Chat extends AModel {

   
public function __construct() {
        $this->_init('chat');
        parent::__construct();
    }
   

    
}