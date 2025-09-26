<?php

declare(strict_types=1);

namespace App\Application\Actions\Redis;

use App\Application\Actions\Action;
use Psr\Log\LoggerInterface;

abstract class RedisAction extends Action
{


    public function __construct(LoggerInterface $logger) //por ahora no se usa el userRepository
    {
        parent::__construct($logger);
    }
}
