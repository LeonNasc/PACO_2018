<?PHP
require("../config/config.php");

if ($_SERVER['REQUEST_METHOD'] == 'GET'){
  // Deve devolver um JSON com a lista de pacientes do usuário logado
  switch($_GET['task']){
    case 'adicionar':
      Helper::make_template('patient_form');
      break;
    default:
    echo "Hello World";
      break;
  }
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  //Deve lidar com as funções ADD, DELETE e UPDATE paciente
  echo "Hello world!2";
}
else {
  throw new Exception("Metodo de acesso inválido");
}
?>
