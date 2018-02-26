<?PHP
require("../config/config.php");

use Rain\Tpl;

if($_SERVER['REQUEST_METHOD'] == 'GET'){

   switch($_GET['task']){

    //Exibe o template com o form para registro de usuário
    case 'registro':
      Helper::make_template('registro',null,false);
      break;

    //Exibe o template com o form para edição de usuário
    case 'editar':
      Helper::make_template('profile',null,false);
      break;

    //Exibe o template com o form para recuperação de senha de usuário
    case 'recuperar':
      Helper::make_template('email_reset_prompt',null, false);
      break;

    //Desloga o usuário logado
    case 'logout':
      User::logout();
      Helper::make_template('staging',null,false);
      break;
   }
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

  switch($_POST['task']){

    //Controla as ações de usuário: Registro, Login, Edição, Remoção e Recuperação

    /*
    * Caso 1: Novo usuário se registra.
    *
    * A partir da form passada, cria um objeto usuário novo e o insere no banco
    * de dados.
    *
    * Devolve ao usuário um template apresentando o usuário ao sistema após
    * realizar seu login.
    *
    * Em caso de erro, apresenta uma página de erro genérica.
    */
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

    /*
    * Caso 2: Um usuário tenta fazer login.
    *
    * A partir do login e senha passados, verifica-se se existe um usuário
    * cadastrado e se a combinação login/senha é valida.
    *
    * Devolve ao usuário um template que o direciona para o dashboard, se OK.
    *
    * Em caso de erro, apresenta uma página de erro relatando o erro encontrado.
    */
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

   /*
   * Caso 3: Um usuário deseja editar suas informações
   *
   * O usuário pode desejar mudar seu login, senha ou e-mail a partir da form
   * associada.
   *
   * Devolve ao usuário um template com sua página de perfil, se OK.
   *
   * Em caso de erro, apresenta uma página de erro relatando o erro encontrado.
   */
    case 'edit':

      $new_info['login'] = isset($_POST['login'])? $_POST['login']: null;
      $new_info['password'] = isset($_POST['senha'])? $_POST['senha']: null;
      $new_info['email'] = isset($_POST['email'])? $_POST['email']: null;

      try{
        $user = User::get_from_id(array('id'=>$_SESSION['active_user_id']['id']));
        $user->update_user_info($new_info);
        $_SESSION['active_user_id'] = json_decode($user->get_user_data(),true);
      }
      catch(Exception $e){
        Helper::make_template('error_page', array('message'=> $e->getMessage()));
      }
      Helper::make_template('profile',null,false);
      break;

    /*
    * Caso 3: Um usuário esqueceu sua senha e deseja acesso ao sistema
    *
    * A partir do e-mail passado, caso exista um usuário cadastrado, envia um
    * email para este usuário com uma senha temporária, que deverá ser alterada
    * após o acesso ao sistema.
    *
    * Devolve ao usuário um template confirmando o envio do email, se OK.
    *
    * Em caso de erro, apresenta uma página de erro relatando o erro encontrado.
    */
    case 'recuperar':
      //Recuperar o usuário a partir do e-mail passado

      $user = User::get_from_id(array('email'=>strtolower($_POST['email'])));

      if($user){
        //Gera uma nova senha temporaria
        $temp['password'] = Helper::random_password();
        $user->update_user_info($temp);
        $user = json_decode($user->get_user_data(),true);
        $user['temp_pass'] = $temp['password'];

        //Montando o e-mail
        $mail = Mailer::get_instance();
        $subject = 'PACO - Recuperação de senha';
        $content = Helper::make_template('acc_reset', $user, true);

        $mail->write($subject, array('email'=>$user['email'],'name'=>$user['user_name']),$content);
        $mail->send();
        Helper::make_template('email_sent', array('email'=> $user['email']),false);
      }
      break;

    /*
    * Caso 4: Um usuário deseja cancelar seu acesso ao sistema
    *
    * A partir do id da sessão, deleta e remove o usuário do BD
    *
    * Devolve ao usuário à página inicial, deslogado.
    */
    case 'delete':
      $user = User::get_from_id(array('id'=>$_SESSION['active_user_id']['id']));
      $user->delete($_SESSION['active_user_id']['id']);
      User::logout();
      Helper::make_template('staging',null,false);
      break;


    /*
    * Caso 4: Uma função de front-end verifica se é não existe um usuário igual
    * no banco de dados
    *
    * Devolve ao usuário à página inicial, deslogado.
    */
    case 'collide':
      if(isset($_POST['login']) && User::user_exists($_POST['login'],'login'))
        print(json_encode(array('exists'=>true)));
      else if(isset($_POST['email']) && User::user_exists($_POST['email'],'email'))
        print(json_encode(array('exists'=>true)));
      else
        print(json_encode(array('exists'=>false)));
      break;

    default:
      exit();
      break;
  }
}
?>
