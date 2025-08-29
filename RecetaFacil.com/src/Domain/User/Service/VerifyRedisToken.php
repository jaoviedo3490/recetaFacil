<?php


namespace App\Domain\User\Service;
use App\Infrastructure\Redis\Redis_cli;

class VerifyRedisToken{
    private $redis;
    public  $message = array("Message"=>"","Code"=>"");
    
    public function __construct(){
        $this->redis = new Redis_cli();
    }

    public function verifyToken(string $email,string $token_frontend): array{
        try{
           $token = $this->redis->getRedisVar($email."_redis");
           if($token['Data'] === $token_frontend){
               $this->message['Message'] = "Token is valid";
               $this->message['Code'] = 200;
               return $this->message;
           }else{
               $this->message['Message'] = "Token is invalid";
               $this->message['Code'] = 401;
               return $this->message;
           }
        }catch(\Exception $e){
            $this->message['Message'] = $e->getMessage();
               $this->message['Code'] = 500;
               return $this->message;
        }
    }
}

?>