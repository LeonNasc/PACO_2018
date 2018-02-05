<?php

// Exibição de erros
error_reporting(-1);
ini_set("display_errors", "1");
ini_set("log_errors", 1);
ini_set("error_log", "/bin/php-error.log");

date_default_timezone_set("America/Sao_Paulo");

/* ------------------ Configuração de sessão  ---------------------------*/

if(!isset($_SESSION))
  session_start();

/* ------------------ Configuração do RainTPL ---------------------------*/


use Rain\Tpl;
$config = array(
                 "tpl_dir"       => "Views/templates/",
                 "cache_dir"     => "Views/cache/"
);
Tpl::configure( $config );

/* -------------------------- Meu Autoload ------------------------------*/

function __autoload($class_name) {
    include "Model/" . $class_name . '.php';
};

?>
