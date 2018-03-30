<?php
require("../config/config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET'){

  switch($_GET['task']){

    //Caminhos de exibição
    case 'URI_view':

    break;
    case 'list_pre':
      Helper::update_list('PRESCRIPTIONS');
      print(Helper::make_template('show_prescriptions', null, true));
    break;
    case 'list_res':
      Helper::update_list('RESULTS');
      print(Helper::make_template('show_results', null, true));
    break;
    case 'list_com':
      Helper::update_list('COMMENTS');
      print(Helper::make_template('show_comments',null, true));
    break;

    //Controla a exibição dos views pertinentes
    case 'add_pre':


    break;
    case 'add_res':

    break;
    case 'add_com':
      print(Helper::make_template("comment_form",array('task'=> 'add_com'), true));

    break;

    case 'edit_pre':

    break;
    case 'edit_res':

    break;
    case 'edit_com':
      print(Helper::make_template("comment_form",array('task'=>'edit_data','patient_data_id'=>$_GET['patient_data_id']), true));
    break;
  }
  exit();
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST'){

  var_dump($_POST);

  if(!isset($_POST['patient_data_id'])){
    $ptt_data = array();
    $ptt_data['author'] = $_SESSION['active_user_id']['id'];
    $ptt_data['patient'] = $_POST['patient'];
    $ptt_data['subject'] = $_POST['subject'];
    $ptt_data['content'] = $_POST['content'];
  }
  else{
    $patient_data = PatientData::get_from_id($_POST['patient_data_id']);
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

      $comment->add();
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
        $patient_data->update_patient_data();
    break;
    default:
      exit();
    break;    
  }
  Helper::make_template('view',null, true);
}
else {
  throw new Exception("Invalid method exception");
}



?>
