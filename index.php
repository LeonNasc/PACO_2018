<?php
require("config/config.php"); //Arquivo de configuração

if(isset($_SESSION['active_user_id']))
  Helper::make_template('dashboard',array('patients'=>json_decode(Patient::get_patient_list($_SESSION['active_user_id']['id']),true)));
else
  Helper::show_landing();

?>
