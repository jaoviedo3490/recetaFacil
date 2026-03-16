<?php

namespace App\Domain\Recetas;
use RedBeanPHP\R;

class RecetasRepository extends \App\Infrastructure\DataBase\ORM{

    public $message = array("Code"=>200,"Message"=>"");

    public function createReceta($data){
        try{
           self::setup();
           $recetas = R::dispense('rfrecetas');
           $recetas->_id_recipe = $data['id_recipe'];
           $recetas->_id_usuario = $data['id_usuario'];
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
public function findAllNotSave($id_usuario){
        try{
            self::setup();
            $recetas = R::find('rfrecetas','_guardada="0" and _id_usuario=?',[$id_usuario]);
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
    public function findAll($id_usuario){
        try{
            self::setup();
            $recetas = R::findAll('rfrecetas','_id_usuario=?',[$id_usuario]);
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
    public function findAllSaves($id_usuario){
        try{
            self::setup();
            $recetas = R::getAll("SELECT rfre.id , rfri._title as _nombre, rfri._ingredients as _ingredientes,rfri._directions as Instrucciones,rfre._fecha_registro FROM RFRECETAS rfre INNER JOIN rfrecipes rfri ON rfre._id_recipe = rfri.id WHERE rfre._guardada = '1' and rfre._id_usuario=? limit 600000",[$id_usuario]);
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
public function findAllCollection($id_usuario){
        try{
            self::setup();
             $recetas = R::getAll(
                "SELECT col.id,col._nombre, count(col._nombre) as 'Cantidad de Recetas' ,col._fecha_registro 
                from rfcoleccionrecetas_details det INNER JOIN rfcoleccionrecetas col ON 
                col.id = det.id_collect_recipe INNER JOIN rfrecetas rfref ON rfref.id = det.id_recipe 
                where col._id_usuario = ? and rfref._guardada = 1 GROUP by col._nombre;",
                [$id_usuario]);
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

    public function viewRecetaCollection($id_usuario){
        try{
            self::setup();
            $dataCollection = R::findAll(
                'SELECT rf_col.id,rf_col._nombre,rf_col._fecha_registro, COUNT(rf_det.id_recipe) as "CANTIDAD DE RECETAS" FROM '.
                'rfcoleccionrecetas_details rf_det INNER JOIN rfrecetas rfref ON rfref.id = rf_det.id_recipe INNER JOIN rfcoleccionrecetas '.
                'rf_col ON rf_col.id = rf_det.id_collect_recipe where rfref._id_usuario = ? AND rfref._guardada = 1',[$id_usuario]
            );
            if(empty($dataCollection)){
                $this->message['Code'] = 404;
                $this->message['Message'] = 'Sin datos';
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
    public function viewStatusWeek($initDate,$endDate,$id_usuario){
        try{
             unset($this->message);
            self::setup();
            $viewStatus = R::getAll("SELECT COUNT(*)AS CANTIDAD , DATE_FORMAT(_FECHA_REGISTRO,'%Y-%m-%d') AS DIA FROM rfrecetas rfre INNER JOIN rfrecipes rfri ON rfre._id_recipe = rfri.id where _FECHA_REGISTRO BETWEEN ? AND NOW() and rfre._id_usuario = ? GROUP BY DATE_FORMAT(_FECHA_REGISTRO,'%Y-%m-%d')",[$initDate,$id_usuario]);
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
    public function viewStatusWeekCollection($initDate,$endDate,$id_usuario){
        try{
            unset($this->message);
            self::setup();
           // $viewStatus = R::getAll("SELECT COUNT(*)AS CANTIDAD  , _FECHA_REGISTRO AS DIA FROM rfcoleccionrecetas where _FECHA_REGISTRO BETWEEN '$initDate' AND '$endDate' GROUP BY _FECHA_REGISTRO");
            $viewStatus = R::getAll("SELECT COUNT(*)AS CANTIDAD  , DATE_FORMAT(_FECHA_REGISTRO,'%Y-%m-%d') AS DIA FROM rfcoleccionrecetas where _FECHA_REGISTRO BETWEEN ? AND NOW() and rfre._id_usuario = ? GROUP BY DATE_FORMAT(_FECHA_REGISTRO,'%Y-%m-%d')",[$initDate,$id_usuario]);
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
    public function viewStatusToday($initDate,$endDate,$id_usuario){
        try{
            unset($this->message);
            self::setup();
            $viewStatus = R::getAll("SELECT COUNT(*)AS CANTIDAD FROM rfrecetas where _FECHA_REGISTRO BETWEEN ? AND ? and _id_usuario = ? ",[$initDate,$endDate,$id_usuario]);
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
    public function ultimate_recets($limit,$id_usuario){
        try{
            unset($this->message);
            self::setup();
            $recetas = R::getAll("SELECT rfre.id , rfri._title as _nombre, rfri._ingredients as _ingredientes,rfre._fecha_registro ,rfri._directions as Instrucciones FROM RFRECETAS rfre INNER JOIN rfrecipes rfri ON rfre._id_recipe = rfri.id WHERE rfre._guardada = 0 AND rfre._id_usuario = ? ORDER BY _FECHA_REGISTRO DESC LIMIT $limit",[$id_usuario]);
            if(!$recetas){
                $this->message['Code'] = 404;
                $this->message['Message'] = "Sin Datos";
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

    public function updateReceta ($value,$id){
        try{
            unset($this->message);
            self::setup();
            $Receta = R::findOne('rfrecetas','id=?',[$id]);
            if(!$Receta){
                $this->message['Code'] = 404;
                $this->message['Message'] = 'Receta no existente';
                return $this->message;
            }
            $Receta->_guardada = $value;
            R::store($Receta);
            $this->message['Code'] = 200;
            $this->message['Message'] = ($value==1) ? "Receta guardada exitosamente" : 'Receta Eliminada Exitosamente';
            return $this->message;
        }catch(\Exception $e){
            $this->message['Code'] = 500;
            $this->message['Message'] = $e->getMessage();
            return $this->message;
        }
    }
}


?>