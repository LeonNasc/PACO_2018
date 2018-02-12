<?php

Class User extends DBObj{

  private $id, $email, $user_name, $login, $password, $registration_date;

  const TABLE_NAME = "users_db";

  public function __construct($user_info){

    if (!is_array($user_info))
      return new Exception("Parâmetro inválido: \$user_info deve ser array");

    $now = new DateTime();

    $this->id = isset($user_info['id'])? $user_info['id'] : uniqid('us_');
    $this->email = $user_info['email']? $user_info['email'] : null;
    $this->user_name = isset($user_info['user_name'])? $user_info['user_name'] : 'Sem Nome';
    $this->login = $user_info['login'];
    $this->password = hash('sha256',$user_info['password']);
    $this->registration_date = isset($user_info['registration_date'])?
    $user_info['registration_date']:$now->format("d-m-Y");
    $this->table_name = User::TABLE_NAME;

    $this->configura_DB();

    return $this;
  }

  //Getters e Setters

  private function set_login($login){
    $this->login = $login;
    return true;
  }

  private function set_password($new_password){
    $this->password = hash('sha256',$new_password);
    return true;
  }

  public function get_user_data($tipo_de_chamada = false){
    //Argumento refere à chamada da função: Uso interno *true ou externo *false
    if(!is_bool($tipo_de_chamada))
      return new Exception("Chamada inválida");

    $returnable = $this->get_fields();
    //Chamada externa: Remove campos sensíveis (senha e email)
    if (!$tipo_de_chamada){
      unset($returnable['email']);
      unset($returnable['password']);
    }

    return json_encode($returnable);
  }

  //Funções de cadastro

  public function add_user(){
    //Se não existir um cadastro desse usuário
    if(!User::user_exists($this->login)){
      return $insert = $this->set($this->get_fields());
    }
    else
      throw new Exception("Usuario já existe");
  }

  public function update_user_info($login=null,$password=null){
    
    if(!isset($login) && !isset($password)){
      throw new Exception("Nenhum alteração foi passada");
      return;
    }
    $result = isset($login)? $this->set_login($login): false;
    $result  = isset($password)? $this->set_password($password) : false;

    if(!User::user_exists($login))
      $result = $this->update($this->get_fields());
    else
      throw new Exception("Nome de usuário já em uso");

    return $result;
  }

  //Funções de acesso

  public static function login($login,$password){

    $user_db = User::user_exists($login);

    if ($user_db){

      if (hash('sha256',$password) === $user_db["password"]){

        $current_user = new User($user_db);
        $_SESSION['active_user_id'] = $user_db['id'];

        return $current_user;
      }
      else {
        return new Exception("Combinação Login/Senha inválida");
      }echo $query;
    }
    else {
      return new Exception("Usuário não cadastrado");
    }
  }

  public static function logout(){
    unset($_SESSION['active_user_id']);
  }

  //Funções utilitárias
  
  public static function get_from_id($id){
    
    $db= new DBOBj(User::TABLE_NAME);
    
    $result = $db->fetch(array('id'=>$id));
    
    if($result && sizeof($result) > 0)
      return new User($result[0]);
    else
      return False;
  }

  private static function user_exists($login){

    $db = new DBOBj(User::TABLE_NAME);

    //Busca no BD se existe um registro referente ao usuário
    $result = $db->fetch(array('login'=> $login));
    //Se existir, retorna um array com o $registro
    if ($result && sizeof($result) > 0)
      return $result[0];
    else //Se não, retorna false
      return false;
  }

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
}
?>
