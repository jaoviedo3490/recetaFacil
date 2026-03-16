<?php

namespace App\Application\Actions\AsyncronousTasks;

use App\Application\Actions\Action;
use App\Scripts\AutomatizedScript;
use App\Scripts\AutomatizedScriptIngredientes;
use Psr\Log\LoggerInterface;



abstract class AsyncronousActions extends Action
{
    protected AutomatizedScript  $loadRedisData;
    protected AutomatizedScriptIngredientes $loadIngredientes;

    public function __construct(LoggerInterface $logger, AutomatizedScript $loadRedisData, AutomatizedScriptIngredientes $loadIngredientes)
    {
        parent::__construct($logger, $loadRedisData,$loadIngredientes);
        $this->loadRedisData = $loadRedisData;
        $this->loadIngredientes = $loadIngredientes;
    }
}
