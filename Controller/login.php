<?PHP
use Rain\Tpl;

if ($_SERVER['method'] == GET){
  $task = $_GET['task'];

  switch($task){
    case 'registrar':
    //TODO
    break;
    case 'login':
    //TODO
    break;
    case default:
    //TODO: redirecionar
    break;
  }
  $view = new Tpl;
  $view->assign('name', isset($_SESSION['logged_user_name']));
  //$login =

}
else if ($_SERVER['method'] == POST){
  //Deve lidar com as funções LOGIN, DELETE e UPDATE usuário
}
else {
  throw new Exception("Metodo de acesso inválido");
}

/* --------------------- Here be functions ----------------------*/

?>
