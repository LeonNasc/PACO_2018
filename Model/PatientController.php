<?php

class PatientController {
  
  // case 'add':
  //     $patient = &$params;
  //     $patient['owner'] = $_SESSION['active_user_id']['id'];

  //     $patient = new Patient($patient);
  //     $patient->add_patient();
  //     $patient = $patient->get_patient_data();

  //     $_SESSION['active_patient'] = $patient['id'];
  //     //$_SESSION['last_seen'].push($patient['id']);

  //     //Bug bizonho da primeira exibição
  //     $patient['sex'] = !$patient['sex'];
  //     $patient['status'] = !$patient['status'];

  //     Helper::make_template('patient_info', array('patient' => $patient), false);
  //     exit();

  //     break;

  // case 'edit':

  //     $patient = Patient::get_from_id($params['id']);
  //     $patient->change_info($params);
  //     $patient->update_patient_info();

  //     $patient = $patient->get_patient_data();
  //     Helper::make_template('patient_info', array('patient' => $patient), false);
  //     break;

  public static function change_patient_status($patient_id){

      $patient = Patient::get_from_id($patient_id);
      $patient->change_status();
      $patient->update_patient_info();
      
  }

  public static function delete_patient($patient_id){
     
      $patient = Patient::get_from_id($patient_id);
      $patient->delete();
  }
  
  public static function render_patient_info($patient_id){
    
    $patient = Patient::get_from_id($patient_id);
    $patient = $patient->get_patient_data();
    
    return Helper::make_template('patient_info', array('patient' => $patient), true);
  }
}
?>