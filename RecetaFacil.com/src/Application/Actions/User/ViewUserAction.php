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
        
        if(isset($user['Data']['_email']) && isset($user['Data']['id'])){
            $data['email'] = $user['Data']['_email'];
            $data['id'] = $user['Data']['id'];
            $data['Code'] = 200;
            $data['Message'] = $user['Message'];
            return $this->respondWithData($data);
        }
        
        $this->logger->info("User of username `${userName}` was viewed.");
        return $this->respondWithData($user);
    }
}
