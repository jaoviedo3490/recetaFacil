<?php

namespace App\Application\Actions\Reports;
use App\Application\Actions\Action;
use App\Domain\Recetas\RecetasRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;


class ReportsHome extends ReportsActions{
    
    public function __construct(LoggerInterface $logger, RecetasRepository $recetasRepository ){
        parent::__construct($logger, $recetasRepository);
    }

    protected function action() : Response{
       try{
         $data = $this->request->getParsedBody();
        if(!isset($data['date_init']) || !isset($data['date_end']) || !isset($data['today'])){
            $this->logger->info("Los datos enviados desde el cliente{date_end,date_init} estan incompletos o han sido modificados");
            return $this->respondWithData(['Code'=>401,"Message"=>"Datos incompletos o corruptos"]);
        }
        $result = $this->recetasRepository->viewStatusWeek($data['date_init'],$data['date_end']);
        $result_today = $this->recetasRepository->viewStatusToday($data['today']." 00:00:00",$data['today']." 23:59:59");
        $result_today_collections= $this->recetasRepository->viewStatusWeekCollection($data['today']." 00:00:00",$data['today']." 23:59:59");
        
        $all_recetas = $this->recetasRepository->executeSQL("SELECT COUNT(*)AS CANTIDAD FROM rfrecetas");
        $all_collections = $this->recetasRepository->executeSQL("SELECT COUNT(*)AS CANTIDAD FROM rfcoleccionrecetas");
        $all_colections_week = $this->recetasRepository->executeSQL("SELECT COUNT(*)AS CANTIDAD  FROM rfcoleccionrecetas where _FECHA_REGISTRO BETWEEN '".$data['date_init']."' AND NOW()");
        $all_recetas_on_week = $this->recetasRepository->executeSQL("SELECT COUNT(*)AS CANTIDAD  FROM rfrecetas where _FECHA_REGISTRO BETWEEN '".$data['date_init']."' AND NOW()");
        $top_5_recetas = $this->recetasRepository->ultimate_recets("5");
        
        $reports["statusWeek"] = $result;
        $reports['today'] = $result_today['data'][0]['CANTIDAD'];
        $reports['collections'] = $result_today_collections[0]['CANTIDAD'];
        $reports['rf_all_Week'] = $all_recetas_on_week['data'][0]['CANTIDAD'];
        $reports['rf_top_5'] = $top_5_recetas['data'];
        $reports['rf_all_week_collections'] = $all_colections_week['data'][0]['CANTIDAD'];
        $reports['rf_all_Week_percent'] = round(($reports['rf_all_Week'] * 100) / $all_recetas['data'][0]['CANTIDAD'], 1);
        $reports['rf_all_'] = round(($reports['today'] * 100) / $all_recetas['data'][0]['CANTIDAD'], 1);
        
        $reports['rf_collect_today'] = round(($reports['collections'] * 100) / $all_collections['data'][0]['CANTIDAD'], 1);
        $reports['rf_all_week_collections_percent'] = round(($reports['rf_all_week_collections'] * 100) / $all_collections['data'][0]['CANTIDAD'],1);
        
        return $this->respondWithData($reports);
       }catch(\Exception $e){
            $this->message['Code'] = 500;
            $this->message['Message'] = $e->getMessage();
           return $this->respondWithData($message);
        }
        
    }
}

?>