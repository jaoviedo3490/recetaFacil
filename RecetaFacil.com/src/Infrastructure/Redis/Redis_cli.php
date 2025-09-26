<?php

namespace App\Infrastructure\Redis;

class Redis_cli {
    private $redisVariables = array();
    public $redis_client;
    public $message = array("Message"=>"","Code"=>"");
    public function __construct(){
        try{
            $this->redis_client = new \Redis();
            $this->redis_client->connect('127.0.0.1',6379);
        }catch(\Exception $e){
            $this->message['Message'] = $e->getMessage();
            $this->message['Code'] = "500";
            return $this->message;
        }
    }
    public function ping(){
        try{
            if($this->redis_client->ping()){
                $this->message['Message'] = "PONG";
                $this->message['Code'] = '200';
                return $this->message;
            }else{
                $this->message['Message'] ="Error al inciar Redis";
                $this->message['Code'] = '500';
                return $this->message;
            }
        }catch(\Exception $e){
            $this->message['Message'] = $e->getMessage();
            $this->message['Code'] = "500";
            return $this->message;
        }
    }
    public function createVar($key,$value){
        try{
            $this->setRedisVariable($key,$value);
            return $this->message;
        }catch(\Exception $e){
            $this->message['Message'] = $e->getMessage();
            $this->message['Code'] = "500";
            return $this->message;
        }
    }

    public function deleteVar($key){
        try{
            $this->redis_client->del($key);
            $this->message['Message'] = "Sesion eliminada con exito";
            $this->message['Code'] = "200";
            return $this->message;
        }catch(\Exception $e){
            $this->message['Message'] = $e->getMessage();
            $this->message['Code'] = "500";
            return $this->message;
        }
    }
    public function createTempVar($key,$value,$time){
        try{
            $this->setRedisVariable($key,$value,$time);
            return $this->message;
        }catch(\Exception $e){
            $this->message['Message'] = $e->getMessage();
            $this->message['Code'] = "500";
            return $this->message;              
        }
    }
    public function getRedisVariables() : array{
        return $this->redisVariables;
    }
    public function setRedisVariable($key,$value,$time=0){
        try{
            if($time>0){
                $this->redis_client->setex($key,(int)$time,$value);
            }else{
                $this->redis_client->set($key,$value);
            }      
            $this->message['Message'] = "Variable creada correctamente";
            $this->message['Code'] = "200";
            return $this->message;
        }catch(\Exception $e){
            $this->message['Message'] = $e->getMessage();
            $this->message['Code'] = "500";
            return $this->message;
        }
    }
    public function getRedisVar($key) : array{
        try{
            $var = $this->redis_client->get($key);
            if($var){
                $this->message['Message'] = "Variable obtenida correctamente";
                $this->message['Code'] = "200";
                $this->message['Data'] = $var;
            }else{
                $this->message['Message'] = "Variable no encontrada";
                $this->message['Code'] = "404";
                
            }
            return $this->message;
        }catch(\Exception $e){
            $this->message['Message'] = $e->getMessage();
            $this->message['Code'] = "500";
            return $this->message;
        }
    }
}

