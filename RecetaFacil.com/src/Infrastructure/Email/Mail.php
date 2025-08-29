<?php



namespace App\Infrastructure\Email;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
//require 'vendor/autoload.php'; 

class Mail {
    public $message = array("Message"=>"","Code"=>"");
    private $mail;
    private $mail_to;
   
    private $message_mail;
  
    public function __construct($mail_to,$message_mail,$subject){
        try{
            $this->mail = new PHPMailer(true);
            $this->mail->isSMTP();
            $this->mail->Host = 'smtp.gmail.com';
            $this->mail->SMTPAuth  = true;
            $this->mail->Username = 'recetafacil15@gmail.com';
            $this->mail->Password = 'jmiodcsddkjesfxk';
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = 587;
            $this->mail_to = $mail_to;
            $this->message_mail = $message_mail;
            $this->mail->setFrom('recetafacil15@gmail.com', 'Receta Facil');
            $this->mail->addAddress($this->mail_to);

            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $this->message_mail;
            
        }catch(\Exception $e){
            $this->message['Message'] = $e->getMessage();
            $this->message['Code'] = "500";
            return $this->message;
        }
    }
    public function sendEmail() {
        try{
           
            if($this->mail->send()){
                //$this->mail->send();
                $this->message['Message'] = "Correo enviado correctamente";
                $this->message['Code'] = "200";
                return $this->message;
            }else{
                $this->message['Message'] = "Error al enviar el correo: ".$this->mail->ErrorInfo;
                $this->message['Code'] = "500";
                return $this->message;
            }
           
        }catch(\Exception $e){
            $this->message['Message'] = $e->getMessage();
            $this->message['Code'] = "500";
            return $this->message;
        }
    }//24/08/2025Jas
    //recetafacil15@gmail.com
    //jmiodcsddkjesfxk
    
    public function getMailTo(){
        return $this->mail_to;
    }
    public function getMessageMail(){
        return $this->message_mail;
    }

    public function setMessageMail($message_mail){
        $this->message_mail = $message_mail;
    }

    public function setMailTo($mail_to){
        $this->mail_to = $mail_to;
    }
}
