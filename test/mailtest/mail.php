<?php

//require_once "vendor/autoload.php";
//require_once "./PHPMailer-master/PHPMailerAutoload.php";
require_once "./PHPMailer-master/class.phpmailer.php";

$mail = new PHPMailer;

//Enable SMTP debugging. 
$mail->SMTPDebug = 3;                               
//Set PHPMailer to use SMTP.
$mail->isSMTP();            
//Set SMTP host name                          
//$mail->Host = "smtp.gmail.com";
$mail->Host = "smtp.126.com";
//Set this to true if SMTP host requires authentication to send email
$mail->SMTPAuth = true;                          
//Provide username and password     
//$mail->Username = "lukcatchen@gmail.com";                 
$mail->Username = "lukcatchen@126.com";                 
$mail->Password = "j88j,ui7i97";                           
//If SMTP requires TLS encryption then set it
$mail->SMTPSecure = "tls";                           
//Set TCP port to connect to 
//$mail->Port = 587;                                   
$mail->Port = 465;                                   

$mail->From = "chan2210@126.com";
$mail->FromName = "chendq";

$mail->addAddress("chendeqing@ceiec.com.cn", "chendeqing");

$mail->isHTML(true);

$mail->Subject = "Subject Text";
$mail->Body = "<i>Mail body in HTML</i>";
$mail->AltBody = "This is the plain text version of the email content";

if(!$mail->send()) 
{
        echo "Mailer Error: " . $mail->ErrorInfo;
} 
else 
{
        echo "Message has been sent successfully";
}
