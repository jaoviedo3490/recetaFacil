<?php

namespace App\Infrastructure\DataBase;

use RedBeanPHP\R;
use RedBeanPHP\RedException;


class ORM{
    public static $Message = array("Message"=>"");
    public static function setup(){
        try{
            R::setup(''.$_ENV['DB_DRIVER'].':host='.$_ENV['DB_HOST'].';dbname='.$_ENV['DB_NAME'].';port='.$_ENV['DB_PORT'].';', $_ENV['DB_USER'], $_ENV['DB_PASS']);
             self::$Message["Message"] = "ORM instanciado correctamente";
             self::$Message["Code"] = "200";
            return  self::$Message;
        }catch(RedException $e){
            self::$Message['Message'] = "Ocurrio un error en el ORM[setup()]: ".$e->getMessage();
            return self::$Message;
        }   
    }
}