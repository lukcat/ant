<?php

namespace App\SMS\SMSMplus;

require_once("XML/Serializer.php");

class SMSMplus {

    private $serializer_options = array(
        //'method' => 'xml',
        'standalone' => 'yes',
        'addDecl' => TRUE,
        'rootName' => 'sms-Response',
        //'mode' => 'simplexml',
        'indent' => '    ',
        'encoding' => 'ISO-8859-1',
        );
    private $serializer_property = array(
        'STANDALONE' => 'yes'
        );

    private $responseArray = array(
        'status' => 0,
        'mensaje' => 'SMS Recibido');


    /* Receive message from Mplus
     * return JSON data
     */
    public function recieveSM() {

        // receive SM from Mplus by HTTP POST method
        $message;
        if (isset($HTTP_RAW_POST_DATA)) {
            $message = $HTTP_RAW_POST_DATA;
        }
        else {
            $message = file_get_contents("php://input");
        }

        $simpleXml = simplexml_load_string($message);
        $jsonData = json_encode($simpleXml);

        return $jsonData;
    }

    /* send message to Mplus
     * @param host
     * @param message
     * return JSON data (response message from Mplus)
     */
    public function sendSM($host, $message) {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $resContent = curl_exec($ch);

        // convert XML data to JSON data
        $resContentJSON = json_encode($resContent);

        return $resContentJSON;
    }

    /* Send response messge to Mplus
     * @param response JSON | Mplus response message
     * @param type string 
     * return void
     */
    public function responseToMplus() {
        $serializer = new \XML_Serializer($this->serializer_options);
        //$serializer = new \XmlSerializer();
        //$serializer->setOption('status'=>'test');

        //var_dump($this->responseArray);
        $status = $serializer->serialize($this->responseArray);
        //$status = $serializer->serialize($this->responseArray, $this->serializer_options);

        //echo htmlspecialchars($serializer->getSerializedData());
        echo $serializer->getSerializedData();

        // return data
        return $serializer->getSerializedData();
        //echo $status;
    }

    /* Send response message to Mplus
     * return JSON object
     */
    public function get() {
    }

}

$sm = new SMSMplus();
$sm->responseToMplus();


