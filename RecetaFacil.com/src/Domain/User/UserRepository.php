<?php

declare(strict_types=1);

namespace App\Domain\User;
use RedBeanPHP\R;

class UserRepository extends \App\Infrastructure\DataBase\ORM
{
    public $message = array("Message"=>"","Code"=>"");
    /**
     * @return User[]
     */
    public function findByUserName($username): array{
        try{
            self::setup();
            $results = R::findOne('rfusuarios','_email=?',[$username]);
            if(!$results){
                $this->message['Code'] = 404;
                $this->message['Message'] = 'User not found';
                return $this->message;
            }
            $this->message['Code'] = 200;
            $this->message['Message'] = 'User found';
            $this->message['Data'] = $results;
            return $this->message;
        }catch (\Exception $e){
            $this->message['Code'] = 500;
            $this->message['Message'] = $e->getMessage();
            return $this->message;
        }
    }
    public function createUser($data) : array{
        try{
                self::setup();
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
                $data['password'] = $hashedPassword;
                $user = R::dispense('rfusuarios');
                $user->NombreUsuario = $data['username'];
                $user->Contrasena = $data['password'];
                $user->Email = $data['email'];
                R::store($user);
                $this->message['Code'] = 201;
                $this->message['Message'] = 'User created successfully';
                $this->message['Data'] = $data['email'];
                $this->message['id'] = $user->id;
                return $this->message;
           
        }catch(\Exception $e){
            $this->message['Code'] = 500;
            $this->message['Message'] = $e->getMessage();
            return $this->message;
        }
    }
    public function ActivateAccountUser($id) : array{
        try{
            self::setup();
            $user = R::load('rfusuarios',$id);
            $user->_verificado = 1;
            R::store($user);
            $this->message['Code'] = 201;
            $this->message['Message'] = 'User activated successfully';
            $this->message['Id'] = $user->id;
            $this->message['email'] = $user->Email;
            return $this->message;
        }catch(\Exception $e){
            $this->message['Code'] = 500;
            $this->message['Message'] = $e->getMessage();
            return $this->message;
        }
    }

    public function UpdateUserPassword($pass,$id) : array{
        try{
            self::setup();
            $user = R::load('rfusuarios',$id);
            $user->_contrasena = $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);
            $this->message['Code'] = 200;
            $this->message['Message'] = 'Password updated successfully';
            R::store($user);
            return $this->message;
        }catch(\Exception $e){
            $this->message['Code'] = 500;
            $this->message['Message'] = $e->getMessage();
            return $this->message;
        }
    }
    public function insertUserAuditory($data) : array{
        try{
            self::setup();
            $user = R::dispense('rfusuarioauditoria');
            $user->_id_usuario = $data;
            //$user->_fecha_creacion = $data['fecha_creacion'];
            R::store($user);
            $this->message['Code'] = 201;
            $this->message['Message'] = 'User auditory regist create';
            return $this->message;
        }catch(\Exception $e){
            $this->message['Code'] = 500;
            $this->message['Message'] = 'Excepcion?';
            return $this->message;
        }
    }

    public function loginUserAuditory($id){
        try{
            self::setup();
            $user_auditory = R::findOne('rfusuarioauditoria','_id_usuario=?',[$id]);
            if(!$user_auditory){
              
                $this->message['Code'] = 404;
                $this->message['Message'] = 'Usuario no encontrado';
                return $this->message;
            }
            date_default_timezone_set('America/Bogota');

            $user_auditory->_fecha_ultimo_acceso = R::isoDateTime();
            R::store($user_auditory);
            $this->message['Code'] = 200;
            $this->message['Message'] = 'Auditoria Actualizada';
            return $this->message;
        }catch(\Exception $e){
            $this->message['Code'] = 500;
            $this->message['Message'] = $e->getMessage();
            return $this->message;
        }
    }

    /**
     * @param int $id
     * @return User
     * @throws UserNotFoundException
     */
}
