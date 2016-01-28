<?php

namespace Chat\App\Core\Model;

use Chat\Framework\Db\DB as DB;
use Chat\Framework\Model\AModel as AModel;

class User extends AModel {

    public function __construct() {
        $this->_init('users');
        parent::__construct();
    }

    public function getUserFromSession() {
        if ($_SESSION['user']) {
            $this->name = $_SESSION['name'];
            $this->gravatar = $_SESSION['gravatar'];
            return true;
        } else {
            return false;
        }
    }

}
