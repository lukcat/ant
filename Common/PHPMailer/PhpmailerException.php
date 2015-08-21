<?php 

namespace Common\PHPMailer;

class phpmailerException extends \Exception
//class phpmailerException 
{
    /**
     * Prettify error message output
     * @return string
     */
    public function errorMessage()
    {
        $errorMsg = '<strong>' . $this->getMessage() . "</strong><br />\n";
        return $errorMsg;
    }
}
