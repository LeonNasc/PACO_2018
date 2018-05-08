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

    private function __construct(){

    }

    public function set_method($method) {$this->method = $method;}
    
    public function get_method() {return $this->method;}
    
    /**
     * Verifica se existe uma instância de Controller ativa e a cria, caso não exista
     *
     * @return Controller
     */
    public static function get_instance() {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Mostra a página de erro quando solicitado
     *
     * @return Controller
     */
    public function show_404(){
        Helper::make_template('404');
        return false;
    }
    
    /**
     *
     * Monta os formulários para as ações de usuário: Registro, Login, Edição, Remoção e Recuperação
     * 
     */
    public function render_user_forms($params){
        
        $task = $params['task'];
        
        switch ($task) {

            case 'registro':
                Helper::make_template('registro', null, false);
                break;

            case 'editar':
                Helper::make_template('profile', null, false);
                break;

            case 'recuperar':
                Helper::make_template('email_reset_prompt', null, false);
                break;

            case 'logout':
                User::logout();
                Helper::make_template('staging', null, false);
                break;
        }
    }


    /**
     *
     * Controla as ações de usuário: Registro, Login, Edição, Remoção e Recuperação
     * 
     */
    public function control_user_actions($params) {

        $task = $params['task'];

        switch ($task) {
            
            case 'registrar':
                
                UserController::register_user($params);
                
                break;

            case 'login':
                
                UserController::log_user($params['login'], $params['senha']);
            
                break;

            case 'edit':
                
                UserController::edit_user(User::get_active_user_id(), $params);
                
                break;

            case 'recuperar':

                UserController::restore_user($params['email']);

                break;

            case 'delete':

                UserController::delete_user(User::get_active_user_id());
                
                break;
            
            case 'collide':

                if(isset($params['login'])){
                    $user_info = $params['login'];
                }
                else if(isset($params['email'])){
                    $user_info = $params['email'];
                }
                else{
                    echo "Nenhum parametro de usuário passado para colisão";
                    exit();
                }
                
                /* A função validate(scripts.js) verifica se existe um nome de usuario ou email em uso no banco de dados.
                Ela usa um JSON na resposta XHR, então aqui temos de converter o array em json */
                $printable_JSON = json_encode(array('exists' => UserController::collide_user($user_info)));
                
                print($printable_JSON);
                
                break;

            default:
                exit();
                break;
        }
    
    }

    /**
     *
     *
     * Documentar ações de paciente
     *
     *
     */
    public function control_patient_actions($params){

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
                
                case 'set_active':
                    
                    $_SESSION['active_patient'] = $params['id'];
                    
                    echo PatientController::render_patient_info($_SESSION['active_patient']);
                    
                    break;
                
                case 'add':
                    $patient_info = &$params;
                    $patient_info['owner'] = User::get_active_user_id();

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

                    PatientController::edit_patient($params['id'],$params);
                    echo PatientController::render_patient_info($params['id']);
                                    
                    break;

                case 'change_status':

                    PatientController::change_patient_status($params['id']);
                    echo PatientController::render_patient_info($params['id']);
                    break;

                case 'delete':
                    
                    PatientController::delete_patient($params['id']);
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
                    $params['task'] = 'add_pre';
                    print(Helper::make_template('prescriptions_form', $params, true));
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
                print(Helper::make_template("results_form", array('task' => 'edit_data', 'patient_data_id' => $params['patient_data_id']), true));
                    break;
                case 'edit_com':
                    print(Helper::make_template("comment_form", array('task' => 'edit_data', 'patient_data_id' => $params['patient_data_id']), true));
                    break;
            }
        } else if ($this->method == 'POST') {

            if (!isset($params['patient_data_id'])) {
                $ptt_data = array();
                $ptt_data['author'] = User::get_active_user_id();
                $ptt_data['patient'] = $params['patient'];
                $ptt_data['subject'] = $params['subject'];
                $ptt_data['content'] = $params['content'];
            } else {
                $patient_data = PatientData::get_from_id($params['patient_data_id']);
            }

            switch ($params['task']) {

                case 'add_pre':
                    $source = 'show_prescriptions';
                    $prescription = new PatientData($ptt_data, PatientData::PRESCRIPTION);
                    $prescription->add();
                    break;

                case 'add_res':
                    $source = 'show_results';
                    $result = new PatientData($ptt_data, PatientData::LAB_RESULT);                    
                    $result->add();
                    break;

                case 'add_com':
                    $source = 'show_comments'; 
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
            print(Helper::make_template($source, array('patient' => Patient::get_from_id($_SESSION['active_patient'])->get_patient_data()), true));
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
