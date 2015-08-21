<?php 

namespace Common\PHPMailer;

class Mailer {
    function sendmails($config, $address, $body, $altbody = '请使用兼容HTML格式邮箱'){
        // require PHPmailer
        //include_once BASEDIR . '/Common/PHPMailer/PHPMailerAutoload.php';
        //require_once "/var/www/html/ant/Common/PHPMailer/PHPMailerAutoload.php";
        //require_once BASEDIR . "/Common/PHPMailer/PHPMailerAutoload.php";

        $mail = new PHPMailer();
        $mail->IsSMTP(); //设置PHPMailer应用SMTP发送Email
        //$mail->CharSet = 'UTF-8';
        $mail->CharSet = $config['mail']['charset'];
        //$mail->Host = 'smtp.126.com';  // 指定邮件服务器
        $mail->Host = $config['mail']['host'];  // 指定邮件服务器
        //$mail->Port = 25;    //指定邮件服务器端口
        $mail->Port = $config['mail']['port'];    //指定邮件服务器端口
        $mail->SMTPAuth = true;     // 开启 SMTP验证
        //设置SMTP用户名和密码
        //$mail->Username = 'lukcatchen@126.com';
        $mail->Username = $config['mail']['username'];
        echo $config['mail']['username'];
        //$mail->Password = 'j88j,ui7i97';
        $mail->Password = $config['mail']['password'];
        echo $config['mail']['password'];
        //$mail->From = 'lukcatchen@126.com'; //指定发送邮件地址
        $mail->From = $config['mail']['fromaddress']; //指定发送邮件地址
        //$mail->FromName = '想学网测试服务器'; //为发送邮件地址命名
        $mail->FromName = $config['mail']['fromname']; //为发送邮件地址命名
        //这里为批量发送邮件
        if (is_array($address)) {
            foreach ($address as $val) {
                $mail->AddAddress($val);
            }
        } else {
            $mail->AddAddress($address);
        }
        $mail->AddReplyTo('lukcatchen@126.com', '大众影评网');
        $mail->WordWrap = 50;   // 设置自动换行的字符长度为 50            
        $mail->IsHTML(true); // 设置Email格式为HTML
        $mail->Subject = $config['mail']['subject'];
        $mail->Body = $body;
        $mail->AltBody = $altbody; //当收件人客户端不支持接收HTML格式email时的可替代内容;        
        //发送邮件。
        if (!$mail->Send()) {
            $mail->ErrorInfo;
            return false;//throw_exception("Mailer Error: " . $mail->ErrorInfo);提示邮箱发送不成功的错误信息
        } else {
            return true;
        }
    }
}
