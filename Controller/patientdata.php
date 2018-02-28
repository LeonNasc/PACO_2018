<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET'){

  switch($_POST['task']){

    //Caminhos de exibição
    case 'URI_view'

    break;
    case 'list_pre'

    break;
    case 'list_res'

    break;
    case 'list_com'

    break;
    default:

    break;

    //Controla a exibição dos views pertinentes
    case 'add_pre'


    break;
    case 'add_res'

    break;
    case 'add_com'

    break;
    case 'edit_pre'

    break;
    case 'edit_res'

    break;
    case 'edit_com'

    break;
  }
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST'){

  $ptt_data = array();

  if(!isset($patient_data_id)){
    $ptt_data['author'] = $_SESSION['active_user_id']['id'];
    $ptt_data['patient'] =  $_POST['patient'];
    $ptt_data['subject'] = $_POST['subject'];
    $ptt_data['content'] = $_POST['content'];
  }
  else{
    $patient = PatientData::get_from_id($patient_data_id);
  }

  switch($_POST['task']){
    case 'add_pre'

    $comment = new PatientData($ptt_data, PatientData::PRESCRIPTION);
    break;
    case 'add_res'

    $comment = new PatientData($ptt_data, PatientData::LAB_RESULT);
    break;
    case 'add_com'

    $comment = new PatientData($ptt_data, PatientData::COMMENT);

    break;
    case 'edit_pre'

    break;
    case 'edit_res'

    break;
    case 'edit_com'

    break;
    default:

    break;
  }
}
else {
  throw new Exception("Invalid method exception");
}



?>
