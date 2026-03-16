<?php
require '/mnt/e/xampp/htdocs/recetaFacil/vendor/autoload.php';

use App\Infrastructure\DataBase\ORM;
exec('php /mnt/e/xampp/htdocs/recetaFacil/RecetaFacil.com/src/Scripts/AutomatizedScript.php > /mnt/c/Users/ADMIN/worker_log.txt 2>&1 &');
exec('php /mnt/e/xampp/htdocs/recetaFacil/RecetaFacil.com/src/Scripts/AutomatizedScriptIngredientes.php > /mnt/c/Users/ADMIN/worker_log.txt 2>&1 &');


ORM::setup();


?>