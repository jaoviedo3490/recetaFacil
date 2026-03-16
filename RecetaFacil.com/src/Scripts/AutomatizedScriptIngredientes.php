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
            $result = $this->loadIngredientes_at_redis();
            $redis->createVar("dataSet_ingredientes", json_encode($result));
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
            do {
                $rows = R::getAll('SELECT id, _nombre from rfingredients where id > ? limit 5', [$last_id]);
                foreach ($rows as $row) {
                    $redis_response = $redis->setList("dataSet_ingredientes", strval($row['_nombre']));
                    $last_id = $row['id'];
                }

                if (count($rows) === 0) {
                    return $redis_response;
                    break;
                }
                unset($rows);
                gc_collect_cycles();
            } while (true);

            return ["Code" => '200', 'Message' => 'Se almaceno correcto', "data" => $redis_data];
        } catch (\Exception $e) {
            return ["Code" => '500', 'Message' => $e];
        }
    }
}
