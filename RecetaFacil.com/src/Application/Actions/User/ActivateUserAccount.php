<?php
namespace App\Application\Actions\User;
use App\Domain\User\Service\RegistryUserApp;
use App\Domain\User\Service\VerifyRedisToken;
use App\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class ActivateUserAccount extends UserAction
{
    public function __construct(LoggerInterface $logger, UserRepository $userRepository)
    {
        parent::__construct($logger, $userRepository);
    }

    protected function action(): Response
    {
        $user_data = $this->request->getParsedBody();
        $email = $user_data['email'];
        $token = $user_data['token'];
        $id = $user_data['id'];
    
        $ServiceRegistry = new VerifyRedisToken();
        $result = $ServiceRegistry->verifyToken($email,$token);
        if($result['Code']===200){
            $user = $this->userRepository->ActivateAccountUser($id);
            return $this->respondWithData($user);
        }else{
            return $this->respondWithData($result);
        }
        $result = $ServiceRegistry->registerUser();
        $this->logger->info("A new user was Activate.");

        return $this->respondWithData($result);
    }
}

?>