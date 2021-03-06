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
        $object = $this->getObject();
        $insertID = $object->insert_id;
        $response = array(
            'status' => 1,
            'insertID' => $insertID
        );

        return json_encode($response);
    }

    public function getChatsResponse() {
        $chats = array();
        if (is_array($this->getResource())) {
            foreach ($this->getResource() as $chat) {
                $chat->time = array(
                    'hours' => gmdate('H', strtotime($chat->ts)), 
                    'minutes' => gmdate('i', strtotime($chat->ts))
                  //  'hours' => gmdate('H', strtotime($chat->ts) + 60 * 60),  // if wrong time settings on server
                  //  'minutes' => gmdate('i', strtotime($chat->ts) + 60 * 60)  // if wrong time settings on server
                );
                $chat->gravatar = Helper::gravatarFromHash($chat->gravatar);
                $chats[] = $chat;
            }
            $response = array('chats' => $chats);

            return json_encode($response);
        }
        
        return json_encode(array('chats' => $chats));
    }

}
