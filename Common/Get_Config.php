<?php 

namespace Common;

class Get_Config {
    private $file;

    public $hostname;
    public $instance;
    public $username;
    public $password;

    //$file = './config/config';
    function __construct($file) {
        $this->file = $file;
    }

    public function readConfig() {
        if (file_exists($this->file)) {
            $xml = simplexml_load_file($this->file);
            $this->hostname = (string)$xml->hostname;
            $this->instance = (string)$xml->instance;
            $this->username = (string)$xml->username;
            $this->password = (string)$xml->password;
            
            return true;
        } else {

            return false;
        }
    }
}


