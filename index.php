<?php
require("config/config.php"); //Arquivo de configuração

if(isset($_SESSION['active_user_id'])){
  $_SESSION['patient_list'] = Patient::get_patient_list($_SESSION['active_user_id']['id']);
  Helper::make_template('dashboard',array('patients'=>$_SESSION['patient_list']));
  }
else
  Helper::show_landing();
?>
