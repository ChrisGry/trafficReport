<?php
class Mailer{       
        
        public $Form;
        public $FormName;
        public $address;
        public $Address;
        public $AddressName;
        public $Subject;
        public $Body;
        public $AltBody;
        public $isHtml =FALSE;
        
        public function  send(){
            require('phpemailer/class.phpmailer.php');
            require('phpemailer/class.smtp.php');
            date_default_timezone_set('Asia/shanghai');
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->CharSet = "UTF-8";
            $mail->Mailer = "smtp";
            $mail->Host = "smtp.qq.com";
            $mail->SMTPAuth = true;
            $mail->Username = "532587280@qq.com";
            $mail->Password="xiangxin20";
            $mail->From = $this->Form();
            $mail->FromName = $this->FormName;
            $mail->addAddress($this->Address,$this->AddressName);
            //$mail->addReplyTo($address);//抄送
            //$mail->isHTML(true); 发送html
            $mail->isHTML($this->isHtml);
            $mail->Subject = $this->Subject;
            $mail->Body = $this->Body;
            if(!$mail->send()){
                return '0';
            }else{
                return '1';
            }
        }
        
}
?>