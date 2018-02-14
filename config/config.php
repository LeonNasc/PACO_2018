<?php

date_default_timezone_set("America/Sao_Paulo");

/* -------------------- Configuração de Erros ---------------------------*/
error_reporting(E_ALL);
ini_set("display_errors", "on");
ini_set("log_errors", 1);
/* -------------------- Configuração de autoloads -----------------------*/
function meu_autoload($class_name) {
   include __DIR__."/../Model/" . $class_name . '.php';
};
spl_autoload_register('meu_autoload');

//Autoloader do composer
require_once(__DIR__."/../vendor/autoload.php");

/* ------------------ Configuração de sessão  ---------------------------*/

if(!isset($_SESSION))
  session_start();

Helper::check_login_status();

/* ------------------ Configuração do RainTPL ---------------------------*/


use Rain\Tpl;

$config = array(
                 "tpl_dir"       => __DIR__."/../Views/templates/",
                 "cache_dir"     => __DIR__."/../Views/cache/"
);
Tpl::configure( $config );
?>
