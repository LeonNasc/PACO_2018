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
   * @param $columns colunas desejadas das tabelas
   * @param $origina_table tabela original
   * @param $joined_table tabela contendo o campo do pivot
   * @param $pivot ponto onde as duas tabelas são unidas
   * 
   */
  protected function joined_fetch($columns, $original_table, $joined_table, $pivot){

    $query = "SELECT $columns FROM $original_table INNER JOIN $joined_table ON $pivot ORDER BY date DESC";

    $stmt = $this->database->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }

  /**
  * Permite que classes herdeiras buscarem nas suas tabelas por um item especifico
  *
  * @param string id
  *
  * @return obj, boolean
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

  /* ------------------------ Utilitários ----------------------------------*/

  /**
  * Verifica se existe no BD um usuário que bata com os dados passados
  *
  * @param String $identifier -> identificador unico (email, id ou nome)
  * @param String $type -> permite buscar por email, id, nome
  *
  * @return boolean
  */
  public static function collide($id,$type){

    $db = new DBOBj(static::TABLE_NAME);

    //Busca no BD se existe um registro referente ao usuário
    $result = $db->fetch(array($type=> $id));
    //Se existir, retorna um array com o $registro
    if ($result && sizeof($result) > 0)
      return $result[0];
    else //Se não, retorna false
      return false;
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

    $updated_info = array(); //Array vazia que vai ser base para string de update

    foreach ($data as $column => $value) {
      if ($likeness)
        $updated_info[] = "{$column} LIKE '%'||:{$column}||'%'";
      else {
        $updated_info[] = "{$column} = :{$column}";
      }
    }

    return $updated_info = implode($glue, $updated_info);
  }

  /**
   * Serializa os campos do objeto em um objeto JSON
   * 
   * @return String JSON
   */
  protected function JSONify($fields){
    return json_encode($fields, JSON_PRETTY_PRINT);

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
  protected function configura_DB(){

    $dbconfig = Helper::read_config(CONFIG_PATH);
    
    $dsn = "pgsql:dbname=".$dbconfig['PRODDB']['dbname']. ";
            host= ".$dbconfig['PRODDB']['host'].
            $dbconfig['PRODDB']['config'];

    $this->database = new PDO($dsn,$dbconfig['PRODDB']['user'],$dbconfig['PRODDB']['password']);
    $this->database->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
  }

}
?>
