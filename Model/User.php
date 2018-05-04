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

    $this->id = isset($user_info['id'])? 
    $user_info['id'] : uniqid('us_');
    
    $this->email = isset($user_info['email'])? 
    strtolower($user_info['email']) : null;
    
    $this->user_name = isset($user_info['user_name'])?
    $user_info['user_name'] : 'Sem Nome';
    
    $this->login = $user_info['login'];
    
    $this->password = User::is_hashed_password($user_info['password'])? 
    $user_info['password'] : hash_password($user_info['password']) ;
    
    $this->registration_date = isset($user_info['registration_date'])?
    $user_info['registration_date']:$now->format("d/m/Y");
    
    $this->table_name = User::TABLE_NAME;

    $this->configura_DB();

    return $this;
  }
  
   /**
    * Instancia um usuário a partir dos parametros da form de registro
    * 
    */
    public static function make_new_from_form($form_fields){
      
      $user_data = array();
      $user_data['user_name'] = $form_fields['nome'];
      $user_data['email'] = $form_fields['email'];
      $user_data['login'] = $form_fields['login'];
      $user_data['password'] = $form_fields['senha'];

      $user = new User($user_data);
        
      return $user;
    }

  /* --------------------- Getters e Setters -----------------------------*/

  /**
  * Altera uma instância de usuário com um login novo
  *
  * @param String $login
  *
  * @return boolean
  */
  public function set_login($login){
    if(!User::collide($login,'login')){
      $this->login = strtolower($login);
      return true;  
    }
    return false;
  }

  /**
  * Altera uma instância de usuário com um email novo
  *
  * @param String $email
  *
  * @return boolean
  */
  public function set_email($email){
    if(!User::collide($email,'email')){
      $this->email = strtolower($email);
      return true;
    }
    return false;
  }

  /**
  * Altera uma instância de usuário com uma senha nova
  *
  * @param String $senha
  *
  * @return boolean
  */
  public function set_password($new_password){
    $this->password = User::hash_password($new_password);
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
    if((User::collide($this->login,'login') || User::collide($this->email,'login')))
      throw new Exception("Usuario já existe");
    else
      return $insert = $this->set($this->get_fields());
  }

  /**
  * Tenta atualizar os dados da instância atual no banco de dados
  *
  * @return boolean
  */
  public function update_user_info($new_info){
    
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
    $this->logout();
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

    $user_db = User::collide($login,'login');

    if ($user_db){

      if (hash('sha256',$password) === $user_db["password"]){

        $current_user = new User($user_db);
        User::set_active_user($current_user);
        
        return true;
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
  
   /**
   * Coloca o usuário atual como usuário ativo
   */
   public static function set_active_user(User $user){
     
     $_SESSION['active_user_id'] = $user->get_user_data();
     
   }
   
   /**
    * Retorna true se a senha ocupar 256 bits (Assim como um string codificado em SHA256)
    */
   private static function is_hashed_password($password){
     return (strlen($password) == 64);
   }
   
   /**
    * Codifica a senha do usuario em SHA256
    */
   private static function hash_password($password){
     return hash('sha256',$password);
   }
}
?>
