<?php

namespace App\Domain\JWT;

use App\Application\Actions\Action;
use App\Domain\User\UserRepository;
use App\Infrastructure\Jwt\JsonWebToken;
use App\Infrastructure\Redis\Redis_cli;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class JwtService extends Action
{
    public $message = array();
    public $key;

    public function __construct(LoggerInterface $logger, UserRepository $userRepository)
    {
        parent::__construct($logger, $userRepository);
    }


    public function generateJWTInternal(string $usermail): array
    {
        try {
            $redis = new Redis_cli();
            $session_token = substr(bin2hex(random_bytes(4)), 0, 8);

            $redis_var = $redis->createTempVar(hash('sha256', $usermail) . '_redis_tokenJWT', $session_token, "28800");

            if ($redis_var['Code'] == 200) {
                $jwt = new JsonWebToken($usermail, $session_token);

                return [
                    'Code' => 200,
                    'Message' => 'Jwt autenticado correctamente',
                    'token' => $jwt->message['Token'],
                    'mail' =>  $jwt->message['Payload']['usermail']
                ];
            } else {
               return [
                    'Code' => 500,
                    'Message' => 'Error al crear la Cookie',
                ];
            }
        } catch (\Exception $e) {
            return ['Code' => 500, 'Message' => $e->getMessage()];
        }
    }

    


    protected function action(): Response
    {
        try {
            $user_data = $this->request->getParsedBody();
            $usermail = $user_data['mail'] ?? null;
            if(isset($user_data['protocol']) && $user_data['protocol']==='getVar'){
                $redis = new Redis_cli();
                $redis_session = $redis->getRedisVar(hash('sha256', $usermail) . '_redis_tokenJWT');
                if($redis_session['Code'] === 200){
                    
                    $result = $this->generateJWTInternal($usermail);
                    return $this->respondWithData($result);
                }else{
                    
                    return $this->respondWithData($redis_session);
                }
            }if (!$usermail) {
                return $this->respondWithData(['Code' => 400, 'Message' => 'Falta mail']);
            }

            return $this->respondWithData(['Code' => 400, 'Message' => 'Error al validar el protocolo']);
        } catch (\Exception $e) {
            return $this->respondWithData(['Code' => 500, 'Message' => $e->getMessage()]);
        }
    }
}
