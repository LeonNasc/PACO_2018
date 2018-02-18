<?PHP
require("../config/config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET'){
  // Deve devolver um JSON com a lista de pacientes do usuário logado
  switch($_GET['task']){
    case 'novo':
      Helper::make_template('patient_form',array('task'=>'adicionar'));
      break;
    case 'editar':
      Helper::make_template('patient_form',array('task'=>'editar'));
      break;
    default:
      echo "Hello World";
      $list = Patient::get_patient_list($_SESSION['active_user_id']['id']);
      var_dump(json_decode($list,true));
      break;
  }
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  //Deve lidar com as funções ADD, DELETE e UPDATE paciente
  switch($_POST['task']){
    case 'adicionar':
      $patient['name'] = $_POST['nome'];
      $patient['birth'] = $_POST['nascimento'];
      $patient['sex'] = $_POST['sexo'];
      $patient['owner'] = $_SESSION['active_user_id']['id'];

      $patient = new Patient($patient);
      print($patient->get_patient_data());
      $patient->add_patient();
      $_SESSION['patient_list'] = json_decode(Patient::get_patient_list($_SESSION['active_user_id']['id']),true);

      //header("Location: /PACO_2018/index.php");

      break;
    case 'editar':
      Helper::make_template('patient_form',array('task'=>'editar'));
      break;
    default:
    echo "Hello World";
      break;
  }
}
else {
  throw new Exception("Metodo de acesso inválido");
}
?>
