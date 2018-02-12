<?PHP
require("../config/config.php");

use Rain\Tpl;

if($_SERVER['REQUEST_METHOD'] == POST){
  //Deve lidar com as funções REGISTRAR, LOGIN e UPDATE usuário
  switch($_POST['task']){
    
    /* ---------------
    *
    *   Registro
    *
    *-----------------*/
    case 'registrar':
      
      $user_data = array();
      $user_data['user_name'] = $_POST['nome'];
      $user_data['email'] = $_POST['email'];
      $user_data['login'] = $_POST['login'];
      $user_data['password'] = $_POST['senha'];

      $user = new User($user_data);
      
      try{
        $user->add_user();
        $user->login($_POST['login'],$_POST['senha']);
        Helper::show_landing();
      }
      catch(Exception $e){
        Helper::show_error_page($e->getMessage());
      }
      
    break;
    
    /* ---------------
    *
    *   Login
    *
    *-----------------*/
    case 'login':
      try{
        User::login($_POST['login'],$_POST['senha']);
        Helper::show_landing();
      }
      catch(Exception $e){
        Helper::show_error_page($e->getMessage());
      }
      
    break;
     /* ---------------
    *
    *   Logout
    *
    *-----------------*/
    case 'logout':
      
      User::logout();
      Helper::show_landing();
      
    break;
    default:
      //DEFAULT ACTION
    break;
  }
}
?>
