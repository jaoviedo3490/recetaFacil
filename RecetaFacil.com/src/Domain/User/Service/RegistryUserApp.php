<?php

namespace App\Domain\User\Service;
use App\Application\Actions\Action;
use App\Application\Actions\User\CreateUserAction;
use App\Infrastructure\Email\Mail;
use App\Infrastructure\Redis\Redis_cli;
use Psr\Http\Message\ResponseInterface as Response;

class RegistryUserApp extends Action {
    public $message = array('Code'=>200,'Message'=>'');
    public $key;
    public function __construct($key){
        $this->key = $key;
    }
    
    protected function action() : Response{
        try{
            $result = $this->registerUser();
            $this->message = $result;
            return $this->respondWithData($this->message);
        }catch(\Exception $e){
            $this->message['Code'] = 500;
            $this->message['Message'] = $e->getMessage();
            return $this->respondWithData($this->message);
        }
    }

    public function registerUser(){
        try{
            $redis = new Redis_cli();
            $redis_response = $redis->ping();
           if($redis->ping()){
                $time_expiration = (int) 330;
                $code_verification = substr(bin2hex(random_bytes(4)), 0, 8);
                $result = $redis->createTempVar($this->key."_redis",$code_verification,$time_expiration);

                if($result['Code']=="200"){
                    $body_mail = "<html>
                                    <head>
                                    <meta charset='UTF-8'>
                                    <style>
                                        .btn {
                                        display: inline-block;
                                        padding: 10px 20px;
                                        background-color: #007bff;
                                        color: white;
                                        text-decoration: none;
                                        border-radius: 5px;
                                        }
                                        .card {
                                        border: 1px solid #ddd;
                                        padding: 20px;
                                        border-radius: 10px;
                                        font-family: Arial, sans-serif;
                                        }
                                    </style>
                                    </head>
                                    <body>
                                    <div class='card'>
                                        <h2>Verificación de cuenta</h2>
                                        <p><strong>Su código de verificación es:</strong></p>
                                        <h3>$code_verification</h3>
                                        <p><strong>Duración del código:</strong> ".(((int)$time_expiration/60)-1)." minutos</p>
                                    </div>
                                    </body>
                                    </html>";
                    $send_mail = new Mail($this->key,$body_mail,'Codigo de verificacion');
                    $result = $send_mail->sendEmail();
                    return $result;

                }else{
                    $this->message['Code'] = $result['message']['Code'];
                    $this->message['Message'] = $result['message']['Message'];
                    return $this->message;
                }

           }else{
               $this->message['Code'] = $result['message']['Code'];
               $this->message['Message'] = $result['message']['Message'];
               return $this->message;
           }    
        }catch(\Exception $e){
            $this->message['Code'] = 500;
            $this->message['Message'] = $e->getMessage();
            return $this->message;
        }
    }
}


?>