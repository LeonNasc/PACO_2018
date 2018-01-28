<?php

require_once("config/config.php");

$patient_info['name'] = "Joana Silva";
$patient_info['birth'] = new DateTime('1969-02-03');
$patient_info['sex'] = true;
$patient_info['owner'] = 'us_5a6cefbc90a0a';
$patient_info['status'] = true;

//$new_patient = new Patient(json_decode(Patient::get_patient_list($patient_info['owner'])));

$patients = json_decode(Patient::get_patient_list($patient_info['owner']),true);

$new_patient = new Patient($patients[0]);

echo $new_patient->get_patient_data(). "<br>";

$new_patient->edit_patient('João Silva');

echo $new_patient->get_patient_data(). "<br>";
//echo $new_patient->get_patient_data();

?>
