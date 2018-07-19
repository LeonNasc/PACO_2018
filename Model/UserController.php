<?php

//Controla as ações de usuário: Registro, Login, Edição, Remoção e Recuperação
class UserController
{

    /**
     * A partir dos dados passados, cria um objeto usuário novo e o insere no banco
     * de dados.
     *
     * Devolve ao usuário um template apresentando o usuário ao sistema após
     * realizar seu login.
     *
     * Em caso de erro, apresenta uma página de erro genérica.
     */
    public static function register_user($user_info)
    {

        $user = User::make_new_from_form($user_info);
        $user_data = $user->get_user_data();

        try {
            $mailer = Mailer::get_instance();

            $mailto = array('email' => $user_data['email'], 'name' => $user_data['user_name']);
            $content = Helper::make_template("new_user", $user_data, true);
            $mailer->write("PACO - Bem vindo!", $mailto, $content);

            echo $content;
            if ($mailer->send()) {
                $user->add_user();
                User::login($params['login'], $params['senha']);
            }

        } catch (Exception $e) {
            Helper::make_template("error_page", array("message" => $e->getMessage()), true);
            exit();
        }
    }

    /**
     * A partir do login e senha passados, verifica-se se existe um usuário
     * cadastrado e se a combinação login/senha é valida.
     *
     * Devolve ao usuário um template que o direciona para o dashboard, se OK.
     *
     * Em caso de erro, apresenta uma página de erro relatando o erro encontrado.
     */
    public static function log_user($login, $senha)
    {
        try {
            $login_status = User::login($login, $senha);

        } catch (Exception $e) {
            Helper::make_template("error_page", array("message" => $e->getMessage()), false);
            return false;
        }

        if ($login_status) {
            Helper::make_template("staging", null, false);
        }
    }

    /**
     * Edita informações de usuário
     *
     * O usuário pode desejar mudar seu login, senha ou e-mail a partir da form
     * associada.
     *
     * Devolve ao usuário um template com sua página de perfil, se OK.
     */
    public static function edit_user($user_id, array $user_info)
    {

        $user = User::get_from_id($user_id);

        foreach ($user_info as $type => $value) {

            switch ($type) {
                case 'login':
                    $user->set_login($value);
                    break;
                case 'email':
                    $user->set_email($value);
                    break;
                case 'senha':
                    $user->set_password($value);
                    break;
                default:
                    //Faz nada
                    break;
            }
        }

        $user->update_user_info($user_info);
        UserController::set_active_user($user);
        Helper::make_template('profile', null, false);

    }

    /**
     * A partir do e-mail passado, caso exista um usuário cadastrado, envia um
     * email para este usuário com uma senha temporária, que deverá ser alterada
     * após o acesso ao sistema.
     *
     * Devolve ao usuário um template confirmando o envio do email, se OK.
     *
     * Em caso de erro, apresenta uma página de erro relatando o erro encontrado.
     */
    public static function restore_user(string $user_mail)
    {

        $user = User::get_from_id(array('email' => strtolower($user_mail)));

        if ($user) {
            //Gera uma nova senha temporaria
            $user->update_user_info(array('password' => Helper::make_random_password()));
            $user_data = $user->get_user_data();

            //Montando o e-mail
            $mail = Mailer::get_instance();
            $subject = 'PACO - Recuperação de senha';
            $content = Helper::make_template('acc_reset', $user_data, true);

            $mail->write($subject, array('email' => $user_data['email'], 'name' => $user_data['user_name']), $content);
            $mail->send();

            Helper::make_template('email_sent', array('email' => $user['email']), false);
        }
    }

    /**
     * A partir do id de usuário, deleta e remove o usuário do BD
     *
     * Devolve ao usuário à página inicial, deslogado.
     */
    public static function delete_user($id)
    {

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
    public static function collide_user(array $user_info)
    {

        $collided = User::collide($user_info, 'login') || User::collide($user_info, 'email');

        return $collided;
    }

    /*------------------ Funções de sessão --------------*/
    /**
     * Coloca o usuário atual como usuário ativo
     */
    public static function set_active_user(User $user)
    {

        $_SESSION['active_user'] = $user->get_user_data();

    }
    /**
     * Retorna o id do usuário ativo
     */
    public static function get_active_user_id()
    {   
        if($_SESSION['active_user']){
            return $_SESSION['active_user']['id'];
        }
    }
}
