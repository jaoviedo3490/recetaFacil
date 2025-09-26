<?php

namespace App\Domain\Recetas;
use RedBeanPHP\R;

class RecetasRepository extends \App\Infrastructure\DataBase\ORM{

    public $message = array("Code"=>200,"Message"=>"");

    public function createReceta($data){
        try{
           self::setup();
           $recetas = R::dispense('rfrecetas');
           $recetas->_nombre = $data['Nombre'];
           $recetas->_ingredientes = json_encode($data['Ingredientes']);
           R::store($recetas);
            $this->message['Code'] = 201;
            $this->message['Message'] = 'Receta creada correctamente';
            return $this->message;
           
        }catch(\Exception $e){
            $this->message['Code'] = 500;
            $this->message['Message'] = $e->getMessage();
            return $this->message;
        }
    }

    public function findAll(){
        try{
            self::setup();
            $recetas = R::findAll('rfrecetas');
            if(empty($recetas)){
                $this->message['Code'] = 404;
                $this->message['Message'] = "La Receta no existe";
                return $this->message;
            }
            $this->message['Code'] = 200;
            $this->message['Message'] = "Recetas seleccionadas con exito";
            $this->message['Data'] = $recetas;
            return $this->message;

        }catch(\Exception $e){
            $this->message['Code'] = 500;
            $this->message['Message'] = $e->getMessage();
            return $this->message;
        }
    }
    public function createCollection($id_receta){
        try{
            self::setup();
            $collection = R::dispense('rfcoleccionrecetas');
            $collection->_id_receta = $id_receta;
            R::store($collection);
            $this->message['Code'] = 201;
            $this->message['Message'] = 'Coleccion creada correctamente';
            return $this->message;
        }catch(\Exception $e){
            $this->message['Code'] = 500;
            $this->message['Message'] = $e->getMessage();
            return $this->message;
        }
    }

