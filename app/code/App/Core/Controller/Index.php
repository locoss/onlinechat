<?php

namespace Chat\App\Core\Controller;

use Chat\Framework\Db\DB as DB;
use Chat\Framework\View\View as View;
use \Chat\App\Core\Helper\Helper as Helper;

class Index {

    public $view;
    public $response;

    public function __construct($action) {
        try{
            if(method_exists($this, $action)){
                $this->$action();
            }else{
                throw new \Exception('Wrong Action. You have been redirected to Main Page');
            }
        }catch(\Exception $e){
            //$this->response = $e->getMessage();
            $this->indexAction();
        }
    }

    public function indexAction() {
        $this->view = new View();
    }

    public function loginAction() {
        if(isset($_POST['name']) && isset($_POST['name'])){
            if(trim($_POST['name']) != '' && trim($_POST['email'])){
                $name = trim($_POST['name']);
                $email = trim($_POST['email']);
            }
            
        }
        
         //$name = 'Alex';
         //$email = 'lidhen@list.ru';
        try {
            if (!isset($name) || !isset($email)) {
                throw new \Exception('Fill in all the required fields.');
            }

            if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Your email is invalid.');
            }

            $gravatar = md5(strtolower(trim($email)));
            $user = new \Chat\App\Core\Model\User();

            $user->setName($name);
            $user->setGravatar($gravatar);
            $user->save();
            if ($user->getMysqlObject()->affected_rows != 1) {
                throw new \Exception('This nick is in use.');
            }


            $response = array(
                'status' => 1,
                'name' => $name,
                'gravatar' => self::gravatarFromHash($gravatar)
            );

            $_SESSION['user'] = array(
                'name' => $name,
                'gravatar' => self::gravatarFromHash($gravatar)
            );

            $this->response = json_encode($response);

            return $this->response;
        } catch (\Exception $e) {
            $this->response = json_encode(array('error' => $e->getMessage()));
        }
    }

    public function checkloggedAction() {
        $response = array('logged' => false);

        if ($_SESSION['user']['name']) {
            $response['logged'] = true;
            $response['loggedAs'] = array(
                'name' => $_SESSION['user']['name'],
                'gravatar' => self::gravatarFromHash($_SESSION['user']['gravatar'])
            );
        }
        $this->response = json_encode($response);
        return $this->response;
    }

    public function logoutAction() {

        $session_user_name = $_SESSION['user']['name'];
        
        $user = new \Chat\App\Core\Model\User();
        $user->setName($session_user_name);
        
        $user->delete('name');

        session_unset();
        $this->response = json_encode(array('status' => 1));
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        
    }

    public function submitchatAction() {
   
        
       $chatText = $_POST['chatText'];
        try {
           
            if (!$_SESSION['user']) {
                throw new \Exception('You are not logged in');
            }


            if (!$chatText) {
                throw new \Exception('You haven\' entered a chat message.');
            }
            $chatText = Helper::clean($chatText);
            $chat = new \Chat\App\Core\Model\Chat();
            
            $chat->setAuthor($_SESSION['user']['name']);
            $chat->setGravatar($_SESSION['user']['gravatar']);
            $chat->setText($chatText);

            $insertID = $chat->save()->insert_id;
            $response = array(
                'status' => 1,
                'insertID' => $insertID
            );
            $this->response = json_encode($response);
            return $this->response;
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }

    public function getusersAction() {
        $user = new \Chat\App\Core\Model\User();
        
        /*if ($_SESSION['user']['name']) {
            $user->setName($_SESSION['user']['name']);
            $user->update();
        }*/
        //DB::query("DELETE FROM chat WHERE ts < SUBTIME(NOW(),'0:5:0')");
        //DB::query("DELETE FROM users WHERE last_activity < SUBTIME(NOW(),'0:0:30')");

        $collection = $user->getCollection('name', 'ASC', 18);
        
        $result = $user->getResource();
        
        // $result = DB::query('SELECT * FROM users ORDER BY name ASC LIMIT 18');
        var_dump($collection);
        $users = array();
        
        while ($user = $result->fetch_object()) {
            $user->gravatar = self::gravatarFromHash($user->gravatar, 30);
            $users[] = $user;
        }
        
        

        $response = array(
            'users' => $users,
            'total' => DB::query('SELECT COUNT(*) as cnt FROM users')->fetch_object()->cnt
        );

        $this->response = json_encode($response);
        return $this->response;
    }

    public function getchatsAction() {
        $result = DB::query('SELECT * FROM chat ORDER BY id ASC');
        $chats = array();
        while ($chat = $result->fetch_object()) {
            $chat->time = array(
                'hours' => gmdate('H', strtotime($chat->ts)),
                'minutes' => gmdate('i', strtotime($chat->ts))
            );

            $chat->gravatar = self::gravatarFromHash($chat->gravatar);

            $chats[] = $chat;
        }

        $response = array('chats' => $chats);
        $this->response = json_encode($response);
    }

    public static function gravatarFromHash($hash, $size = 23) {
        return 'http://www.gravatar.com/avatar/' . $hash . '?size=' . $size . '&amp;default=' .
                urlencode('http://www.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?size=' . $size);
    }

}
