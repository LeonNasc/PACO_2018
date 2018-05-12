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
     * Tarefa atual
     */
    private $task = "";

    /**
     * Mantém o método HTTP/HTTPS atual
     *
     * @var String
     */
    private $method;

    /**
     * 
     * Armazena os dados de operação
     * 
     */
    private $dados;

    private function __construct(){
        //Restrito por padrão
    }

    public function set_method($method) {$this->method = $method;}
    
    public function get_method() {return $this->method;}
    
    public function set_task($task) {$this->task = $task;}

    public function get_task() { return $this->task;}

    public function set_dados(Array $dados) {$this->dados = $dados;}

    public function get_dados() { return $this->dados;}

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
     * 
     * 
     */
    public function handle_forms($actor){
        switch($actor){
            case 'user':
              $this->render_user_forms();
            break;
            case 'patient':
              $this->render_patient_forms($this->get_dados());
            break;
            case 'patient_data':
              $this->control_patientdata_actions($this->get_dados());
            break;
        }
    }
    /**
     * 
     * 
     */
    public function handle_data($actor){
        switch($actor){
            case 'user':
              $this->control_user_actions($this->get_dados());
            break;
            case 'patient':
              $this->control_patient_actions($this->get_dados());
            break;
            case 'patient_data':
              $this->control_patientdata_actions($this->get_dados());
            break;
        }
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
    public function render_user_forms(){
         
        switch ($this->task) {

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
    public function control_user_actions($dados) {

        switch ($this->task) {
            
            case 'registrar':
                
                UserController::register_user($dados);
                
                break;

            case 'login':
                
                UserController::log_user($dados['login'], $dados['senha']);
            
                break;

            case 'edit':
                
                UserController::edit_user(UserController::get_active_user_id(), $dados);
                
                break;

            case 'recuperar':

                UserController::restore_user($dados['email']);

                break;

            case 'delete':

                UserController::delete_user(UserController::get_active_user_id());
                
                break;
            
            case 'collide':

                if(isset($params['login'])){
                    $user_info = $dados['login'];
                }
                else if(isset($params['email'])){
                    $user_info = $dados['email'];
                }
                else{
                    //Nenhum parametro de usuário passado para colisão
                    return false;
                }
                
                /* A função validate(scripts.js) verifica se existe um nome de usuario ou email em uso no banco de dados.
                Ela usa um JSON na resposta XHR, então aqui temos de converter o array em json */
                $printable_JSON = json_encode(array('exists' => UserController::collide_user($user_info)));
                
                print($printable_JSON);
                
                break;

            default:
                return new Exception("Ação inválida");
                break;
        }
    
    }

    /**
     *
     * Monta os formulários para as ações de paciente: Adicionar e editar dados
     * 
     */
    public function render_patient_forms(){
        switch ($this->task) {

            case 'add':
                Helper::make_template('patient_form', array('task' => 'add'));
                break;

            case 'edit':
                $patient = Patient::get_from_id($this->get_dados()['id'])->get_patient_data();
                $patient_info = Array('task' => 'edit', 'patient' => $patient);
                Helper::make_template('patient_form', $patient_info);
                break;

            default:
                return new Exception("Ação inválida");
                break;
        }
    }

    /**
     * Controla as açoes de paciente
     * Lida com as funcionalidades ADD, DELETE e UPDATE dos pacientes
     */
    public function control_patient_actions($dados){   

        switch ($this->task) {
            
            case 'set_active':
                PatientController::set_active_patient(Patient::get_from_id($dados['id']));                
                echo PatientController::render_patient_info(PatientController::get_active_patient());
                
                break;
            
            case 'add':

                $patient = PatientController::add_user($this->get_dados());
                echo PatientController::render_patient_info(PatientController::get_active_patient());

                break;

            case 'edit':
                PatientController::edit_patient($dados['id'],$dados);                 
                echo PatientController::render_patient_info(PatientController::get_active_patient());
                break;

            case 'change_status':
                PatientController::change_patient_status($dados['id']);
                echo PatientController::render_patient_info(PatientController::get_active_patient());
                break;

            case 'delete':
                PatientController::delete_patient($dados['id']);
                Helper::make_template('staging');
                break;

            default:
                throw new Exception("Ação inválida");
                break;
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
                $ptt_data['author'] = UserController::get_active_user_id();
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

    public static function get_stats($user){
        $stats = Array();
        $stats['recente'] = PatientController::get_active_patient()->get_patient_data()['name'];

        return $stats;
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
