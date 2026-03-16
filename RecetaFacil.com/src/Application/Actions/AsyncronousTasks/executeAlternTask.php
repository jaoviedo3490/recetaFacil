<?php

namespace App\Application\Actions\AsyncronousTasks;


use App\Scripts\AutomatizedScript;
use App\Scripts\AutomatizedScriptIngredientes;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class executeAlternTask extends AsyncronousActions
{
    protected RecetasRepository $recetasRepository;

    public function __construct(LoggerInterface $logger, AutomatizedScript $automatizedScript, AutomatizedScriptIngredientes $automatizedScriptIngredientes)
    {
        parent::__construct($logger, $automatizedScript,$automatizedScriptIngredientes);
    }

    protected function action(): Response
    {
        $data = $this->request->getParsedBody();
        if (!isset($data['RunTask'])) {

            $this->logger->info('Los datos incrustados en el cuerpo de la peticion estan corruptos o han sido alterados');
            return $this->respondWithData(['Code' => 400, "Message" => 'Datos corruptos o alterados']);
        }
        

        //$result = $this->recetasRepository->createCollection($data['id']);
       // $this->logger->info('Collecion de recetas creada');
        return $this->respondWithData(["Code"=>200,"Mesage"=>"Async process execute"]);
    }
}
