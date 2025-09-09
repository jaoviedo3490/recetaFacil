<?php

namespace App\Application\Actions\User;

use App\Domain\User\Service\RegistryUserApp;
use App\Domain\User\Service\VerifyRedisToken;
use App\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;


class RecoveryPassword extends UserAction
{
    public function __construct(LoggerInterface $logger, UserRepository $userRepository)
    {
        parent::__construct($logger, $userRepository);
    }

    protected function action () : Response{
        $user_data = $this->request->getParsedBody();
        $user_mail =  $user_data['_email'];
        if(isset($user_data['protocol'])){
            $redis_verify = new VerifyRedisToken();
            $result_redis_verify = $redis_verify->verifyToken($user_mail,$user_data['redis_token']);
            if($result_redis_verify['Code'] === 200){
                $user = $this->userRepository->UpdateUserPassword($user_data['new_password'],$user_data['id']);
                return $this->respondWithData($user);
            }
           return $this->respondWithData($result_redis_verify);
        }else{
            
            $user = $this->userRepository->findByUserName($user_mail);
            if($user['Code'] === 200){
                $VerifyUser = new RegistryUserApp($user_mail);
                $verify_user_result = $VerifyUser->registerUser();
                $id = $user['Data']['id'];
                $verify_user_result['email'] = $user_mail;
                $verify_user_result['id'] = $id;
                switch($verify_user_result['Code']){
                    case 200:
                        return $this->respondWithData($verify_user_result);
                    break;
                    case 500:
                        return $this->respondWithData($verify_user_result);
                    break;
                }
            }else{
                return $this->respondWithData(['Message'=>'User not found','Code'=>404]);
            }
        }
    }
}

?>