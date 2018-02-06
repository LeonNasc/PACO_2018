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
