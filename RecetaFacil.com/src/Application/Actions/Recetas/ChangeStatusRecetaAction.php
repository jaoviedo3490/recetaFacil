<?php

namespace App\Application\Actions\Recetas;
use App\Application\Actions\Action;
use App\Domain\Recetas\RecetasRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class ChangeStatusRecetaAction extends RecetasActions {
    public function __construct(LoggerInterface $logger ,RecetasRepository $recetasRepository){
        parent::__construct( $logger,$recetasRepository);
    }

    protected function action() : Response{
        $data = $this->request->getParsedBody();
        if(!isset($data['value']) || !isset($data['id'])){
            $this->logger->info('Los datos incrustados en el cuerpo de la peticion estan corruptos o han sido alterados');
            return $this->respondWithData(['Code'=>400,"Message"=>'Datos corruptos o alterados']);
        }
        $updateValue = $data['value'];
        $id = $data['id'];
        $result = $this->recetasRepository->updateReceta($updateValue,$id);
        $this->logger->info('Estado cambiado exitosamente');
        return $this->respondWithData($result);
    }
    
}


?>