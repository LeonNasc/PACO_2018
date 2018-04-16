<?php
Class Patient extends DBObj{

  private $id, $name, $birth, $sex, $admission_date, $owner, $status;

  const TABLE_NAME = "patients_db";

  public function __construct($patient_info){

    if (!is_array($patient_info))
      return new Exception("Parâmetro inválido: \$patient_info deve ser array");

    $now = new DateTime();

    $this->id = isset($patient_info['id']) ? $patient_info['id'] : uniqid('ptt_');
    $this->name = $patient_info['name'];
    $this->birth = $patient_info['birth'];
    $this->sex = $patient_info['sex'];
    $this->admission_date = isset($patient_info['admission_date'])?
      $patient_info['admission_date'] : $now->format("d/m/Y");
    $this->owner = $patient_info['owner'];
    $this->status = isset($patient_info['status'])?$patient_info['status'] : true;

    $this->table_name = Patient::TABLE_NAME;
    $this->configura_DB();

    return $this;
  }

  //Getters e setters
  private function set_name($name){$this->name = $name;}

  private function set_birth($date){$this->birth = $date;}

  private function set_gender($sex){$this->sex = $sex;}


  /**
   * Altera o status de acompanhamento de um paciente
   * 
   * Se ativo, torna-se inativo. Se inativo, torna-se ativo.
   * 
   * @return null
   */
  public function change_status(){
    $this->status = !$this->status;
    
    /*Obs.: Quando PDO transforma o status para passar no statement,
    ele transforma false em ''. Por isso, uso um ternario em get_fields
    pra converter os valores booleanos nas strings 'TRUE' e 'FALSE' */

    if($this->status){
      $now = new DateTime();
      $this->admission_date = $now->format("d/m/Y");
    }
  }

  /**
   * Setter para dados de paciente
   * 
   * @param $new_info: array com dados do paciente
   * 
   * @return null
   */
  public function change_info($new_info){

    $this->set_name($new_info['name']);
    $this->set_birth($new_info['birth']);
    $this->set_gender($new_info['sex']);

  }

  /**
   * Retorna um array contendo dados do paciente
   * 
   * @return array;
   */
  public function get_patient_data(){

    return $this->get_fields();

  }
  /**
   * Obtém uma lista atualizada de pacientes para um usuário
   * 
   * 
   * @param $owner: lista pacientes para um User
   * @param $json: define se o formato de retorno será em JSON
   * 
   * @return $array ou String(JSON) com dados do paciente
   * 
   */
  public static function get_patient_list($owner, $json = false){
    $db = new DBObj(Patient::TABLE_NAME);

    $patient_list = $db->fetch(array('owner'=>$owner));

    if($json)
      return json_encode($patient_list,JSON_PRETTY_PRINT);

    return $patient_list;
  }

  /* ------------------------- Funções de cadastro ---------------------------- */

  /**
   * Inclui paciente ativo no banco de dados
   * 
   * 
   * 
   */
  public function add_patient(){

    return $this->set($this->get_fields());

  }

  /**
   * Ediata os dados do paciente em questão. 
   * 
   * @param $name: Novo nome do paciente
   * @param $birth: Nova data de nascimento do paciente
   * 
   */
  public function edit_patient($name = null, $birth = null){

    $result = isset($name)? $this->set_name($name) : false;
    $result = isset($birth)? $this->set_birth($birth):false;

    return $this->update_patient_info();
  }

  /**
   * Atualiza dados do paciente no banco de dados
   * 
   * @return boolean
   * 
   */
  public function update_patient_info(){
    $result = $this->update($this->get_fields());

    return $result;
  }

  /**
   * Deleta o registro de um paciente
   * 
   * Como isso envolve o constraint Patient_must_exist da tabela de patient_data, 
   * delete deve remover todos os items referentes ao paciente a ser deletado na tabela
   * referente a patient_data
   * 
   * @param $id : id do paciente a ser deletado
   * 
   * @return null
   */
  public function delete($id = null){
    //Deleta todos os dados de paciente do banco.
    $patient_data_list = PatientData::get_for_patient($id);

    foreach($patient_data_list as $patient_data){
      $patient_data = PatientData::get_from_id($patient_data['id']);
      $patient_data->delete();
    }

    DBObj::delete($id);
  }
  /*---------------------------- Funções utilitárias ----------------------------------*/

  /**
   * Reúne todos os campos do objeto como um array a ser utilizado
   * 
   * @return array $patient_data: dados do paciente 
   */ 
   private function get_fields(){

    $patient_data = array();
    $patient_data['id'] = $this->id;
    $patient_data['name'] = $this->name;
    $patient_data['birth'] = $this->birth;
    $patient_data['sex'] = $this->sex;
    $patient_data['admission_date'] = $this->admission_date;
    $patient_data['owner'] = $this->owner;
    $patient_data['status'] = $this->status ? true : false;

    return $patient_data;
  }

}


?>
