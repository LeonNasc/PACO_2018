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
    case 'logout':
      User::logout();
      Helper::make_template('staging',null,false);
      break;
   }
  }
}
?>