    public function viewRecetaCollection($id){
        try{
            self::setup();
            $dataCollection = R::findOne('rfcoleccionrecetas','id=?',[$id]);
            if(empty($dataCollection)){
                $this->message['Code'] = 404;
                $this->message['Message'] = 'Coleccion no encontrada';
                return $this->message;
            }
            $this->message['Code'] = 200;
            $this->message['Message'] = 'Coleccion encontrada';
            $this->message['Data'] = $dataCollection;
            return $this->message;
        }catch(\Exception $e){
            $this->message['Code'] = 500;
            $this->message['Message'] = $e->getMessage();
            return $this->message;
        }
    }
    public function viewStatusWeek($initDate,$endDate){
        try{
             unset($this->message);
            self::setup();
            $viewStatus = R::getAll("SELECT COUNT(*)AS CANTIDAD  , DATE_FORMAT(_FECHA_REGISTRO,'%Y-%m-%d') AS DIA FROM rfrecetas where _FECHA_REGISTRO BETWEEN '$initDate' AND NOW() GROUP BY DATE_FORMAT(_FECHA_REGISTRO,'%Y-%m-%d')");
            //$viewStatus = R::getAll("SELECT COUNT(*)AS CANTIDAD , DATE_FORMAT(_FECHA_REGISTRO,'%Y-%m-%d') as DIA  FROM rfrecetas where _FECHA_REGISTRO GROUP BY _FECHA_REGISTRO;");
            if(!$viewStatus){
                $this->message['Code'] = 404;
                $this->message['Message'] = 'Sin Datos';
                $this->message['data'] = [["CANTIDAD"=>"0"]];

                return $this->message;
            }
            $this->message['Code'] = 200;
            $this->message['Message'] = 'Datos extraidos';
            $this->message['data'] =$viewStatus; 
            return $this->message;
        }catch(\Exception $e){
            $this->message['Code'] = 500;
            $this->message['Message'] = $e->getMessage();
            return $this->message;
        }
    }
    public function viewStatusWeekCollection($initDate,$endDate){
        try{
            unset($this->message);
            self::setup();
           // $viewStatus = R::getAll("SELECT COUNT(*)AS CANTIDAD  , _FECHA_REGISTRO AS DIA FROM rfcoleccionrecetas where _FECHA_REGISTRO BETWEEN '$initDate' AND '$endDate' GROUP BY _FECHA_REGISTRO");
            $viewStatus = R::getAll("SELECT COUNT(*)AS CANTIDAD  , DATE_FORMAT(_FECHA_REGISTRO,'%Y-%m-%d') AS DIA FROM rfcoleccionrecetas where _FECHA_REGISTRO BETWEEN '$initDate' AND NOW() GROUP BY DATE_FORMAT(_FECHA_REGISTRO,'%Y-%m-%d')");
            if(!$viewStatus){
                $this->message['Code'] = 404;
                $this->message['Message'] = 'Sin Datos';
                $this->message['data'] = [["CANTIDAD"=>"0"]];

                return $this->message;
            }
            $this->message['Code'] = 200;
            $this->message['Message'] = 'Datos extraidos';
            $this->message['data'] =$viewStatus; 
            return $viewStatus;
        }catch(\Exception $e){
            $this->message['Code'] = 500;
            $this->message['Message'] = $e->getMessage();
            return $this->message;
        }
    }
    public function viewStatusToday($initDate,$endDate){
        try{
            unset($this->message);
            self::setup();
            $viewStatus = R::getAll("SELECT COUNT(*)AS CANTIDAD FROM rfrecetas where _FECHA_REGISTRO BETWEEN '$initDate' AND '$endDate'");
            //$viewStatus = R::getAll("SELECT COUNT(*)AS CANTIDAD , DATE_FORMAT(_FECHA_REGISTRO,'%Y-%m-%d') as DIA  FROM rfrecetas where _FECHA_REGISTRO GROUP BY _FECHA_REGISTRO;");
            if(!$viewStatus){
                $this->message['Code'] = 404;
                $this->message['Message'] = 'Sin Datos';
                $this->message['data'] = [["CANTIDAD"=>"0"]];

                return $this->message;
            }
            $this->message['Code'] = 200;
            $this->message['Message'] = 'Datos extraidos';
            $this->message['data'] =$viewStatus; 
            return $this->message;
        }catch(\Exception $e){
            $this->message['Code'] = 500;
            $this->message['Message'] = $e->getMessage();
            return $this->message;
        }
    }
    public function executeSQL($initDate){
        try{
            unset($this->message);
            self::setup();
            $viewStatus = R::getAll($initDate);
            //$viewStatus = R::getAll("SELECT COUNT(*)AS CANTIDAD , DATE_FORMAT(_FECHA_REGISTRO,'%Y-%m-%d') as DIA  FROM rfrecetas where _FECHA_REGISTRO GROUP BY _FECHA_REGISTRO;");
            if(!$viewStatus){
                $this->message['Code'] = 404;
                $this->message['Message'] = 'Sin Datos';
                return $this->message;
            }
            $this->message['Code'] = 200;
            $this->message['Message'] = 'Datos extraidos';
            $this->message['data'] =$viewStatus; 
            return $this->message;
        }catch(\Exception $e){
            $this->message['Code'] = 500;
            $this->message['Message'] = $e->getMessage();
            return $this->message;
        }
    }
    public function ultimate_recets($limit){
        try{
            self::setup();
            $recetas = R::getAll("SELECT * FROM RFRECETAS ORDER BY _FECHA_REGISTRO DESC LIMIT $limit");
            if(!$recetas){
                $this->message['Code'] = 404;
                $this->message['Message'] = 'Sin datos';
                return $this->message;
            }
            $this->message['Code'] = 200;
            $this->message['Message'] = 'Datos extraidos';
            $this->message['data'] =$recetas; 
            return $this->message;
        }catch(\Exception $e){
            $this->message['Code'] = 500;
            $this->message['Message'] = $e->getMessage();
            return $this->message;
        }
    }
}


?>