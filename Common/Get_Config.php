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

            // remove enter and space in string 
            $this->hostname = preg_replace("/\s/","",(string)$xml->hostname);
            $this->instance = preg_replace("/\s/","",(string)$xml->instance);
            $this->username = preg_replace("/\s/","",(string)$xml->username);
            $this->password = preg_replace("/\s/","",(string)$xml->password);
            
            return true;
        } else {

            return false;
        }
    }
}


