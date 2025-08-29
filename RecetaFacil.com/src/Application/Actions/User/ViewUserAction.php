<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class ViewUserAction extends UserAction
{
    public function __construct(LoggerInterface $logger, UserRepository $userRepository)
    {
        parent::__construct($logger, $userRepository);
    }

    protected function action(): Response
    {
        $userName = (string) $this->resolveArg('username');
        $user = $this->userRepository->findByUserName($userName);
        $this->logger->info("User of username `${userName}` was viewed.");
        return $this->respondWithData($user);
    }
}
