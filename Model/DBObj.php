<?php

Class DBObj extends PDO{

  $database = '';
  $table_name = '';

  //Gera as constantes de Banco de dados a partir do JSON de configuração
  function __construct($dbconfig_path = "config/dbinfo.json"){

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

    return json_encode($result);
  }

  function set($data){

    //Expande as keys da array passada como values e adiciona os valores
    //relacionados

    $values = "(`".implode("`, `", array_keys($data))."`)";

    $stmt = $database->prepare("INSERT INTO $table_name ($values) VALUES ?");
    $stmt = $database->execute($data);

    return $stmt;

  }
  function update($data){
    //Cria os dados para o SET e depois no execute as inclui no execute

    $update_info = array(); //Array vazia que vai ser base para string de update

    foreach ($data as $column => $value) {
      $update_info[] = "`{$column}` = :{$column}";
      //ex.: Campo = :Campo
    }

    $update_info = implode(",", $update_info);


    $stmt = $database->prepare("UPDATE $table_name SET $update_info
                                WHERE id = :id");
    $stmt = $database->execute($data);

    return $stmt;
  }
  function delete($id){

    $stmt = $database->prepare("DELETE * FROM $table_name WHERE id = :id");
    $stmt = $database->execute(array(":id"=>$id));
  }
}
?>
