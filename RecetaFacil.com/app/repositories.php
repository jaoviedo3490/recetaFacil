<?php

declare(strict_types=1);

use App\Domain\User\UserRepository;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        // Aquí mapeamos directamente la clase real
        UserRepository::class => function (ContainerInterface $c) {
            return new UserRepository();
        },
        recetasRepository::class => function (ContainerInterface $c) {
            return new recetasRepository();
        }
    ]);
};

