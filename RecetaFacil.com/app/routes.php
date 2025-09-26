<?php

declare(strict_types=1);

use App\Application\Actions\Recetas\ViewCollectionsRecetas;
use App\Application\Actions\Recetas\ViewUserAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\LoginUsuario;
use App\Domain\JWT\JwtService;
use App\Domain\User\UserRepository;
use App\Infrastructure\DataBase\ORM;
use App\Infrastructure\Email\Mail;
use App\Infrastructure\Redis\Redis_cli; 
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use RedBeanPHP\R;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });
    
    $app->group('/Services', function (Group $group) {
        //$group->get('/loginUser', ListUsersAction::class);
        $group->get('/loginUser/{username}',\App\Application\Actions\User\ViewUserAction::class);
        $group->post('/loginUser/CreateUser', \App\Application\Actions\User\CreateUserAction::class);
        $group->post('/loginUser/SendTemporalPassword', \App\Application\Actions\User\VerifyUserAccount::class);
        $group->post('/loginUser/ActivateAccount', \App\Application\Actions\User\ActivateUserAccount::class);
        $group->post('/loginUser/loginUsuario',\App\Application\Actions\User\LoginUsuario::class);
        $group->post('/loginUser/RecoveryPassword',\App\Application\Actions\User\RecoveryPassword::class);
        $group->post('/login/Jwt/Auth',\App\Domain\JWT\JwtService::class);

    });

    $app->group('/Recetas',function(Group $group){
        $group->get('/findAll',\App\Application\Actions\Recetas\viewRecetas::class);
        $group->post('/createReceta',\App\Application\Actions\Recetas\CreateRecetaAction::class);
        $group->post('/createCollection',\App\Application\Actions\Recetas\CreateRecetaCollection::class);
        $group->post('/viewCollection',\App\Application\Actions\Recetas\ViewCollectionsRecetas::class);
       
    });

    $app->group('/Session',function(Group $group){
      $group->post('/Jwt/Auth',\App\Domain\JWT\JwtService::class);
      $group->post('/Jwt/Logout',\App\Application\Actions\Redis\SessionAction::class);
    })->add(\App\Application\Middleware\SessionMiddleware::class);
    
    $app->group('/Reports',function(Group $group){
        $group->post('/home',\App\Application\Actions\Reports\ReportsHome::class);
    })->add(\App\Application\Middleware\SessionMiddleware::class);
  

};
