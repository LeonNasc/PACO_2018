<?php
require("config/config.php"); //Arquivo de configuração

if(isset($_SESSION['active_user_id'])){
  Helper::make_template('dashboard',array('patients'=>Patient::get_patient_list(User::get_active_user_id())));
  }
else
  Helper::show_landing();
?>
