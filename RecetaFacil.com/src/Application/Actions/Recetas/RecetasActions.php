<?php
namespace App\Application\Actions\Recetas;
use App\Application\Actions\Action;
use App\Domain\Recetas\RecetasRepository;
use Psr\Log\LoggerInterface;



abstract class RecetasActions extends Action
{
    protected RecetasRepository $recetasRepository;

    public function __construct(LoggerInterface $logger, RecetasRepository $recetasRepository)
    {
        parent::__construct($logger);
        $this->recetasRepository = $recetasRepository;
    }
}


?>