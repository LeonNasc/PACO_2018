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
    $this->status = isset($patient_info['status'])?$patient_info['status'] : 1;

    $this->table_name = Patient::TABLE_NAME;
    $this->configura_DB();

    return $this;
  }

  //Getters e setters
  private function set_name($name){$this->name = $name;}

  private function set_birth($date){$this->birth = $date;}

  private function set_gender($sex){$this->sex = $sex;}

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

  public function change_info($new_info){

    $this->set_name($new_info['name']);
    $this->set_birth($new_info['birth']);
    $this->set_gender($new_info['sex']);

  }

  public function get_patient_data(){

    return json_encode($this->get_fields());

  }

  public static function get_patient_list($owner){
    $db = new DBObj(Patient::TABLE_NAME);

    $patient_list = $db->fetch(array('owner'=>$owner));

    return json_encode($patient_list,JSON_PRETTY_PRINT);
  }

  //Funções de cadastro

  public function add_patient(){

    return $this->set($this->get_fields());

  }
  public function edit_patient($name, $birth = null){

    $result = isset($name)? $this->set_name($name) : false;
    $result = isset($birth)? $this->set_birth($birth):false;

    return $this->update_patient_info();
  }

  public function update_patient_info(){
    $result = $this->update($this->get_fields());

    return $result;
  }

  public static function get_from_id($id){
    $db= new DBOBj(Patient::TABLE_NAME);

    $result = $db->fetch(array('id'=>$id));

    if($result && sizeof($result) > 0){
      $ptt = new Patient($result[0]);
      return $ptt;
    }
    else
      return False;
  }

  public function delete($id){
    DBObj::delete($id);
  }
  //Funções utilitárias

  private function get_fields(){

    $patient_data = array();
    $patient_data['id'] = $this->id;
    $patient_data['name'] = $this->name;
    $patient_data['birth'] = $this->birth;
    $patient_data['sex'] = $this->sex;
    $patient_data['admission_date'] = $this->admission_date;
    $patient_data['owner'] = $this->owner;
    $patient_data['status'] = $this->status ? 1 : 0;

    return $patient_data;
  }

}


?>
