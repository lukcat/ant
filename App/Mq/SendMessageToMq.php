<?php

namespace App\Mq;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class SendMessageToMq {
    private $host;
    private $port;
    private $username;
    private $password;
    private $exchange;
    private $queue;

    public function __construct($connectPara) {
        $this->host     = $connectPara['rabbitMq']['host'];
        $this->port     = $connectPara['rabbitMq']['port'];
        $this->username = $connectPara['rabbitMq']['username'];
        $this->password = $connectPara['rabbitMq']['password'];
        $this->exchange = $connectPara['rabbitMq']['exchange'];
        $this->queue    = $connectPara['rabbitMq']['queue'];
        // var_dump($connectPara);die();
    }

    /*
     * Send messsage to rabbitMq
     * @param data array, which contains {ComplaintId=>'id', UserId=>'id', ComplaintType=>'type', CreateTime=>'yyyy-MM-dd hh:mm:ss'}
     */
    public function send($data) {
        // New a connection to rabbitMq
        try {
            $con = new AMQPStreamConnection($this->host, $this->port, $this->username, $this->password);
        } catch (\Exception $e) {
            return false;
        }


        // Create a channel
        $channel = $con->channel();

        // declear a queue
        $channel->queue_declare($this->queue, false, true, false, false);

        // generate bind key, MB.V2.RP.complaintid
        //var_dump($data);die();
        $bindKey = 'MB.V2.RP.' . $data['ComplaintId'];

        // Bind queue to exchange with bindKey
        $channel->queue_bind($this->queue, $this->exchange, $bindKey);

        // Convert json object to json string
        $jsonStr = json_encode($data);

        $property = array('type' => '1', 'priority'=> 1, 'expiration' => 600000, 'content_type' => 'utf-8');
        $msg = new AMQPMessage($jsonStr, $property);

        // Send message to exchange
        $channel->basic_publish($msg, $this->exchange, $bindKey);

        // Close connection
        $channel->close();
        $con->close();
    }

    public function filterMessage($data) {
        //var_dump($data);die();
        $res = array(
                'ComplaintId'   => $data['ComplaintId'],
                'UserId'        => $data['UserId'],
                'UserName'      => $data['UserName'],
                'ComplaintType' => $data['ComplaintType'],
                'CreateTime'    => $data['CreateTime'],
                );

        return $res;
    }
}
