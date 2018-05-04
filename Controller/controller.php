<?php

require("../config/config.php");

use Rain\Tpl;

$controller = Controller::get_instance();
$controller->set_method($_SERVER['REQUEST_METHOD']);

$info_passed = $_GET? $_GET : $_POST;
$actor = isset($info_passed['actor_object'])?$info_passed['actor_object']:null;

if($controller->get_method() == 'GET'){
  switch($actor){
      case 'user':
        $controller->render_user_forms($info_passed);
      break;
      case 'patient':
        $controller->control_patient_actions($info_passed);
      break;
      case 'patient_data':
        $controller->control_patientdata_actions($info_passed);
      break;
      default:
        $controller->show_404();
      break;
  }
}
else{
  switch($actor){
      case 'user':
        $controller->control_user_actions($info_passed);
      break;
      case 'patient':
        $controller->control_patient_actions($info_passed);
      break;
      case 'patient_data':
        $controller->control_patientdata_actions($info_passed);
      break;
      default:
        $controller->show_404();
      break;
  }
}
?>