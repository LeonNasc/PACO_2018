<?php

require("../config/config.php");

use Rain\Tpl;

$info_passed = $_GET ? $_GET : $_POST;

$controller = Controller::get_instance();
$controller->set_method($_SERVER['REQUEST_METHOD']);
$controller->set_task($info_passed['task']);
$controller->set_dados($info_passed);

if(isset($info_passed['actor_object'])){
  $actor = $info_passed['actor_object'];
}

//A maioria das formas são geradas via GET
if($controller->get_method() == 'GET'){
  $controller->handle_forms($actor);
}
//Por sua vez, a maior parte da lógica é requisitada via POST
else if ($controller->get_method() == 'POST'){
  $controller->handle_data($actor);
}
else{
  $controller->show_404();
}
?>