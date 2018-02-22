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
        Helper::make_template("error_page", array("message" => $e->getMessage()),false);
        exit();
      }
      Helper::make_template("staging",null,false);
     break;

    case 'collide':
      if(isset($_POST['login']) && !User::user_exists($_POST['login'],'login'))
        print("Nome de usuário válido");
      else if(isset($_POST['email']) && !User::user_exists($_POST['email'],'email'))
        print("E-mail válido");
      else
        print("");
      break;

    case 'recuperar':
      //Montando o e-mail
      $mail = Mailer::get_instance();
      $subject = utf8_encode('PACO - Recuperação de senha');

      //Recuperar o usuário a partir do e-mail
      $user = User::get_from_id(array('email'=>$_POST['email']));

      //Gera uma nova senha temporaria e atualiza BD pra essa senha nova
      $new_pw = Helper::random_password();
      $user->update_user_info(null, $new_pw);

      //Prepara os dados do paciente para envio
      $user = json_decode($user->get_user_data(),true);
      $content = Helper::make_template('email_pw', $data, true);

      $mail->write($subject, array('email'=>$user['email'],'name'=>$user['user_name']),$content);
      $mail->send();
      break;

    case 'edit':

      $new_login = isset($_POST['login'])? $_POST['login']: null;
      $new_password = isset($_POST['senha'])? $_POST['senha']: null;
      $new_email = isset($_POST['email'])? $_POST['email']: null;

      try{
        $user = User::get_from_id(array('id'=>$_SESSION['active_user_id']['id']));
        $user->update_user_info($new_login,$new_password,$new_email);
        $_SESSION['active_user_id'] = json_decode($user->get_user_data(),true);
      }
      catch(Exception $e){
        Helper::make_template('error_page', array('message'=> $e->getMessage()));
      }
      Helper::make_template('profile',null,false);
      break;

    case 'delete':
      $user = User::get_from_id(array('id'=>$_SESSION['active_user_id']['id']));
      $user->delete($_SESSION['active_user_id']['id']);
      User::logout();
      Helper::make_template('staging',null,false);
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

    case 'editar':
      Helper::make_template('profile',null,false);
      break;

    case 'logout':
      User::logout();
      Helper::make_template('staging',null,false);
      break;

    case 'recuperar':
      Helper::make_template('email_reset_prompt',null, false);
      break;
   }
  }
}
?>
