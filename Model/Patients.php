<?php
Class Patient extends DBOj{

  private $id, $name, $birth, $sex, $admission_date, $owner, $status;

  public function __construct($patient_info){

    if (!is_array($patient_info))
      return new Exception("Parâmetro inválido: \$patient_info deve ser array");

    $this->table_name = "patients_db";

    return $this;
  }

  private function set_name($name)
  private function set_birth($birth)
  public function set_status($status){



  }

  public function get_patient_data(){


  }

  public static function get_patient_list($owner){


  }

  public function update_patient_info()
  private function set_login($login)
  private function set_login($login)

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
