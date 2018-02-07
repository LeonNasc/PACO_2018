<?PHP
use Rain\Tpl;

if ($_SERVER['method'] == GET){
  $task = $_GET['task'];
  echo $task
  switch($task){
    case 'registrar':

    echo "AEHOO";
    break;
    case 'login':
    //TODO
    break;
    default:
    //TODO: redirecionar
    echo "AEHOO!";
    break;
  }
}
else if ($_SERVER['method'] == POST){
  //Deve lidar com as funções LOGIN, DELETE e UPDATE usuário
}
else {
  throw new Exception("Metodo de acesso inválido");
}

/* --------------------- Here be functions ----------------------*/

?>
