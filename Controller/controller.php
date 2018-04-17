<?php

require("../config/config.php");

use Rain\Tpl;

$controller = Controller::get_instance();
$controller->set_method($_SERVER['REQUEST_METHOD']);

$info = $_GET? $_GET : $_POST;
$actor = isset($info['actor_object'])?$info['actor_object']:null;

switch($actor){
    case 'user':
     $controller->control_user_actions($info);
    break;
    case 'patient':
    $controller->control_patient_actions($info);
    break;
    case 'patient_data':
    $controller->control_patientdata_actions($info);
    break;
    default:
    $controller->show_404();
    break;
}
?>