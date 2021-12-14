<?php


namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
require dirname(__DIR__) . "/database/Rooms.php";
require dirname(__DIR__) . "/database/User.php";

class Notif implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        echo 'Server Started';
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        echo 'Server Started';

        $this->clients->attach($conn);

        $querystring = $conn->httpRequest->getUri()->getQuery();

        parse_str($querystring, $queryarray);

        $user_object = new \User;

        $user_object->setUserToken($queryarray['token']);

        $user_object->setUserConnectionId($conn->resourceId);

        $user_object->update_user_connection_id();

        $user_object->update_user_connection_id_connection();
         
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
      
        $data = json_decode($msg, true);

        if($data['command'] == "private")
        {
            //private chat

            $private_noti_object = new \Rooms;

            $private_noti_object->setToUserId($data['to_user_id']);

            $private_noti_object->setUserId($data['userid']);

            $private_noti_object->setCommentSubject($data['comment_subject']);

            $private_noti_object->setCommentText($data['comment_text']);

            $timestamp = date('Y-m-d h:i:s');

            $private_noti_object->setTimestamp($timestamp);

            $private_noti_object->setCommentStatus($data['comment_status']);

            $noti_message_id = $private_noti_object->save_chat();

            $user_object = new \User;          

            $user_object->setUserId($data['userid']);

            $sender_user_data = $user_object->get_user_data_by_id();

            $user_object->setUserId($data['to_user_id']);

            //$receiver_user_data = $user_object->get_user_data_by_id();

            $receiver_user_data = $user_object->get_user_data_by_id_connection();

            $sender_user_name = $sender_user_data['user_name'];

            $data['datetime'] = $timestamp;

            $receiver_user_connection_id = $receiver_user_data['user_connection_id'];

            foreach($this->clients as $client)
            {
                if($from == $client)
                {
                    $data['from'] = 'Me';
                }
                else
                {
                    $data['from'] = $sender_user_name;
                }

                //$client->send(json_encode($data));

                if($client->resourceId == $receiver_user_connection_id || $from == $client)
                {   
                    $client->send(json_encode($data));
                }/*
                else
                {
                    $private_noti_object->setCommentStatus('0');
                    $private_noti_object->setCommentId($noti_message_id);

                    $private_noti_object->update_chat_status();
                }*/
            }
        }
    }    
        

        /*$noti_object = new \Rooms;

        $noti_object->setCommentId($data['comment_id']);

        $noti_object->setToUserId($data['to_userid']);

        $noti_object->setUserId($data['userid']);

        $noti_object->setCommentSubject($data['comment_subject']);

        $noti_object->setCommentText($data['comment_text']);

        $noti_object->setCommentStatus($data['comment_status']);

        $noti_object->save_chat();


        foreach ($this->clients as $client) {

            $client->send(json_encode($data));
        }*/
    

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}

?>