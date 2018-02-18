<?PHP
require("../config/config.php");

use Rain\Tpl;

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  
  switch($_POST['task']){

    case 'registrar':

      $user_data = array();
      $user_data['user_name'] = $_POST['nome'];
      $user_data['email'] = $_POST['email'];
      $user_data['login'] = $_POST['login'];
      $user_data['password'] = $_POST['senha'];

      $user = new User($user_data);

      try{
        $user->add_user();
        User::login($_POST['login'],$_POST['senha']);
        Helper::make_template("new_user",$user_data, true);
      }
      catch(Exception $e){
        Helper::make_template("error_page", array("message" => $e->getMessage()),true);
        exit();
      }
      break;

    case 'login':
      try{
        User::login($_POST['login'],$_POST['senha']);
      }
      catch(Exception $e){
        Helper::make_template("error_page", array("message" => $e->getMessage()),true);
        exit();
      }
      Helper::make_template("staging",null,false);
     break;

    case 'collide':
      if(isset($_POST['login']) && User::user_exists($_POST['login'],'login'))
        print("Nome de usuário em uso");
      else if(isset($_POST['email']) && User::user_exists($_POST['email'],'email'))
        print("E-mail já cadastrado");
      else
        print("");
      break;

    case 'edit':
      
      $new_login = isset($_POST['login'])? $_POST['login']: null;
      $new_password = isset($_POST['senha'])? $_POST['senha']: null;
      $new_email = isset($_POST['email'])? $_POST['email']: null;;
      
      $user = User::get_from_id($_SESSION['active_user_id']['id']);
      
      try{
        $user->update_user_info($new_login,$new_password,$new_email);
        $_SESSION['active_user_id'] = json_decode($user->get_user_data(),true);
      }
      catch(Exception $e){
        Helper::make_template('error_page', array('message'=> $e->getMessage()));
      }
      break;
      
      case 'delete':
      $user = User::get_from_id($_SESSION['active_user_id']['id']);
      $user->delete($_SESSION['active_user_id']['id']);

    default:
      exit();
      break;
  }
}
else{

  if(isset($_GET)){
   switch($_GET['task']){

    case 'registro':
      Helper::make_template('registro',null,false);
      break;

    case 'editar':
      Helper::make_template('profile',null,false);
      break;

    case 'logout':
      User::logout();
      Helper::make_template('staging',null,false);
      break;
   }
  }
}
?>
