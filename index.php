<?php

require_once("vendor/autoload.php"); //Autoloader do composer
require_once("config/config.php"); //Arquivo de configuração
use Rain\Tpl;

$init = configurar_views();
$init->draw('landing');
?>
