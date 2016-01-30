<?php

namespace Chat\App\Core\Model;

use Chat\Framework\Model\AModel as AModel;
use Chat\App\Core\Helper\Helper as Helper;

class Chat extends AModel {

    public function __construct() {
        $this->_init('chat');
        parent::__construct();
    }

    public function getChatSubmitResponse() {

        if ($this->_object instanceof \mysqli) {
            $insertID = $this->_object->insert_id;
        } else {
            $insertID = 1;
        }

        $response = array(
            'status' => 1,
            'insertID' => $insertID
        );

        return json_encode($response);
    }

    public function getChatsResponse() {
        $chats = array();

        foreach ($this->getResource() as $chat) {
            $chat->time = array(
                'hours' => gmdate('H', strtotime($chat->ts)),
                'minutes' => gmdate('i', strtotime($chat->ts))
            );

            $chat->gravatar = Helper::gravatarFromHash($chat->gravatar);

            $chats[] = $chat;
        }

        $response = array('chats' => $chats);

        return json_encode($response);
    }

}
