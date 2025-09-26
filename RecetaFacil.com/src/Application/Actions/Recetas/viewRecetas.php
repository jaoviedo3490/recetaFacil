<?php

namespace App\Application\Actions\Recetas;
use App\Domain\Recetas\RecetasRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class viewRecetas extends RecetasActions{
    public function __construct(LoggerInterface $logger, RecetasRepository $recetasRepository){
        parent::__construct($logger,$recetasRepository);
    }

    protected function action(): Response{
       $recetas = $this->recetasRepository->findAll();
       $this->logger->info('Receta obtenida con exito');
       return $this->respondWithData($recetas);
    }
}