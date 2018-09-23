<?php
require_once("Smtp.class.php");
 class Sendmail{
     
     public function send($mailserver,$mailport,$mailform,$mailpwd,$mailtitle,$mailcontent,$mailto){
         
         $MailServer = $mailserver;     
         $MailPort   = $mailport;				
         $MailId     = $mailform;
         $MailPw     = $mailpwd;
         $Title      = $mailtitle;  
         $Content    = $mailcontent; 
         $email      = $mailto;
         $smtp = new smtp($MailServer,$MailPort,true,$MailId,$MailPw);
         $smtp->debug = false;
         if($smtp->sendmail($email,$MailId, $Title, $Content, "HTML")){
             return '1';          
         } else {
             return '0';         
         }
     }
 }

?>