<?php
namespace App\Application\Actions\Reports;
use App\Application\Actions\Action;
use App\Domain\Recetas\RecetasRepository;
use Psr\Log\LoggerInterface;



abstract class ReportsActions extends Action
{
    protected RecetasRepository $recetasRepository;

    public function __construct(LoggerInterface $logger, RecetasRepository $recetasRepository)
    {
        parent::__construct($logger);
        $this->recetasRepository = $recetasRepository;
    }
}


?>