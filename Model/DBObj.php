<?php

Class DBObj extends PDO{

  protected $database = '';
  public $table_name = '';

  public function __construct($table_name, $path = null){

    $this->table_name = $table_name;
    if (isset($path))
      $this->configura_DB($path);
    else
      $this->configura_DB();

    return $this;
  }

  protected function fetch($data){

    //Permite selecionar por qualquer campo passado, desde que venha como array
    $key = array_keys($data)[0];
    $value = $data[$key];


    $query = "SELECT * from public.$this->table_name WHERE $key = :value";

    $stmt = $this->database->prepare($query);
    $stmt->execute(array(":value"=>$value));
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }

  protected function set($data){

    //Expande as keys da array passada como values e adiciona os valores
    //relacionados

    $columns = "(".implode(", ", array_keys($data)).")";
    $placeholders = ":".implode(",:", array_keys($data));

    $query = "INSERT INTO public.$this->table_name".$columns." VALUES ($placeholders)";

    $stmt = $this->database->prepare($query);
    $stmt->execute($data);

    return $stmt;

  }
  protected function update($data){
    //Cria os dados para o SET e depois no execute as inclui no execute

    $update_info = array(); //Array vazia que vai ser base para string de update

    foreach ($data as $column => $value) {
      $update_info[] = "{$column} = :{$column}";
      //ex.: Campo = :Campo
    }

    $update_info = implode(",", $update_info);

    $stmt = $this->database->prepare("UPDATE public.$this->table_name SET $update_info
                                WHERE id = :id");
    $stmt->execute($data);

    return $stmt;
  }

  protected function delete($id){

    $stmt = $this->database->prepare("DELETE * FROM public.$this->table_name WHERE id = :id");
    $stmt->execute(array(":id"=>$id));

    return $stmt;
  }

  //Gera as constantes de Banco de dados a partir do JSON de configuração
  protected function configura_DB($dbconfig_path = "config/dbinfo.json"){

    //Recupera dados de configuração de DB a partir de um arquivo
    $dbconfig_data = fopen($dbconfig_path,"r");
    $dbconfig = json_decode(stream_get_contents($dbconfig_data), true);
    fclose($dbconfig_data);

    $dsn = "pgsql:dbname=".$dbconfig['TESTDB']['dbname']. ";
            host= ".$dbconfig['TESTDB']['host'].
            $dbconfig['TESTDB']['config'];

    $this->database = new PDO($dsn,$dbconfig['TESTDB']['user'],$dbconfig['TESTDB']['password']);
    $this->database->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
  }

}
?>
