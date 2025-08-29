<?php

namespace App\Infrastructure\DataBase;

use RedBeanPHP\R;
use RedBeanPHP\RedException;


class ORM{
    public static $Message = array("Message"=>"");
    public static function setup(){
        try{
            R::setup('mysql:host=127.0.0.1;dbname=rf_bd;port=3306;', 'root', '');
             self::$Message["Message"] = "ORM instanciado correctamente";
             self::$Message["Code"] = "200";
            return  self::$Message;
        }catch(RedException $e){
            self::$Message['Message'] = "Ocurrio un error en el ORM[setup()]: ".$e->getMessage();
            return self::$Message;
        }   
    }
    

}