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

  if(!isset($patient_data_id)){
    $ptt_data = array();
    $ptt_data['author'] = $_SESSION['active_user_id']['id'];
    $ptt_data['patient'] = $_POST['patient'];
    $ptt_data['subject'] = $_POST['subject'];
    $ptt_data['content'] = $_POST['content'];
  }
  else{
    $patient_data = PatientData::get_from_id($patient_data_id);
  }

  switch($_POST['task']){

    case 'add_pre':
      $prescription = new PatientData($ptt_data, PatientData::PRESCRIPTION);
    break;

    case 'add_res':
      $result = new PatientData($ptt_data, PatientData::LAB_RESULT);
    break;

    case 'add_com':
      $comment = new PatientData($ptt_data, PatientData::COMMENT);
    break;

    case 'edit_data': //Caso geral para todos os PatientData

      $content_info = json_decode($patient_data->to_JSON(),true);

        switch(substr($content_info['id'],0,3)){

          case 'prs_':
            $content = json_encode($_POST['prescription']);
          break;

          case 'res_':
            $content = json_encode($_POST['results']);
          break;

          default:
            $content = $_POST['content'];
          break;
        }

        $patient_data->edit($content);
        $patient_data->update();

        Helper::show_template('view',null, true);
    break;
    default:
      exit();
    break;
  }
}
else {
  throw new Exception("Invalid method exception");
}



?>
