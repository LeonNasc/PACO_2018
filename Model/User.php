<?php

/**
* Classe base para controle de interação de usuário
*
* Extende a classe DBObj para manejo de operações no banco de dados.
* Capaz de registrar, editar, atualizar, logar e deslogar um usuário.
*
* @see DBObj
*/
Class User extends DBObj{

  /**
  * Nome do banco de dados utilizado pela classe User
  *
  * @const string
  */
  const TABLE_NAME = "users_db";


  /**
  * Membros da classe user que representam os mesmos dados no banco
  *
  * @var string $id : Uniqid com prefixo us_
  * @var string $email: Email do usuário
  * @var string $user_name: Nome do usuário
  * @var string $login: Login único do usuário
  * @var string $password: Senha encriptada do usuário
  * @var string $registration_date: Data da criação do usuário
  *
  */
  private $id, $email, $user_name, $login, $password, $registration_date;

  public function __construct($user_info){

    if (!is_array($user_info))
      throw new Exception("Parâmetro inválido: \$user_info deve ser array");

    $now = new DateTime();

    $this->id = isset($user_info['id'])? $user_info['id'] : uniqid('us_');
    $this->email = isset($user_info['email'])? strtolower($user_info['email']) : null;
    $this->user_name = isset($user_info['user_name'])? $user_info['user_name'] : 'Sem Nome';
    $this->login = $user_info['login'];
    $this->password = strlen($user_info['password'])>= 64 ? $user_info['password'] : hash('sha256',$user_info['password']);
    $this->registration_date = isset($user_info['registration_date'])?
    $user_info['registration_date']:$now->format("d/m/Y");
    $this->table_name = User::TABLE_NAME;

    $this->configura_DB();

    return $this;
  }

  /**
  * Construtor alternativo, gera um User a partir de um id
  *
  * @param string $id
  *
  * @return User, boolean
  */
  public static function get_from_id($id){
    //$id deve ser array
    $db= new DBOBj(User::TABLE_NAME);

    $result = $db->fetch($id);

    if($result && sizeof($result) > 0)
      return new User($result[0]);
    else
      return False;
  }

  /* --------------------- Getters e Setters -----------------------------*/

  /**
  * Altera uma instância de usuário com um login novo
  *
  * @param String $login
  *
  * @return boolean
  */
  private function set_login($login){
    $this->login = strtolower($login);
    return true;
  }

  /**
  * Altera uma instância de usuário com um email novo
  *
  * @param String $email
  *
  * @return boolean
  */
  private function set_email($email){
    $this->email = strtolower($email);
    return true;
  }

  /**
  * Altera uma instância de usuário com uma senha nova
  *
  * @param String $senha
  *
  * @return boolean
  */
  private function set_password($new_password){
    $this->password = hash('sha256',$new_password);
    return true;
  }

  /**
  * Reune todos os dados de usuário em um objeto JSON
  *
  * $inner_call refere ao tipo de chamada da função:
  *       -- Uso interno (True) ou externo (False)
  * @param boolean $inner_call
  *
  * @return string
  */
  public function get_user_data($inner_call = false){
    //Argumento refere à chamada da função: Uso interno *true ou externo *false
    if(!is_bool($inner_call))
      throw new Exception("Chamada inválida");

    $returnable = $this->get_fields();
    //Chamada externa: Remove campos sensíveis (senha e email)
    if (!$inner_call){
      //unset($returnable['email']);
      unset($returnable['password']);
    }

    return $returnable;
  }

  /**
  * Reune todas as variaveis de instância em um array para conveniencia
  *
  * @return array
  */
  private function get_fields(){
    $user_info = array();
    $user_info['id'] = $this->id;
    $user_info['email'] = $this->email;
    $user_info['login'] = $this->login;
    $user_info['password'] = $this->password;
    $user_info['registration_date'] = $this->registration_date;
    $user_info['user_name'] = $this->user_name;

    return $user_info;
  }

  /*----------------------  Funções de acesso ao BD -------------------------*/

  /**
  * Tenta adicionar a instância atual de usuário ao banco de dados
  *
  * @return boolean
  */
  public function add_user(){
    //Se não existir um cadastro desse usuário
    if((User::user_exists($this->login,'login') || User::user_exists($this->email,'login')))
      throw new Exception("Usuario já existe");
    else
      return $insert = $this->set($this->get_fields());
  }

  /**
  * Tenta atualizar os dados da instância atual no banco de dados
  *
  * @param array $new_info
  *  -- $new_info pode ter 3 opções: login, password e email
  *
  * @return boolean
  */
  public function update_user_info($new_info){

    if(!isset($new_info)){
      throw new Exception("Nenhum alteração foi passada");
      return;
    }

    $result = isset($new_info['login'])? $this->set_login($new_info['login']): false;
    $result = isset($new_info['password'])? $this->set_password($new_info['password']) : false;
    $result = isset($new_info['email'])? $this->set_email($new_info['email']) : false;

    if(isset($new_info['login']) || isset($new_info['email'])){
      if(User::user_exists($new_info['login'],'login') || User::user_exists($new_info['email'],'email')){
        throw new Exception("Já em uso");
        }
      }
    else
      $result = $this->update($this->get_fields());

    return $result;
  }

  /**
  * Remove um usuário a partir do ID passado
  *
  * @param array $id
  * $id deve ser array pois o BD separa key e value para a query
  *
  * @return boolean
  */
  public function delete($id){
    
    $patients = Patient::get_patient_list($id);
    
    foreach($patients as $patient){
      
      $patient = $patient->get_from_id($patient['id']);
      $patient->delete();
      
    }
    
    DBObj::delete($id);
  }


  /* ------------------- Funções de acesso ao sistema ------------------------*/

  /**
  * Verifica se as informações de login passadas batem com um usuário válido
  *
  * @param String $login
  * @param String $password
  *
  * @return boolean
  */
  public static function login($login,$password){

    $login = strtolower($login);

    $user_db = User::user_exists($login,'login');

    if ($user_db){

      if (hash('sha256',$password) === $user_db["password"]){

        $current_user = new User($user_db);
        $_SESSION['active_user_id'] = $current_user->get_user_data();

        return $current_user;
      }
      else {
        throw new Exception("Combinação Login/Senha inválida");
      }
    }
    else {
      throw new Exception("Usuário não cadastrado");
    }
  }

  /**
  * Encerra a sessão de usuário
  *
  * @param String $senha
  *
  * @return boolean
  */
  public static function logout(){
    session_destroy();
  }

}
?>
