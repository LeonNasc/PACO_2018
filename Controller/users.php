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
      Helper::make_template("manutencao",null,true);
     break;

    case 'logout':
      User::logout();
      Helper::make_template("landing",null,true);
      break;

    case 'collide':
      if(User::user_exists($_POST['login']))
        print("");
      else if(User::user_exists($_POST['login']))
        print("");
      else
        print("joinha");
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
    case 'login':
    unset($_SESSION['active_user_id']);
    Helper::make_template('manutencao', null, false);
      break;
   }
  }
}
?>
