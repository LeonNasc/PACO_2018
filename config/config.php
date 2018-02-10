<?php

// Meu Autoload
function my_autoloader($class_name) {
    include "../Model/" . $class_name . '.php';
};
spl_autoload_register('my_autoloader');

require_once("../vendor/autoload.php"); //Autoloader do composer

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
                 "tpl_dir"       => "Views/templates/",
                 "cache_dir"     => "Views/cache/"
);
Tpl::configure( $config );

/*----------------- Funções Helper ---------------------*/

function configurar_views(){

  $t = new Tpl;
  $urls = array();
  $urls['Adicione prescrições e resultados laboratoriais de forma fácil'] = 'https://image.flaticon.com/icons/svg/1/1755.svg';
  $urls['Visualize prescrições anteriores e contraste com resultados laboratoriais'] ='https://image.flaticon.com/icons/svg/344/344074.svg';
  $urls['Comente e discuta sobre os seus pacientes de maneira rápida e simples'] = 'https://image.flaticon.com/icons/svg/134/134807.svg';
  $t->assign('icon_url',$urls);
  $t->assign('title','PACO');
  return $t;
}


?>
