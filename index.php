<?php
require("config/config.php"); //Arquivo de configuração


use Rain\Tpl;

$init = configurar_views();
$init->draw('registro');
?>
