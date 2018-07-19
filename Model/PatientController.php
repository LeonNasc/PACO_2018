<?php

class PatientController {
  
  public static function add_user($params){
      $patient = $params;
      $patient['owner'] = UserController::get_active_user_id();
      $patient = new Patient($patient);

      $patient->add_patient();

      PatientController::set_active_patient($patient);

      return $patient;
  }

  public static function edit_patient($id,$params){

      $patient = Patient::get_from_id($id);
      $patient->change_info($params);
      $patient->update_patient_info();
      
      return $patient;
  }

  public static function change_patient_status($patient_id){

      $patient = Patient::get_from_id($patient_id);
      $patient->change_status();
      $patient->update_patient_info();
      
      return $patient;
  }

  public static function delete_patient($patient_id){
     
      $patient = Patient::get_from_id($patient_id);
      $patient->delete();
  }
  
  public static function render_patient_info(Patient $patient){
    
    $patient = $patient->get_patient_data();
    
    return Helper::make_template('patient_info', array('patient' => $patient), true);
  }

  /*------------ Funções de sessão -------*/

  public static function set_active_patient(Patient $patient){
    $_SESSION['active_patient'] = $patient->get_patient_data()['id'];
  }

  public static function get_active_patient(){
    if(isset($_SESSION['active_patient'])){
      return Patient::get_from_id($_SESSION['active_patient']);
    }
    else
      return null;
  }
}
?>