<?php

namespace App\Scripts;

use App\Infrastructure\Redis\Redis_cli;
use RedBeanPHP\R;

class AutomatizedScript  extends \App\Infrastructure\DataBase\ORM
{


    public function __construct()
    {
        ini_set('memory_limit', '2G');
        $redis = new Redis_cli();
        $resultDataSet = $redis->variableIsExistAtRedis('dataSet');
        if ($resultDataSet['Code'] === '404') {
            $result = $this->loadRecipes_at_redis();
            $redis->createVar("dataSet", gzcompress(json_encode($result, JSON_UNESCAPED_UNICODE)));
        }else{
            return $resultDataSet;
        }
    }
    public function loadRecipes_at_redis()
    {
        try {
            self::setup();
            $last_id = 0;
            $redis_data = array('Titulo' => array(), "Ner" => array(), "id" => array());
            do {
                $rows = R::getAll('SELECT id ,_title, _ner from rfrecipes where id > ? limit 5', [$last_id]);
                foreach ($rows as $row) {
                    array_push($redis_data['Titulo'], $row['_title']);
                    array_push($redis_data['Ner'], $row['_ner']);
                    array_push($redis_data['id'], $row['id']);
                    $last_id = $row['id'];
                }

                if (count($rows) === 0) {
                    return $redis_data;
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
