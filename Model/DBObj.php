<?php

Class DBObj extends PDO{

  //Gera as constantes de Banco de dados a partir do JSON de configuração
  static function configuraDB($dbconfig_path = "config/dbinfo.json"){

  //Recupera dados de configuração de DB a partir de um arquivo
  $dbconfig_data = fopen($dbconfig_path,"r");
  $dbconfig = json_decode(stream_get_contents($dbconfig_data), true);
  fclose($dbconfig_data);

  $dsn = "pgsql:dbname=".$dbconfig['TESTDB']['dbname']. ";
          host= ".$dbconfig['TESTDB']['host'].
          $dbconfig['TESTDB']['config'];
  var_dump($dsn);
  $database = new PDO($dsn,$dbconfig['TESTDB']['user'],$dbconfig['TESTDB']['password']);
  }

  function fetch($id){

    $stmt = $database->prepare("SELECT * from $table_name WHERE id = :id");
    $stmt = $database->execute(array(":id"=>$id));
    $result = $stmt->fetchAll();

    return $result;
  }

  function set($data){

    //TODO: Definir uma padronização de valores para cada tabela
    $stmt = $database->prepare("INSERT INTO $table_name VALUES ?");
    $stmt = $database->execute($data);

    return $stmt;

  }
  function update( ){
    //TODO
  }
  function delete( ){
    //TODO
  }
}
?>
