<?php
class PatientData extends DBObj{

  private $id, $patient, $date, $author,$subject, $content;

  const PRESCRIPTION = "prs_";
  const LAB_RESULT = "res_";
  const COMMENTARY = "com_";
  const RESTORED = TRUE;
  const TABLE_NAME = 'patient_data_db'

  public function __construct($content_info, $type = PatientData::RESTORED){

    //$data_info deve ser um array
    if (!is_array($content_info))
      return new Exception("Parâmetro inválido: \$content_info deve ser array");

    $now = new DateTime();

    $this->id = isset($content_info['id'])? $content_info['id']: uniqid($type);
    $this->patient = $content_info['patient'];
    $this->author = $content_info['author']; //An user ID
    $this->subject = isset($content_info['subject'])?$content_info['subject'] :$this->id; //Can be itself or another PatientData
    $this->content = $content_info['content'];
    $this->date = now->format("d-m-Y");

    $this->table_name = PatientData::TABLE_NAME;
    $this->configura_DB();

    return $this;
  }

  public function add(){
    return $this->set($this->get_fields());
  }

  public function delete(){
    return $this->delete($this->id);
  }

  public function edit($changes){
    $this->content = $changes;
  }

  public function update(){
    return $this->update($this->get_fields());
  }

  public static function get_from_date($date){
    return PatientData::fetch($array('date'=>$date));
  }

  public static function get_for_patient($patient){
    return PatientData::fetch($array('patient'=>$patient));
  }

  public static function get_prescriptions($patient,$quantity){

    $db = new DBObj(PatientData::TABLE_NAME);

    $prescription_data = array();
    $precription_data['patient']= $patient;
    $prescription_data['content'] = PatientData::PRESCRIPTION;

    //TODO: Retornar somente quantidade solicitada no segundo argumento
    return $db->fetch($prescription_data, true);
  }

  public static function get_lab_results(){


  }

  public function get_comments(){


  }

  private function to_json(){


  }

  private function get_fields(){
    $content_data = array();
    $content_data['id'] = $this->id;
    $content_data['patient'] = $this->patient;
    $content_data['author'] = $this->author;
    $content_data['subject'] = $this->subject;
    $content_data['content'] = $this->content;
    $content_data['date'] = $this->date;

    return $content_data;

  }

}
?>
