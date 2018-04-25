<?php

/**
 *
 * TODO:
 * Descrever classe
 * Modularizar funções
 *
 *
 */
class Controller
{
    /**
     * Mantém a instância atual
     *
     * @var Controller instance
     */
    private static $instance;

    /**
     * Mantém o método HTTP/HTTPS atual
     *
     * @var String
     */
    private $method;

    private function __construct()
    {

    }

    public function set_method($method)
    {$this->method = $method;}

    /**
     * Verifica se existe uma instância de Controller ativa e a cria, caso não exista
     *
     * @return Controller
     */
    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function show_404()
    {
        Helper::make_template('404');
    }

    /**
     *
     *
     * Documentar ações de usuário
     *
     *
     */
    public function control_user_actions($params)
    {

        //Separa a tarefa em curso das demais ações
        $task = $params['task'];

        if ($this->method == 'GET') {

            switch ($task) {

                //Exibe o template com o form para registro de usuário
                case 'registro':
                    Helper::make_template('registro', null, false);
                    break;

                //Exibe o template com o form para edição de usuário
                case 'editar':
                    Helper::make_template('profile', null, false);
                    break;

                //Exibe o template com o form para recuperação de senha de usuário
                case 'recuperar':
                    Helper::make_template('email_reset_prompt', null, false);
                    break;

                //Desloga o usuário logado
                case 'logout':
                    User::logout();
                    Helper::make_template('staging', null, false);
                    break;
            }
        } else { //($method == 'POST')

            switch ($task) {

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
                    $user_data['user_name'] = $params['nome'];
                    $user_data['email'] = $params['email'];
                    $user_data['login'] = $params['login'];
                    $user_data['password'] = $params['senha'];

                    $user = new User($user_data);

                    try {
                        $mailer = Mailer::get_instance();

                        $mailto = array('email' => $user_data['email'], 'name' => $user_data['user_name']);
                        $content = Helper::make_template("new_user", $user_data, true);

                        echo $content;
                        $mailer->write("PACO - Bem vindo!", $mailto, $content);
                        if ($mailer->send()) {
                            $user->add_user();
                            User::login($params['login'], $params['senha']);
                        }

                    } catch (Exception $e) {
                        Helper::make_template("error_page", array("message" => $e->getMessage()), true);
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
                    try {
                        User::login($params['login'], $params['senha']);
                    } catch (Exception $e) {
                        Helper::make_template("error_page", array("message" => $e->getMessage()), false);
                        exit();
                    }
                    Helper::make_template("staging", null, false);
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

                    $new_info['login'] = isset($params['login']) ? $params['login'] : null;
                    $new_info['password'] = isset($params['senha']) ? $params['senha'] : null;
                    $new_info['email'] = isset($params['email']) ? $params['email'] : null;

                    try {
                        $user = User::get_from_id(array('id' => $_SESSION['active_user_id']['id']));
                        $user->update_user_info($new_info);
                        $_SESSION['active_user_id'] = json_decode($user->get_user_data(), true);
                    } catch (Exception $e) {
                        Helper::make_template('error_page', array('message' => $e->getMessage()));
                    }
                    Helper::make_template('profile', null, false);
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

                    $user = User::get_from_id(array('email' => strtolower($params['email'])));

                    if ($user) {
                        //Gera uma nova senha temporaria
                        $temp['password'] = Helper::random_password();
                        $user->update_user_info($temp);
                        $user = json_decode($user->get_user_data(), true);
                        $user['temp_pass'] = $temp['password'];

                        //Montando o e-mail
                        $mail = Mailer::get_instance();
                        $subject = 'PACO - Recuperação de senha';
                        $content = Helper::make_template('acc_reset', $user, true);

                        $mail->write($subject, array('email' => $user['email'], 'name' => $user['user_name']), $content);
                        $mail->send();
                        Helper::make_template('email_sent', array('email' => $user['email']), false);
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
                    $user = User::get_from_id(array('id' => $_SESSION['active_user_id']['id']));
                    $user->delete($_SESSION['active_user_id']['id']);
                    User::logout();
                    Helper::make_template('staging', null, false);
                    break;

                /*
                 * Caso 5: Uma função de front-end verifica se é não existe um usuário igual
                 * no banco de dados
                 *
                 * Devolve ao usuário à página inicial, deslogado.
                 */
                case 'collide':
                    if (isset($params['login']) && User::user_exists($params['login'], 'login')) {
                        print(json_encode(array('exists' => true)));
                    } else if (isset($params['email']) && User::user_exists($params['email'], 'email')) {
                        print(json_encode(array('exists' => true)));
                    } else {
                        print(json_encode(array('exists' => false)));
                    }

                    break;

                default:
                    exit();
                    break;
            }
        }
    }

    /**
     *
     *
     * Documentar ações de paciente
     *
     *
     */
    public function control_patient_actions($params)
    {

        $task = $params['task'];

        if ($this->method == 'GET') {
            switch ($task) {

                case 'add':
                    Helper::make_template('patient_form', array('task' => 'add'));
                    break;

                case 'edit':
                    $patient = Patient::get_from_id($params['id']);
                    $patient = $patient->get_patient_data();
                    Helper::make_template('patient_form', array('task' => 'edit', 'patient' => $patient));
                    break;

                default:
                    Helper::update_list('patients');
                    break;
            }
        } else if ($this->method == 'POST') {
            //Deve lidar com as funções ADD, DELETE e UPDATE paciente

            switch ($params['task']) {

                case 'add':
                    $patient = &$params;
                    $patient['owner'] = $_SESSION['active_user_id']['id'];

                    $patient = new Patient($patient);
                    $patient->add_patient();
                    $patient = $patient->get_patient_data();

                    $_SESSION['active_patient'] = $patient['id'];
                    //$_SESSION['last_seen'].push($patient['id']);

                    //Bug bizonho da primeira exibição
                    $patient['sex'] = !$patient['sex'];
                    $patient['status'] = !$patient['status'];

                    Helper::make_template('patient_info', array('patient' => $patient), false);
                    exit();

                    break;

                case 'edit':

                    $patient = Patient::get_from_id($params['id']);
                    $patient->change_info($params);
                    $patient->update_patient_info();

                    $patient = $patient->get_patient_data();
                    Helper::make_template('patient_info', array('patient' => $patient), false);
                    break;

                case 'get_data':

                    $patient = Patient::get_from_id($params['id']);
                    $patient = $patient->get_patient_data();
                    $_SESSION['active_patient'] = $params['id'];

                    Helper::make_template('patient_info', array('patient' => $patient), false);
                    break;

                case 'change_status':

                    $patient = Patient::get_from_id($params['id']);
                    $patient->change_status();
                    $patient->update_patient_info();
                    $patient = $patient->get_patient_data();

                    Helper::make_template('patient_info', array('patient' => $patient), false);
                    break;

                case 'delete':
                    //TODO: Deletar todas as prescrições, resultados e comentários para este paciente

                    $patient = Patient::get_from_id($params['id']);
                    $patient->delete($params['id']);
                    Helper::make_template('staging');
                    break;

                default:
                    exit();
                    break;

                    Helper::update_list('patients');
            }
        } else {
            throw new Exception("Metodo de acesso inválido");
        }
    }

    public function control_patientdata_actions($params)
    {

        $task = $params['task'];

        if ($this->method == 'GET') {

            switch ($task) {

                //Caminhos de exibição
                case 'URI_view':

                    break;
                case 'list_pre':
                    Helper::update_list('PRESCRIPTIONS');
                    print(Helper::make_template('show_prescriptions', null, true));
                    break;
                case 'list_res':
                    Helper::update_list('RESULTS');
                    print(Helper::make_template('show_results', null, true));
                    break;
                case 'list_com':
                    Helper::update_list('COMMENTS');
                    print(Helper::make_template('show_comments', null, true));
                    break;

                //Controla a exibição dos views pertinentes
                case 'add_pre':
                    print(Helper::make_template('404'));
                    break;
                case 'add_res':
                    if (isset($params['subject'])) {
                        $params['task'] = 'add_res';
                        print(Helper::make_template('results_form', $params, true));
                    } else {
                        print(Helper::make_template('results_form', array('task' => 'add_res'), true));
                    }

                    break;
                case 'add_com':
                    if (isset($params['subject'])) {
                        $params['task'] = 'add_com';
                        print(Helper::make_template('comment_form', $params, true));
                    }
                    else
                        print(Helper::make_template("comment_form", array('task' => 'add_com'), true));
                    break;

                case 'edit_pre':

                    break;
                case 'edit_res':

                    break;
                case 'edit_com':
                    print(Helper::make_template("comment_form", array('task' => 'edit_data', 'patient_data_id' => $params['patient_data_id']), true));
                    break;
            }
        } else if ($this->method == 'POST') {

            if (!isset($params['patient_data_id'])) {
                $ptt_data = array();
                $ptt_data['author'] = $_SESSION['active_user_id']['id'];
                $ptt_data['patient'] = $params['patient'];
                $ptt_data['subject'] = $params['subject'];
                $ptt_data['content'] = $params['content'];
            } else {
                $patient_data = PatientData::get_from_id($params['patient_data_id']);
            }

            switch ($params['task']) {

                case 'add_pre':
                    $prescription = new PatientData($ptt_data, PatientData::PRESCRIPTION);
                    break;

                case 'add_res':
                    $result = new PatientData($ptt_data, PatientData::LAB_RESULT);
                    
                    $result->add();
                    break;

                case 'add_com':
                    $comment = new PatientData($ptt_data, PatientData::COMMENT);

                    $comment->add();
                    break;

                case 'edit_data': //Caso geral para todos os PatientData

                    $content_info = json_decode($patient_data->to_JSON(), true);

                    switch (substr($content_info['id'], 0, 3)) {

                        case 'prs_':
                            $content = json_encode($params['prescription']);
                            break;

                        case 'res_':
                            $content = json_encode($params['results']);
                            break;

                        default:
                            $content = $params['content'];
                            break;
                    }

                    $patient_data->edit($content);
                    $patient_data->update_patient_data();

                    break;
                default:
                    break;
            }
            print(Helper::make_template('patient_info', array('patient' => Patient::get_from_id($_SESSION['active_patient'])->get_patient_data()), true));
        } else {
            throw new Exception("Método de acesso inválido");
        }

    }

    /**
     * Desabilita a função mágica clone
     *
     */
    private function __clone()
    {
        throw new Exception("Controller não pode ser clonado");
    }

}
