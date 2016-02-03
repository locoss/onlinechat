<?php

namespace Chat\App\Core\Controller;

//use Chat\Framework\Db\DB as DB;
use Chat\Framework\View\View as View;
use \Chat\App\Core\Helper\Helper as Helper;

class Index {

    public $view;
    public $response;
    public $_redirect;

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

    public function loginAction() {

        if (isset($_POST['name']) && isset($_POST['password'])) {
            if (trim($_POST['name']) != '' && trim($_POST['password'])) {
                $name = trim($_POST['name']);
                $email = trim($_POST['password']);
            }
        }

        $gravatar = md5(strtolower(trim($email)));

        $user = new \Chat\App\Core\Model\User();
        $user->loadByFieldName('gravatar', $gravatar);
        try {
            if ($user->getObject()) {
				if ($user->getObject()->loaded) {
					$session_data = array(
						'name' => $user->getName(),
						'gravatar' => Helper::gravatarFromHash($user->getGravatar())
					);

					if ($user->getHomepage()) {
						array_push($session_data, array('homepage' => $user->getHomepage()));
					}
					$_SESSION['user'] = $session_data;
					$this->response = $user->getLoginResponse();
					// header('Location: ' . $_SERVER['HTTP_REFERER']);
					//header('location: index');
					//$this->indexAction();

					$this->_redirect = 'index';
					return $this;
				}
            } else {
                throw new \Exception('User with this name is not registered yet');
            }
        } catch (\Exception $e) {
            // $this->response = json_encode(array('error' => $e->getMessage()));
            \Chat\Framework\Bootstrap::register('error', $e->getMessage());
            $this->indexAction();
        }
    }

    public function indexAction() {

        $view = new View();
        $view->setLayout();
        if (isset($_SESSION['user']['name'])) {
            $view->getLayout()->setContent('content')->setHead('head');
        } else {
            $view->getLayout()->setContent('login_content')->setHead('login_head');
        }



        $this->view = $view;
    }

    public function registerAction() {
        if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password'])) {
            if (trim($_POST['name']) != '' && trim($_POST['email']) != '' && trim($_POST['password']) != '') {
                $name = trim($_POST['name']);
                $email = trim($_POST['email']);
                $password = trim($_POST['password']);
            }
        }

        try {
            if (!isset($name) && !isset($email) && !isset($password)) {
                throw new \Exception('Fill in all the required fields.');
            }

            if (!filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Your email is invalid.');
            }

            $gravatar = md5(strtolower(trim($password)));
            $user = new \Chat\App\Core\Model\User();
            $user->setName($name);
            $user->setEmail($email);
            $user->setGravatar($gravatar);
            //$homepage = 'lol';
            if (isset($_POST['homepage'])) { // $_POST['homepage]
                $url = $_POST['homepage'];
                $url = Helper::getRightUrl(trim($url)); // $_POST['homepage]

                if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
                    $user->setHomepage($url);
                }
            }

            $user->save();

            if ($user->getObject()->affected_rows != 1) {
                throw new \Exception('This email is in use.');
            }

            $session_data = array(
                'name' => $user->getName(),
                'gravatar' => Helper::gravatarFromHash($user->getGravatar())
            );

            if ($user->getHomepage()) {
                array_push($session_data, array('homepage' => $user->getHomepage()));
            }
            $_SESSION['user'] = $session_data;
            $this->response = $user->getLoginResponse();
            // header('Location: ' . $_SERVER['HTTP_REFERER']);
            //header('location: index');
            $this->_redirect = 'index';
            return $this;
        } catch (\Exception $e) {
            // $this->response = json_encode(array('error' => $e->getMessage()));
            \Chat\Framework\Bootstrap::register('error', $e->getMessage());
            $this->indexAction();
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
        //$user->setName($session_user_name);
        //$user->load('name', $session_user_name);
        //$user->delete('name');

        session_unset();
        $this->response = json_encode(array('status' => 1));
        //header('Location: ' . $_SERVER['HTTP_REFERER']);
        $this->_redirect = $_SERVER['HTTP_REFERER'];
    }

    /* public function logoutAction() {

      $session_user_name = $_SESSION['user']['name'];

      $user = new \Chat\App\Core\Model\User();
      $user->setName($session_user_name);
      //$user->load('name', $session_user_name);
      $user->delete('name');

      session_unset();
      $this->response = json_encode(array('status' => 1));
      header('Location: ' . $_SERVER['HTTP_REFERER']);
      } */

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
                    'limit' => 10
        ));

        $this->response = $user->getResponse();

        return $this->response;
    }

    public function getchatsAction() {
        $chat_model = new \Chat\App\Core\Model\Chat();
        $chat_model->getCollection(array(
            'order' => 'id',
            'sort' => 'ASC',
        ));

        $this->response = $chat_model->getChatsResponse();
    }

    public function submitchatAction() {
        $chatText = $_POST['chatText'];
        $file = $_POST['file'];
        try {
            if (!$_SESSION['user']) {
                throw new \Exception('You are not logged in');
            }

            if (!$chatText) {
                throw new \Exception('You haven\' entered a chat message.');
            }

            $chatText = Helper::clean($chatText);
            $chat = new \Chat\App\Core\Model\Chat();
            $chat->setFilename($file);
            $chat->setAuthor($_SESSION['user']['name']);
            $chat->setGravatar($_SESSION['user']['gravatar']);
            $chat->setText($chatText);
            $chat->save();

            $chat->getChatSubmitResponse();
            $this->response = $chat->getChatSubmitResponse();
            return $this->response;
        } catch (\Exception $e) {
            $this->response = json_encode(array('error' => $e->getMessage()));
        }
    }

    public function savefileAction() {
        $path = BASE_DIR . '/media/files/';

        if (isset($_FILES['file']['name'])) {
            if (!$_FILES['file']['error']) {
                move_uploaded_file($_FILES['file']['tmp_name'], $path . $_FILES['file']['name']);
            }
        }
    }

}
