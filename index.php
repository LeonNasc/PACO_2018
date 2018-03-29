<?php
require("config/config.php"); //Arquivo de configuração

if(isset($_SESSION['active_user_id'])){
  $_SESSION['patient_list'] = Patient::get_patient_list($_SESSION['active_user_id']['id']);
  Helper::make_template('dashboard',array('patients'=>$_SESSION['patient_list']));
  }
else
  Helper::show_landing();


/* TODO: Implementar ROTAS
//Quebra a URI em duas partes
$request_uri = explode('?', $_SERVER['REQUEST_URI'], 2);

const HOMEDIR = "127.0.0.1/PACO_2018/"

// Rotas
switch ($request_uri[0]) {
  // Home
  case HOMEDIR.'/':
      if(isset($_SESSION['active_user_id'])){
        $_SESSION['patient_list'] = json_decode(Patient::get_patient_list($_SESSION['active_user_id']['id']),true);
        Helper::make_template('dashboard',array('patients'=>$_SESSION['patient_list']));
        }
      else
        Helper::show_landing();
      break;
  // Acompanhamento
  case HOMEDIR.'/acompanhar':
      require '../views/about.php';
      break;
  // Everything else
  default:
      header('HTTP/1.0 404 Not Found');
      require 'Views/templates/404.html';
      break;
}
*/
?>
