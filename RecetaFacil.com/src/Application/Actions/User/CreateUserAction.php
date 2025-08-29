<?php
declare(strict_types=1);

namespace App\Application\Actions\User;
use App\Domain\User\Service\RegistryUserApp;
use App\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class CreateUserAction extends UserAction
{
    public function __construct(LoggerInterface $logger, UserRepository $userRepository)
    {
        parent::__construct($logger, $userRepository);
    }

    protected function action(): Response
    {
        $user_data = $this->request->getParsedBody();
        $email = $user_data['email'];
        $user_exist = $this->userRepository->findByUserName($email);
        if($user_exist['Code']===404){
            $user = $this->userRepository->createUser($user_data);
            $user_audit = $this->userRepository->insertUserAuditory($user['id']);
            if($user_audit['Code']!=201){
                    return $this->respondWithData(['Message'=>$user_audit['Message'],'Code'=>500]);
            }
            $ServiceRegistry = new RegistryUserApp($email);
            $result = $ServiceRegistry->registerUser();
            $result['Data'] = $user;
            $result['Audit'] = $user_audit;
            $this->logger->info("A new user was created.");
            return $this->respondWithData($result);  
        }else if($user_exist['Code']===200){
            return $this->respondWithData(['Message'=>'User already exists','Code'=>409]);
        }
    }
}


?>