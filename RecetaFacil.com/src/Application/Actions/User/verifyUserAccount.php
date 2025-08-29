<?php
declare(strict_types=1);

namespace App\Application\Actions\User;
use App\Domain\User\Service\RegistryUserApp;
use App\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class VerifyUserAccount extends UserAction
{
    public function __construct(LoggerInterface $logger, UserRepository $userRepository)
    {
        parent::__construct($logger, $userRepository);
    }

    protected function action(): Response
    {
        
        $user_data = $this->request->getParsedBody();
        $email = $user_data['email'];
        $id = $user_data['id'];
        $user_activation = new RegistryUserApp($email);
        //$user = $this->userRepository->ActivateAccountUser($id);
        $this->logger->info("A new user Activated.");

        return $this->respondWithData($user);
    }
}


?>