<?php
declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use Slim\App;
use Slim\Psr7\Response;

return function (App $app) {
    
    $app->add(SessionMiddleware::class);
};
