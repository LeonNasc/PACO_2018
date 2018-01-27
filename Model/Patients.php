<?php
Class Patient extends DBOj{

  private $id, $name, $birth, $sex, $admission_date, $owner, $status;

  private const TABLE_NAME = "patients_db";

  public function __construct($patient_info){

    if (!is_array($patient_info))
      return new Exception("Parâmetro inválido: \$patient_info deve ser array");

    $this->id = $patient_info['id'] ? $patient_info['id'] : null;
    $this->name = $patient_info['name'];
    $this->birth = $patient_info['birth'];
    $this->sex = $patient_info['sex'];
    $this->admission_date = $patient_info['admission_date'];
    $this->owner = $patient_info['owner'];
    $this->status = $patient_info['status'] ? $patient_info['status'] : true;

    $this->table_name = TABLE_NAME;

    return $this;
  }

  private function set_name($name){$this->name = $name;}
  private function set_birth($date){$this->birth = $date;}
  public function change_status($status){
    $this->status = !$this->status;

    if($this->status){
      $this->admission_date = new Date();
    }
  }

  public function edit_patient($name, $birth){

    $result = isset($name)? set_name($name) : false;
    $result = isset($birth)? set_birth($birth):false;

    $result = update($this->get_fields());

    return $result;
  }

  public function get_patient_data(){

    return json_encode($this->get_fields());

  }

  public static function get_patient_list($owner){

    $patient_list = fetch(array('owner'=>$owner));

    return json_encode($patient_list);
  }

  public function update_patient_info(){
    update($this->get_fields());
  }  

  private function get_fields(){

    $patient_data = array();
    $patient_data['id'] = $this->id;
    $patient_data['name'] = $this->name;
    $patient_data['age'] = $this->age;
    $patient_data['sex'] = $this->sex;
    $patient_data['admission_date'] = $this->admission_date;
    $patient_data['owner'] = $this->owner;

    return $patient_data;
  }

}


?>
