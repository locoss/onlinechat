<?php

namespace Chat\App\Core\Controller;

//use Chat\Framework\Db\DB as DB;
use Chat\Framework\View\View as View;
use \Chat\App\Core\Helper\Helper as Helper;

class Index {

    public $view;
    public $response;

    public function __construct($action) {
        try {
            if (method_exists($this, $action)) {
                $this->$action();
            } else {
                throw new \Exception('Wrong Action. You have been redirected to Main Page');
            }
        } catch (\Exception $e) {
            //$this->response = $e->getMessage();
            $this->indexAction();
        }
    }

    public function indexAction() {
        $this->view = new View();
    }

    public function loginAction() {
        if (isset($_POST['name']) && isset($_POST['email'])) {
            if (trim($_POST['name']) != '' && trim($_POST['email'])) {
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

            if ($user->getObject()->affected_rows != 1) {
                throw new \Exception('This nick is in use.');
            }
            $_SESSION['user'] = array(
                'name' => $user->getName(),
                'gravatar' => Helper::gravatarFromHash($user->getGravatar())
            );
            $this->response = $user->getLoginResponse();

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
                'gravatar' => Helper::gravatarFromHash($_SESSION['user']['gravatar'])
            );
        }
        $this->response = json_encode($response);
        return $this->response;
    }

    public function logoutAction() {

        $session_user_name = $_SESSION['user']['name'];

        $user = new \Chat\App\Core\Model\User();
        $user->setName($session_user_name);
        //$user->load('name', $session_user_name);
        $user->delete('name');

        session_unset();
        $this->response = json_encode(array('status' => 1));
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    public function getusersAction() {
        $user = new \Chat\App\Core\Model\User();

        if (isset($_SESSION['user']['name'])) {
            $user->setName($_SESSION['user']['name']);
            $user->update();
        }
        //DB::query("DELETE FROM chat WHERE ts < SUBTIME(NOW(),'0:5:0')");
        //DB::query("DELETE FROM users WHERE last_activity < SUBTIME(NOW(),'0:0:30')");

        $user->getCollection(
                array(
                    'order' => 'name',
                    'sort' => 'ASC',
                    'limit' => 18
        ));
        $this->response = $user->getResponse();

        return $this->response;
    }

    public function getchatsAction() {
        $chat_model = new \Chat\App\Core\Model\Chat();
        $chat_model->getCollection(array(
            'order' => 'id',
            'sort' => 'ASC'
        ));

        $this->response = $chat_model->getChatsResponse();
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
            $chat->save();

            $chat->getChatSubmitResponse();
            $this->response = $chat->getChatSubmitResponse();
            return $this->response;
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }

}
