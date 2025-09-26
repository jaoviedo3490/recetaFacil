<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\JWT\JwtService;
use App\Domain\User\Service\RegistryUserApp;
use App\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
class LoginUsuario extends UserAction
{
    public function __construct(LoggerInterface $logger, UserRepository $userRepository)
    {
        parent::__construct($logger, $userRepository);
    }

    protected function action(): Response
    {
        $user_data = $this->request->getParsedBody();
        $userName =  $user_data['username'];
        $password =  $user_data['password'];
        
        $user = $this->userRepository->findByUserName($userName);
        if($user['Code']=== 404){
            return $this->respondWithData($user);
        }
        $verificado = $user['Data']['_verificado'];
        if($verificado === '1'){
            if(password_verify($password,$user['Data']['_contrasena'])){
                $user_login_auditory = $this->userRepository->loginUserAuditory($user['Data']['id']);
                $jwt = new JwtService($this->logger ,$this->userRepository);
                $jwt_response = $jwt->generateJWTInternal($userName);
                return $this->respondWithData(['Message'=>"Usuario autenticado",'Code'=>200,"JWT"=>$jwt_response]);
            }else{
                return $this->respondWithData(['Message'=>"Usuario no autenticado",'Code'=>403]);
            }
        }else if ($verificado === '0'){
            $VerifyUser = new RegistryUserApp($user['Data']['_email']);
            $verify_user_result = $VerifyUser->registerUser();
            $verify_user_result['email'] = $user['Data']['_email'];
            $verify_user_result['id'] = $user['Data']['id'];
            switch($verify_user_result['Code']){
                case 200:
                    return $this->respondWithData($verify_user_result);
                break;
                case 500:
                    return $this->respondWithData($verify_user_result);
                    $this->logger->info(`$verify_user_result`);
                break;
            }
                return $this->respondWithData(['Message'=>$user,'Code'=>403]);
        }
        
        $this->logger->info("User of username `${userName}` was login.");

        return $this->respondWithData(['Message'=>"BAD REQUEST",'Code'=>400]);
    }
    
}
