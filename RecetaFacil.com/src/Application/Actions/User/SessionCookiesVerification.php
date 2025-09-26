<?php

namespace App\Application\Actions\User;

use App\Infrastructure\Jwt\JsonWebToken;
use App\Infrastructure\Redis\Redis_cli;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;


class SessionCookiesVerification extends UserAction
{
    public function __construct(LoggerInterface $logger, UserRepository $userRepository)
    {
        parent::__construct($logger, $userRepository);
    }

    protected function action () : Response{
        $user_data = $this->request->getParsedBody();
        $user_mail =  $user_data['email'];
        $redis_var = new Redis_cli();
        $redis_var = $redis->getRedisVar(hash('sha256', $user_mail).'_redis_tokenJWT');
        if($redis_var['Code'] == 200){
            
                $user_session = $redis->createTempVar(hash('sha256', $usermail).'_redis_tokenJWT',$session_token,"28800");
                $jwt = new JsonWebToken($usermail,$session_token);
                setCookie(
                    "jwt_key",$jwt->message['Payload']['usermail'],[
                        'expires' => time() + 8 * 3600, 
                        'path' => '/',                 
                        'domain' => '127.0.0.1',    
                        //'secure' => true,               
                        'httponly' => true,           
                       // 'samesite' => 'None'  
                        'samesite' => 'Lax'   
                    ]
                    );
                    setCookie(
                    "jwt_token",$jwt->message['Token'],[
                        'expires' => time() + 8 * 3600, 
                        'path' => '/',                 
                        'domain' => '127.0.0.1',    
                        'samesite' => 'Lax'   
                    ]
                    );
                $this->message['Code'] = 200;
                $this->message['Message'] = 'Jwt autenticado correctamente';
                $this->message['token'] = $jwt->message['Token'];

                return $this->respondWithData($this->message);
                //return $this->respondWithData();
                
            }else if($redis_var['Code'] == 404){
                $this->message = $redis_var;
                return $this->respondWithData($this->message);
            }
        
    }
}

?>