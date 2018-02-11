<?php

// Meu Autoload
function my_autoloader($class_name) {
   include __DIR__."/../Model/" . $class_name . '.php';
};
spl_autoload_register('my_autoloader');

require_once(__DIR__."/../vendor/autoload.php"); //Autoloader do composer

// Exibição de erros
error_reporting(E_ALL);
ini_set("display_errors", "on");
ini_set("log_errors", 1);
ini_set("error_log", "/home/leon/dev/PACO_2018/config/log/php-error.log");

date_default_timezone_set("America/Sao_Paulo");

/* ------------------ Configuração de sessão  ---------------------------*/

if(!isset($_SESSION))
  session_start();

/* ------------------ Configuração do RainTPL ---------------------------*/


use Rain\Tpl;
$config = array(
                 "tpl_dir"       => __DIR__."/../Views/templates/",
                 "cache_dir"     => __DIR__."/../Views/cache/"
);
Tpl::configure( $config );
?>
