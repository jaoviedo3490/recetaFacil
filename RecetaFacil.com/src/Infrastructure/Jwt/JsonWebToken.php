<?php
namespace App\Infrastructure\Jwt;

use Firebase\JWT\JWT;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class JsonWebToken {
    private Logger $logger;
    private $secret_key;
    public $payload;
    public $message = array();
    private $jwt;

    public function __construct($usermail, $redis_token) {
        $this->secret_key = $redis_token;

        $this->logger = new Logger('jwt');
        $this->logger->pushHandler(new StreamHandler(__DIR__.'/jwt.log', Logger::DEBUG));

        $this->GeneratePayload($usermail);
    }

    public function GeneratePayload($usermail) {
        try {
            $this->payload = [
                'iat' => time(),
                'nbf' => time(),
                'exp' => time() + 3600,
                'usermail' => $usermail
            ];

            $this->jwt = JWT::encode($this->payload, $this->secret_key, 'HS256');

            $this->message['Token'] = $this->jwt;
            $this->message['Payload'] = $this->payload;

            $this->logger->info('Token generado', ['token' => $this->jwt, 'payload' => $this->payload]);

            return $this->message;

        } catch (\Exception $e) {
            $this->logger->error('Error al generar token', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->message['Message'] = $e;
            $this->message['Code'] = "500";
            return $this->message;
        }
    }

    public function getToken(){
        if(isset($this->payload) && !empty($this->payload)){
            $this->message['Message'] = "Token Activo";
            $this->message['Code'] = 200;
            return $this->message;
        }else{
            $this->message['Message'] = "Token Expirado";
            $this->message['Code'] = 404;
            return $this->message;
        }
    }
}
