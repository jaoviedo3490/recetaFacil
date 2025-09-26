<?php

namespace App\Application\Actions\Recetas;
use App\Application\Actions\Action;
use App\Domain\Recetas\RecetasRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;


class ViewCollectionsRecetas extends RecetasActions{
    
    public function __construct(LoggerInterface $logger, RecetasRepository $recetasRepository ){
        parent::__construct($logger, $recetasRepository);
    }

    protected function action() : Response{
        $data = $this->request->getParsedBody();
        if(!isset($data['getCollectionID'])){
            $this->logger->info("Los datos enviados desde el cliente{getCollectionID} estan incompletos o han sido modificados");
            return $this->respondWithData(['Code'=>401,"Message"=>"Datos incompletos o corruptos"]);
        }
        $result = $this->recetasRepository->viewRecetaCollection($data['id']);
        return $this->respondWithData($result);
    }
}

?>