<?php

/**
* Classe base de manejo de banco de dados.
*
* DBObj implementa a conexão com banco de dados via PDO, utilizando, neste
* projeto um banco de dados Postgresql. Permite ações CRUD generalizadas no
* nosso banco de dados.
*
* @see PDO
*/
Class DBObj extends PDO{

  /**
  * Mantém a conexão com o banco de dados
  *
  * @var PDO instance
  */
  protected $database;

  /**
  * Nome da tabela de dados em uso
  *
  * @var String
  */
  protected $table_name;

  /**
  * Constrói um objeto DBObj e configura uma conexão com o banco de dados.
  *
  * Path é um parametro opcional passado para configuraDB.
  *
  * @param String $table_name
  * @param String $path
  *
  * @return DOBj
  */
  public function __construct($table_name, $path = null){

    $this->table_name = $table_name;
    if (isset($path))
      $this->configura_DB($path);
    else
      $this->configura_DB();

    return $this;
  }

  /**
  * Realiza uma busca no banco de dados.
  *
  * Permite fazer um SELECT no banco de dados com qualquer conjunto de
  * $selectors na clásula WHERE. Pode ser usada opcionalmente com LIKE em
  * vez de =.
  *
  * @param array $selectors {
  *   @var String[] de key/values válidas para o table_name em uso
  *  }
  *
  * @param String $selector_type Determina se a busca é exclusiva (AND) ou não (OR)
  * @param boolean $likeness Permite busca por termos similares
  *
  * @return array or boolean
  */
  protected function fetch($selectors, $selector_type="AND", $likeness = false){
    
    //Permite selecionar por qualquer campo passado, desde que venha como array
    $query_fields = $this->get_query_fields($selectors, " ".$selector_type." ",$likeness);

    $query = "SELECT * FROM $this->table_name WHERE $query_fields";

    $stmt = $this->database->prepare($query);
    $stmt->execute($selectors);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }

  /**
   * 
   * Permite buscar uma conjunto de linhas do banco de dados com base em um JOIN
   * 
   * @param $origina_table tabela original
   * @param $joined_table tabela contendo o campo do pivot
   * @param $pivot ponto onde as duas tabelas são unidas
   * 
   */
  protected function joined_fetch($original_table, $joined_table, $pivot){

    $query = "SELECT * FROM $original_table INNER JOIN $joined_table ON $pivot";

    $stmt = $this->database->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
  * Permite que classes herdeiras buscarem nas suas tabelas por um item especifico
  *
  * @param string id
  *
  * @return obj, false
  */
  public static function get_from_id($id){
    $db= new DBOBj(static::TABLE_NAME);

    $result = $db->fetch(array('id'=>$id));

    if($result && sizeof($result) > 0){
      $obj = new static($result[0]);
      return $obj;
    }
    else
      return False;
  }

  /**
  * Inclui um array de valores no banco de dados.
  *
  * Transforma um array em uma query SQL válida no molde INSERT(values)
  * e a executa via PDOStatement.
  *
  * @param array $data {
  *   @var String[] de key/values válidas para o table_name em uso
  *  }
  *
  * @return boolean
  */
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

  /**
  * Atualiza valores no banco de dados a partir de um array.
  *
  * Monta uma query SQL válida a partir de um conjunto de key/values e a executa.
  *
  * @param array $data {
  *   @var String[] de key/values válidas para o table_name em uso
  *  }
  *
  * @return boolean
  */
  protected function update($data){

    //Cria os dados para o SET e depois no execute as inclui no execute
    $update_info = $this->get_query_fields($data, ",");

    $query = "UPDATE public.$this->table_name SET $update_info WHERE id = :id";

    $stmt = $this->database->prepare($query);
    $stmt->execute($data);

    return $stmt;
  }
  /**
  * Remove uma entrada de valores no banco de dados.
  *
  * Deleta uma entrada que contenha um id de uma table_name em uso.
  * Na query, id deverá ser obrigatoriamente um id daquele table_name.
  *
  * @param String $id
  *
  * @return boolean
  */
  protected function delete($id){

    $stmt = $this->database->prepare("DELETE FROM public.$this->table_name WHERE id = :id");
    $stmt->execute(array(":id"=>$id));

    return $stmt;
  }
  
  /**
  * Transforma um array em string válida para queries.
  *
  * Transforma um array em uma string válida para queries SQL, podendo ser
  * de duas formas de filtro:
  * - Campo = :Campo
  * - Campo LIKE :Campo%
  *
  * Assim como podem ser ligados como:
  * - Campo = :Campo, Campo2 = :Campo2
  * - Campo = :Campo AND Campo2 = :Campo2
  * - Campo = :Campo OR Campo2 = :Campo2
  *
  * @param array $data {
  *   @var String[] de key/values válidas para o table_name em uso
  *  }
  *
  * @return String
  */
  private function get_query_fields($data, $glue, $likeness = false){

    $update_info = array(); //Array vazia que vai ser base para string de update

    foreach ($data as $column => $value) {
      if (!$likeness)
        $update_info[] = "{$column} = :{$column}";
      else {
        $update_info[] = "{$column} LIKE '%'||:{$column}||'%'";
      }
    }

    return $update_info = implode($glue, $update_info);
  }

  /**
   * Serializa os campos do objeto em um objeto JSON
   * 
   * @return String JSON
   */
  protected function to_JSON(){
    return json_encode($this->get_fields(), JSON_PRETTY_PRINT);

  }

  /**
  * Configura uma conexão de banco de dados a partir de um JSON
  *
  * Transforma o Json em array, monta um dsn e constroi um objeto PDO
  * que será armazenado na variável de classe $database.
  *
  * @param String $dconfig_path Caminho até o json de configuração.
  *
  * @return void
  */
  //Gera as constantes de Banco de dados a partir do JSON de configuração
  protected function configura_DB($dbconfig_path = "/../config/dbinfo.json"){

    //Recupera dados de configuração de DB a partir de um arquivo
    $dbconfig_data = fopen(__DIR__ .$dbconfig_path,"r");
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
