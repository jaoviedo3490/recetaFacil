<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\LoginUsuario;
use App\Application\Actions\User\ViewUserAction;
use App\Infrastructure\DataBase\ORM;
use App\Infrastructure\Email\Mail;
use App\Infrastructure\Redis\Redis_cli; 
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RedBeanPHP\R;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });
    

    $app->group('/testers',function (Group $group) use ($app){
        $group->get('/phpinfo',function(Request $request, Response $response){
            ob_start();
            phpinfo();
            $phpinfo = ob_get_clean();
            $response->getBody()->write($phpinfo);
            return $response;
        });
        $group->get('/Redis/ping',function(Request $request , Response $response){
            try{
               $redis = new Redis_cli();
               $redis->ping();
               $response->getBody()->write(json_encode($redis,JSON_PRETTY_PRINT));
               return $response->withStatus(200);

            }catch(\Exception $e){
                $response->getBody()->write($e->getMessage());
                return $response->withStatus(500);
            }
        });
        $group->post('/Redis/createVar',function(Request $request , Response $response){
            try{
                $user_data = $request->getParsedBody();
                $redis = new Redis_cli();
                $result = $redis->createVar($user_data['key'],$user_data['value']);
                $response->getBody()->write(json_encode($result, JSON_PRETTY_PRINT));
                return $response->withStatus(200);
            }catch(\Exception $e){
                $response->getBody()->write($e->getMessage());
                return $response->withStatus(500);
            }
        });
        $group->post('/Redis/deleteVar',function(Request $request , Response $response){
            try{
                $user_data = $request->getParsedBody();
                $redis = new Redis_cli();
                $result = $redis->deleteVar($user_data['key']);
                $response->getBody()->write(json_encode(['Message'=>"Variable elimianda"],JSON_PRETTY_PRINT));
                return $response->withStatus(200);
            }catch(\Exception $e){
                $response->getBody()->write($e->getMessage());
                return $response->withStatus(500);
            }
        });
        $group->post('/Redis/createTempVar',function( Request $request, Response $response){
            try{
                $user_data = $request->getParsedBody();
                $redis = new Redis_cli();
                $time_expiration = $user_data['time_expiration'];
                $result = $redis->createTempVar($user_data['key'],$user_data['value'],$time_expiration);
                $response->getBody()->write(json_encode($result, JSON_PRETTY_PRINT));
                return $response->withStatus(200);
            }catch(\Exception $e){
                $response->getBody()->write($e->getMessage());
                return $response->withStatus(500);
            }
        });

        $group->get('/debug/routes', function (Request $request, Response $response) use($app)  {
            $routes = $app->getRouteCollector()->getRoutes();
            $list = [];
            foreach ($routes as $route) {
                $list[] = [
                    "methods" => $route->getMethods(),
                    "pattern" => $route->getPattern()
                ];
            }
            $response->getBody()->write(json_encode($list, JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type', 'application/json');
        });

        $group->get('/getConnectionRedBean', function (Request $request, Response $response) {
            try {
                $Message = ORM::setup();
                if ($Message['Code'] == "200") {
                    if (R::testConnection()) {
                        $payload = json_encode(["Message" => "Conexion exitosa"], JSON_PRETTY_PRINT);
                        $response->getBody()->write($payload);
                        return $response->withStatus(200);
                    } else {
                        $payload = json_encode(['Message' => "Error de conexion"], JSON_PRETTY_PRINT);
                        $response->getBody()->write($payload);
                        return $response->withStatus(500);
                    }
                } else {
                    $response->getBody()->write($Message['Message']);
                    return $response->withStatus(500);
                }
            } catch (\Exception $e) {
                $response->getBody()->write($e->getMessage());
                return $response->withStatus(500);
            }
        });

        $group->get('/loginUser/mail',function(Request $request , Response $response){
            try{
                $send_mail = new Mail("jassonlukno44@gmail.com",'<h1>Hola mundo</h1>','Codigo de verificacion');
                $result = $send_mail->sendEmail();
                $response->getBody()->write(json_encode($result, JSON_PRETTY_PRINT));
                return $response->withStatus(200);
            }catch(\Exception $e){
                $response->getBody()->write($e->getMessage());
                return $response->withStatus(500);
            }
        });

        $group->post('/loginUser/verifyUser',function(Request $request , Response $response){
            try{
                $user_data = $request->getParsedBody();
                $redis = new Redis_cli();
                $time_expiration = $user_data['time_expiration'];
                $mail_to = $user_data['mail_to'];
                $code_verification = substr(bin2hex(random_bytes(4)), 0, 8); 
                $result = $redis->createTempVar($user_data['key'],$code_verification,$time_expiration);
                
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
                    $send_mail = new Mail($mail_to,$body_mail,'Codigo de verificacion');
                    $result = $send_mail->sendEmail();
                    $response->getBody()->write(json_encode($result, JSON_PRETTY_PRINT));
                    return $response->withStatus(200);
                }else{
                    $response->getBody()->write(json_encode($result, JSON_PRETTY_PRINT));
                    return $response->withStatus(200);
                }

            }catch(\Exception $e){
                $response->getBody()->write($e->getMessage());
                return $response->withStatus(500);
            }
        });
        $group->post('/loginUser/ActivateAccount',function(Request $request, Response $response){
            try{
                $user_data = $request->getParsedBody();
                $redis = new Redis_cli();
                $redis_vars = $redis->getRedisVar($user_data['key']);
                if($redis_vars['Code'] === '200' && $redis_vars['Data'] === $user_data['key_test']){
                    $response->getBody()->write(json_encode(['Message'=>"Activación exitosa",'Code'=>'200'], JSON_PRETTY_PRINT));
                    return $response->withStatus(200);
                }else{
                    $response->getBody()->write(json_encode(['Message'=>"Código no coincide o ha expirado",'Code'=>'400'], JSON_PRETTY_PRINT));
                    return $response->withStatus(400);
                }
                $response->getBody()->write(json_encode($redis_vars, JSON_PRETTY_PRINT));
                return $response->withStatus(200);
            }catch(\Exception $e){
                $response->getBody()->write($e->getMessage());
                return $response->withStatus(500);
            }
        });
    });

    $app->group('/Services', function (Group $group) {
        //$group->get('/loginUser', ListUsersAction::class);
        $group->get('/loginUser/{username}',\App\Application\Actions\User\ViewUserAction::class);
        $group->post('/loginUser/CreateUser', \App\Application\Actions\User\CreateUserAction::class);
        $group->post('/loginUser/SendTemporalPassword', \App\Application\Actions\User\VerifyUserAccount::class);
        $group->post('/loginUser/ActivateAccount', \App\Application\Actions\User\ActivateUserAccount::class);
        $group->post('/loginUser/loginUsuario',\App\Application\Actions\User\LoginUsuario::class);
        $group->post('/loginUser/RecoveryPassword',\App\Application\Actions\User\RecoveryPassword::class);

    });
};
