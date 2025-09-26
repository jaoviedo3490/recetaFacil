<?php
declare(strict_types=1);

namespace App\Application\Actions\Redis;
use App\Domain\User\Service\RegistryUserApp;
use App\Infrastructure\Redis\Redis_cli;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
class SessionAction extends RedisAction
{
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct($logger);
    }

    protected function action(): Response{
        $user_data = $this->request->getParsedBody();
        $email = $user_data['mail'];
        $redis = new Redis_cli();
        $response_logout = $redis->deleteVar(hash('sha256', $email) . '_redis_tokenJWT');
        
        return $this->respondWithData($response_logout);
    }
}


?>