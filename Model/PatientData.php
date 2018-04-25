<?php
class PatientData extends DBObj{

  private $id, $patient, $date, $author,$subject, $content;

  const PRESCRIPTION = "prs_";
  const LAB_RESULT = "res_";
  const COMMENT = "com_";
  const RESTORED = TRUE;
  const TABLE_NAME = 'patient_data_db';

  public function __construct($content_info, $type = PatientData::RESTORED){

    //$data_info deve ser um array
    if (!is_array($content_info))
      return new Exception("Parâmetro inválido: \$content_info deve ser array");

    $now = new DateTime();

    $this->id = isset($content_info['id'])? $content_info['id']: uniqid($type);
    $this->patient = $content_info['patient'];
    $this->author = $content_info['author']; //An user ID
    $this->subject = isset($content_info['subject'])?$content_info['subject'] :$this->id; //Can be itself or another PatientData
    $this->content = $content_info['content']; //Quando lab ou prescrição, deve ser um JSON
    $this->date = $now->format("d-m-Y");

    $this->table_name = PatientData::TABLE_NAME;
    $this->configura_DB();

    return $this;
  }

  public function add(){
    return $this->set($this->get_fields());
  }

  public function delete($id = null){
    return DBObj::delete($this->id);
  }

  public function edit($changes){
    $this->content = $changes;
  }

  public function update_patient_data(){
    return $this->update($this->get_fields());
  }

  public static function get_list($date){
    $db = new DBObj(PatientData::TABLE_NAME);

    return $db->fetch(array("date" => $date));
  }

  public static function get_for_patient($patient){
    $db = new DBObj(PatientData::TABLE_NAME);
    
    return $db->fetch(array('patient'=>$patient));;
  }

  public static function get_recent_data($patient,$type,$quantity = null){
    /*
      Exemplo de chamada (para prescrições)
      $patient_data->get_data($paciente, PatientData::PRESCRIPTION, 12).
    */
    $db = new DBObj(PatientData::TABLE_NAME);

    $patient_data['patient']= substr($patient,4);
    $patient_data['id'] = $type;
    
    $columns = self::TABLE_NAME. ".*, " . User::TABLE_NAME.".user_name";

    $pivot = self::TABLE_NAME.".author = ".User::TABLE_NAME .".id 
    WHERE ". self::TABLE_NAME.".patient = '".$_SESSION['active_patient']."' 
    AND ".self::TABLE_NAME.".id LIKE '$type%'";

    $recents = $db->joined_fetch($columns, self::TABLE_NAME,User::TABLE_NAME,$pivot);
    
    //Previne que o slice seja maior que o tamanho da array
    if($quantity > count($recents))
      $quantity = count($recents);
    
    $recents = array_slice($recents,0,$quantity);
    
    return $recents;
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

  public function to_JSON(){
    return $this->JSONify($this->get_fields());
  }
}
?>
