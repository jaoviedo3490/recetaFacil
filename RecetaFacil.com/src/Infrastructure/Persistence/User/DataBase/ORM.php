<?php

namespace RecetaFacil\Infrastructure\DataBase;

use RedBeanPHP\R;
use RedBeanPHP\RedException;

$Message = array("Message"=>"");
class ORM{
    public static function setup(){
        try{
            R::setup('mysql:host='.getenv('DB_HOST').';dbname='.
                getenv('DB_NAME').';port='.getenv('DB_PORT').';user='.
                    getenv('DB_USER').';password='.getenv('DB_PASS'));
            $Message["Message"] = "ORM instanciado correctamente";
            return $Message;
        }catch(RedException $e){
            $Message["Message"] = "Ocurrio un error el instanciar el ORM:".$e->getMessage();
            return $Message;
        }   
    }
}