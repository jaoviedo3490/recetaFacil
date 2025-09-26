<?php

namespace App\Application\Actions\Recetas;
use App\Application\Actions\Action;
use App\Domain\Recetas\RecetasRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class CreateRecetaAction extends RecetasActions{

    public function __construct(LoggerInterface $logger,RecetasRepository $recetasRepository ){
        parent::__construct($logger,$recetasRepository);
    }

    protected function action() : Response{
        $data = $this->request->getParsedBody();
        if(!isset($data['Nombre']) || !isset($data['Ingredientes'])){
            $this->logger->info('La informacion enviada desde el cliente no existe o esta mal enviada {Nombre} , {Ingredientes}');
            return $this->respondWithData(['Code'=>'400','Message'=>"Informacion incompleta o alterada"]);
        }
        $data_recetas = $this->recetasRepository->createReceta($data);
        return $this->respondWithData($data_recetas);
    }
}

?>