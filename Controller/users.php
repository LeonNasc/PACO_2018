<?PHP
require("../config/config.php");

use Rain\Tpl;

if($_SERVER['REQUEST_METHOD'] == 'GET'){

  $task = isset($_GET['task'])? $_GET['task']:null;
  
  switch($task){
    case 'registrar':
      $user_data = array();
      $user_data['user_name'] = $_GET['nome'];
      $user_data['email'] = $_GET['email'];
      $user_data['login'] = $_GET['login'];
      $user_data['password'] = $_GET['senha'];

    $user = new User($user_data);
    try{
      $user->add_user();
    }
    catch(Exception $e){
      Helper::show_error_page($e->getMessage());
    }
    break;
    default:
      $user = User::get_from_id("us_5a7f40791e89a");
      if ($user)
        $data= json_decode($user->get_user_data());
        
      $t = new Tpl;
      //$t->assign("patient_array",a);
      //$t->assign("",a);
    break;
  }
}
else if($_SERVER['REQUEST_METHOD'] == POST){
  //Deve lidar com as funções LOGIN, DELETE e UPDATE usuário
  switch($task){
    case 'update_usuario':
      $user_data = array();
      $user_data['login'] = $_GET['novo_login'];
      $user_data['password'] = $_GET['nova_senha'];

    $user = User::get_from_id($_SESSION['active_user_id']);
    try{
      $user->update_user_info();
      //Helper::show something
    }
    catch(Exception $e){
      Helper::show_error_page($e->getMessage());
    }
    break;
    case 'login':
      User::login($_GET['login'],$_GET['senha']);
      Helper::show_landing();
    break;
    default:
      $user = User::get_from_id("us_5a7f40791e89a");
      if ($user)
        $data= json_decode($user->get_user_data());
        
      $t = new Tpl;
      //$t->assign("patient_array",a);
      //$t->assign("",a);
    break;
  }
  
  
  
}
else {
  throw new Exception("Metodo de acesso inválido");
}

/* --------------------- Here be functions ----------------------*/



?>
