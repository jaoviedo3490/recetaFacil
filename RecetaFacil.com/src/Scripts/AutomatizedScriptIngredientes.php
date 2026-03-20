<?php

namespace App\Scripts;

use App\Infrastructure\Redis\Redis_cli;
use RedBeanPHP\R;

class AutomatizedScriptIngredientes  extends \App\Infrastructure\DataBase\ORM
{


    public function __construct()
    {
        ini_set('memory_limit', '2G');
        $redis = new Redis_cli();
        $resultDataSetIngredientes = $redis->variableIsExistAtRedis('dataSet_ingredientes');
        if ($resultDataSetIngredientes['Code'] === '404') {
            $this->loadIngredientes_at_redis();
            //$redis->createVar("dataSet_ingredientes", json_encode($this->loadIngredientes_at_redis()));
        } else {
            return $resultDataSetIngredientes;
        }
    }
    public function loadIngredientes_at_redis()
    {
        $redis = new Redis_cli();
        try {
            self::setup();
            $last_id = 0;
            $redis_data = array("id" => array(), 'Nombre' => array());

            $resultDataSet = $redis->variableIsExistAtRedis('dataSet_ingredientes');
            if ($resultDataSet['Code'] === '404') {
                //$redis->createVar("dataSet_ingredientes", json_encode($redis_data));
                do {
                    $rows = R::getAll('SELECT id, _nombre from rfingredients where id > ? limit 5000', [$last_id]);
                    foreach ($rows as $row) {
                        //$redis_response = $redis->setList("dataSet_ingredientes", strval($row['_nombre']));
                        //array_push($redis_data['id'], $row['id']);
                        array_push($redis_data['Nombre'],$row['_nombre']);
                        $last_id = $row['id'];
                        $redis_response = $redis->setList("dataSet_ingredientes", $row['_nombre']);
                        //$redis_response = $redis->createVar("dataSet_ingredientes", json_encode($redis_data));
                    }

                    if (count($rows) === 0) {
                        return $redis_response;
                        break;
                    }
                } while (true);
            }


            return ["Code" => '200', 'Message' => 'Se almaceno correcto', "data" => $redis_data];
        } catch (\Exception $e) {
            return ["Code" => '500', 'Message' => $e];
        }
    }
}
