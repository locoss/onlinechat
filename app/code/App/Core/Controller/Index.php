<?php
namespace Chat\App\Core\Controller;

use Chat\Framework\Db\DB as DB;
use Chat\Framework\View\View as View;
use \Chat\App\Core\Helper\Helper as Helper;

class Index {
    
    public $view;
    public $response;
    public function __construct($action) {
        $this->$action();
    }
    
    public function indexAction(){
       $this->view = new View();
    }
    
  public function loginAction() {
      $name = $_POST['name'];
      $email = $_POST['email'];
	  try{
      if (!$name || !$email) {
            throw new \Exception('Fill in all the required fields.');
        }

        if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
			
            //throw new \Exception('Your email is invalid.');
        }
        
        $gravatar = md5(strtolower(trim($email)));
       
        $user = new \Chat\App\Core\Model\User(array(
            'name' => $name,
            'gravatar' => $gravatar
        ));
        
        if ($user->save()->affected_rows != 1) {
           // throw new \Exception('This nick is in use.');
        }

        $response =  array(
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
		
		
		}catch(\Exception $e){
		  $e->getMessage();
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
		
        DB::query("DELETE FROM users WHERE name = '" . DB::esc($_SESSION['user']['name']) . "'");

        $_SESSION = array();
        unset($_SESSION);
		session_unset();
		$this->response = json_encode(array('status' => 1));
		header('Location: ' . $_SERVER['HTTP_REFERER']);
        //return $this->response;
    }

    public function submitchatAction() {
		$chatText = $_POST['chatText'];
		try{
        if (!$_SESSION['user']) {
            throw new \Exception('You are not logged in');
        }

        if (!$chatText) {
            throw new \Exception('You haven\' entered a chat message.');
        }
		$chatText = Helper::clean($chatText);
        $chat = new \Chat\App\Core\Model\Chat(array(
            'author' => $_SESSION['user']['name'],
            'gravatar' => $_SESSION['user']['gravatar'],
            'text' => $chatText
        ));

        
        $insertID = $chat->save()->insert_id;
        $response =  array(
            'status' => 1,
            'insertID' => $insertID
        );
		$this->response = json_encode($response);
		return $this->response;
		}catch(\Exception $e){
			$e->getMessage();
		}
    }

    public function getusersAction() {
        if ($_SESSION['user']['name']) {
            $user = new \Chat\App\Core\Model\User(array('name' => $_SESSION['user']['name']));
            $user->update();
        }

        DB::query("DELETE FROM chat WHERE ts < SUBTIME(NOW(),'0:5:0')");
        DB::query("DELETE FROM users WHERE last_activity < SUBTIME(NOW(),'0:0:30')");

        $result = DB::query('SELECT * FROM users ORDER BY name ASC LIMIT 18');

        $users = array();
        while ($user = $result->fetch_object()) {
            $user->gravatar = self::gravatarFromHash($user->gravatar, 30);
            $users[] = $user;
        }

        $response =  array(
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