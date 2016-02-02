<?php

namespace Chat\App\Core\Model;

use Chat\Framework\Model\AModel as AModel;
use Chat\App\Core\Helper\Helper as Helper;

class User extends AModel {

    public function __construct() {
        $this->_init('users');
        parent::__construct();
    }

    public function getUserFromSession() {
        if ($_SESSION['user']) {
            $this->name = $_SESSION['name'];
            $this->gravatar = $_SESSION['gravatar'];
        } 
        return $this;
    }
    
    
    
     public function getResponse(){
        $users = array();
        foreach($this->_resource as $user){
            $user->gravatar = Helper::gravatarFromHash($user->gravatar, 30);
            $users[] = $user;
        }        

        $response = array(
            'users' => $users,
            'total' => count($users)
        );

        return json_encode($response);
    }
    
    public function getLoginResponse(){
        $response = array(
                'status' => 1,
                'name' => $this->getName(),
                'gravatar' => Helper::gravatarFromHash($this->getGravatar())
            );
        
        return json_encode($response);
    }
    
   

}
