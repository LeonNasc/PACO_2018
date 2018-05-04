<?php

class UserController {

    private $active_user, $login_status;

    // //Controla as ações de usuário: Registro, Login, Edição, Remoção e Recuperação

    //             /*
    //              * Caso 1: Novo usuário se registra.
    //              *
    //              * A partir da form passada, cria um objeto usuário novo e o insere no banco
    //              * de dados.
    //              *
    //              * Devolve ao usuário um template apresentando o usuário ao sistema após
    //              * realizar seu login.
    //              *
    //              * Em caso de erro, apresenta uma página de erro genérica.
    //              */
    //             case 'registrar':

    //                 $user_data = array();
    //                 $user_data['user_name'] = $params['nome'];
    //                 $user_data['email'] = $params['email'];
    //                 $user_data['login'] = $params['login'];
    //                 $user_data['password'] = $params['senha'];

    //                 $user = new User($user_data);

    //                 try {
    //                     $mailer = Mailer::get_instance();

    //                     $mailto = array('email' => $user_data['email'], 'name' => $user_data['user_name']);
    //                     $content = Helper::make_template("new_user", $user_data, true);

    //                     echo $content;
    //                     $mailer->write("PACO - Bem vindo!", $mailto, $content);
    //                     if ($mailer->send()) {
    //                         $user->add_user();
    //                         User::login($params['login'], $params['senha']);
    //                     }

    //                 } catch (Exception $e) {
    //                     Helper::make_template("error_page", array("message" => $e->getMessage()), true);
    //                     exit();
    //                 }
    //                 break;

    //             /*
    //              * Caso 2: Um usuário tenta fazer login.
    //              *
    //              * A partir do login e senha passados, verifica-se se existe um usuário
    //              * cadastrado e se a combinação login/senha é valida.
    //              *
    //              * Devolve ao usuário um template que o direciona para o dashboard, se OK.
    //              *
    //              * Em caso de erro, apresenta uma página de erro relatando o erro encontrado.
    //              */
    //             case 'login':
    //                 try {
    //                     User::login($params['login'], $params['senha']);
    //                 } catch (Exception $e) {
    //                     Helper::make_template("error_page", array("message" => $e->getMessage()), false);
    //                     exit();
    //                 }
    //                 Helper::make_template("staging", null, false);
    //                 break;

    //             /*
    //              * Caso 3: Um usuário deseja editar suas informações
    //              *
    //              * O usuário pode desejar mudar seu login, senha ou e-mail a partir da form
    //              * associada.
    //              *
    //              * Devolve ao usuário um template com sua página de perfil, se OK.
    //              *
    //              * Em caso de erro, apresenta uma página de erro relatando o erro encontrado.
    //              */
    //             case 'edit':

    //                 $new_info['login'] = isset($params['login']) ? $params['login'] : null;
    //                 $new_info['password'] = isset($params['senha']) ? $params['senha'] : null;
    //                 $new_info['email'] = isset($params['email']) ? $params['email'] : null;

    //                 try {
    //                     $user = User::get_from_id(array('id' => $_SESSION['active_user_id']['id']));
    //                     $user->update_user_info($new_info);
    //                     $_SESSION['active_user_id'] = $user->get_user_data();
    //                 } catch (Exception $e) {
    //                     Helper::make_template('error_page', array('message' => $e->getMessage()));
    //                 }
    //                 Helper::make_template('profile', null, false);
    //                 break;

        /**
        * A partir do e-mail passado, caso exista um usuário cadastrado, envia um
        * email para este usuário com uma senha temporária, que deverá ser alterada
        * após o acesso ao sistema.
        *
        * Devolve ao usuário um template confirmando o envio do email, se OK.
        *
        * Em caso de erro, apresenta uma página de erro relatando o erro encontrado.
        */
        public static function restore_user($user_mail){

            $user = User::get_from_id(array('email' => strtolower($user_mail)));

            if ($user) {
                //Gera uma nova senha temporaria
                $temp['password'] = Helper::make_random_password();
                $user->update_user_info($temp);
                $user = $user->get_user_data();
                $user['temp_pass'] = $temp['password'];

                //Montando o e-mail
                $mail = Mailer::get_instance();
                $subject = 'PACO - Recuperação de senha';
                $content = Helper::make_template('acc_reset', $user, true);

                $mail->write($subject, array('email' => $user['email'], 'name' => $user['user_name']), $content);
                $mail->send();

                Helper::make_template('email_sent', array('email' => $user['email']), false);
            }
        }
        
        /**
        * A partir do id de usuário, deleta e remove o usuário do BD
        *
        * Devolve ao usuário à página inicial, deslogado.
        */
        public static function delete_user($id){

            $user = User::get_from_id(array('id' => $id));
            $user->delete($id);
            Helper::make_template('staging', null, false);
        }

        /**
        * Verifica se já existe um nome de usuário ou email no banco de dados
        *
        * @param String $user_info: nome ou email de usuário
        *
        * @return boolean $collided;
        */
        public static function collide_user($user_info){

            $collided = User::collide($user_info, 'login') || User::collide($user_info, 'email'); 

            return $collided;
        }

}

?>