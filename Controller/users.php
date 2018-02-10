<?PHP
require("../config/config.php");

if($_SERVER['REQUEST_METHOD'] == 'GET'){

  $task = $_GET['task'];
  
  switch($task){
    case 'registrar':
      $user_data = array();
      $user_data['user_name'] = $_GET['nome'];
      $user_data['email'] = $_GET['email'];
      $user_data['login'] = $_GET['login'];
      $user_data['password'] = $_GET['senha'];

    $user = new User($user_data);

    echo ("<pre>". $user->get_user_data(true) . "</pre>");
    break;
    case 'login':
    //TODO
    break;
    default:
    //TODO: redirecionar

    break;
  }
}
else if($_SERVER['REQUEST_METHOD'] == POST){
  //Deve lidar com as funções LOGIN, DELETE e UPDATE usuário
}
else {
  throw new Exception("Metodo de acesso inválido");
}

/* --------------------- Here be functions ----------------------*/

?>
