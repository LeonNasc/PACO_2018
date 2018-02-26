<?PHP
require("../config/config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET'){
  // Deve devolver um JSON com a lista de pacientes do usuário logado
  switch($_GET['task']){

    case 'add':
      Helper::make_template('patient_form',array('task'=>'add'));
      break;

    case 'edit':
      $patient = Patient::get_from_id($_POST['id']);
      $patient = json_decode($patient->get_patient_data(), true);
      Helper::make_template('patient_form',array('task'=>'edit','id'=>$patient['id']));
      break;

    default:
      Helper::update_list('patients');
      break;
  }
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  //Deve lidar com as funções ADD, DELETE e UPDATE paciente

  switch($_POST['task']){

    case 'add':
      $patient = &$_POST;
      $patient['owner'] = $_SESSION['active_user_id']['id'];

      $patient = new Patient($patient);
      $patient->add_patient();
      $patient = json_decode($patient->get_patient_data(), true);

      Helper::make_template('patient_info',array('patient'=>$patient), false);
      exit();

      break;

    case 'edit':

      $patient = Patient::get_from_id($_POST['id']);
      $patient->change_info($_POST);
      $patient->update_patient_info();

      $patient = json_decode($patient->get_patient_data(),true);
      Helper::make_template('patient_info',array('patient'=>$patient), false);
      break;

    case 'get_data':

      $patient = Patient::get_from_id($_POST['id']);
      $patient = json_decode($patient->get_patient_data(), true);

      Helper::make_template('patient_info',array('patient'=>$patient), false);
      break;

    case 'change_status':

      $patient = Patient::get_from_id($_POST['id']);
      $patient->change_status();
      $patient->update_patient_info();
      $patient = json_decode($patient->get_patient_data(), true);


      Helper::make_template('patient_info',array('patient'=>$patient), false);
      break;

    case 'delete':
      //TODO: Deletar todas as prescrições, resultados e comentários para este paciente

      $patient = Patient::get_from_id($_POST['id']);
      $patient->delete($_POST['id']);
      Helper::make_template('staging');
      break;

    default:
      exit();
      break;

    Helper::update_list('patients');
  }
}
else {
  throw new Exception("Metodo de acesso inválido");
}
?>
