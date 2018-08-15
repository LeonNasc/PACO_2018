<?php
require("config/config.php"); //Arquivo de configuração

if(isset($_SESSION['active_user'])){

  Helper::make_template('dashboard',array('patients'=>Patient::get_patient_list(UserController::get_active_user_id())));
  }
else{
  Helper::show_landing();
}
?>
