<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Infrastructure\Redis\Redis_cli;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface; 
use Slim\Psr7\Response as SlimResponse;

class SessionMiddleware implements Middleware
{
    protected Request $request;
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $this->request = $request;
       try{
         $user_data = $this->request->getParsedBody();
        if (!isset($user_data['mail']) || !isset($user_data['token'])) {
            return $this->unauthorizedResponse($user_data['mail']);
        }

        $token = $user_data['token'];
        $correo = $user_data['mail'];

        $redis = new Redis_cli();
        $redis_var = $redis->getRedisVar(hash('sha256', $correo) . '_redis_tokenJWT');

        if ($redis_var['Code'] !== "200") {
            return $this->unauthorizedResponse('Sesión no válida o expirada');
        }
        try {
            $decoded = JWT::decode($token, new Key($redis_var['Data'], 'HS256'));
            $request = $request->withAttribute('user', $decoded);
            return $handler->handle($request);
        } catch (\Exception $e) {
            return $this->unauthorizedResponse('Token inválido: ' . $e->getMessage());
        }
       }catch(\Exception $e){
        return $this->unauthorizedResponse('Token inválido: ' . $e->getMessage());
       }
    }

    private function unauthorizedResponse(string $message): Response
    {
        $response = new SlimResponse();
        $response->getBody()->write(json_encode([
            'Code' => 401,
            'Message' => $message
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(401);
    }
}
