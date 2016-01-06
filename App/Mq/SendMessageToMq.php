<?php

namespace App\Mq;

class SendMessageToMq {
    private $host;
    private $port;
    private $username;
    private $password;
    private $exchange;
    private $mobile;

    public function __construct($connectPara) {
        $this->host     = $connectPara['host'];
        $this->port     = $connectPara['port'];
        $this->username = $connectPara['username'];
        $this->password = $connectPara['password'];
        $this->exchange = $connectPara['exchange'];
        $this->mobile   = $connectPara['mobile'];
    }

    /*
     * Send messsage to rabbitMq
     * @param data array, which contains {ComplaintId=>'id', UserId=>'id', ComplaintType=>'type', CreateTime=>'yyyy-MM-dd hh:mm:ss'}
     */
    public function send($data) {
        // New a connection to rabbitMq
        $con = new AMQPStreamConnection($this->host, $this->port, $this->username, $this->password);

        // Create a channel
        $channel = $con->channel();

        // declear a queue
        $channel->queue_declear($this->mobile, false, true, false, false);

        // generate bind key, MB.V2.RP.complaintid
        $bindKey = 'MB.V2.RP.' . $data['complaintId'];

        // Bind queue to exchange with bindKey
        $channel->bind($this->mobile, $this->exchange, $this->bindKey);

        // Convert json object to json string
        $jsonStr = json_encode($data);
        $msg = new AMQPMessage($jsonStr);

        // Send message to exchange
        $channel->basic_publish($msg, $this->exchange, $this->bindKey);

        // Close connection
        $channel->close();
        $con->close();
    }
}
