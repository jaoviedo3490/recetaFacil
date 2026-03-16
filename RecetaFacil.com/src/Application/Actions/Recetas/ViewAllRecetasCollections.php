<?php

namespace App\Application\Actions\Recetas;
use App\Application\Actions\Action;
use App\Domain\Recetas\RecetasRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;


class ViewAllRecetasCollections extends RecetasActions{
    
    public function __construct(LoggerInterface $logger, RecetasRepository $recetasRepository ){
        parent::__construct($logger, $recetasRepository);
    }

    protected function action() : Response{
        $data = $this->request->getParsedBody();
        $ID_USER = $this->request->getAttribute('user_id');
        if(!isset($data['getAllCollection'])){
            $this->logger->info("Los datos enviados desde el cliente{getAllCollection} estan incompletos o han sido modificados");
            return $this->respondWithData(['Code'=>401,"Message"=>"Datos incompletos o corruptos"]);
        }
        $result = $this->recetasRepository->findAllCollection($ID_USER);
        return $this->respondWithData($result);
    }
}

?>