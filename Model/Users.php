<?php

Class User extends DBObj{

  private $id, $email, $login, $password, $registration_date;

  const TABLE_NAME = "users_db"

  public function __construct($user_info){

    if (!is_array($user_info))
      return new Exception("Parâmetro inválido: \$user_info deve ser array");

    $this->id = isset($user_info['id']) ? $user_info['id'] : null;
    $this->email = $user_info['email']? $user_info['email'] : null;
    $this->login = $user_info['login'];
    $this->password = set_password($user_info['password']);
    $this->registration_date = $user_info['registration_date']? $user_info['registration_date']:new date('Y-m-d H:i:s');
    $this->table_name = TABLE_NAME;

    return $this;
  }

  private function set_login($login){
    $this->login = $login;
    return true;
  }

  private function set_password($new_password){
    $this->password = hash('sha256',$new_password);
    return true;
  }

  public function get_user_data($tipo_de_chamada){
    //Argumento refere à chamada da função: Uso interno *true ou externo *false
    if(!$is_bool($tipo_de_chamada))
      return new Exception("Chamada inválida");

    $returnable = $this->get_fields();
    //Chamada externa: Remove campos sensíveis (senha e email)
    if (!$tipo_de_chamada){
      unset($returnable['email']);
      unset($returnable['password']);
    }

    return json_encode($returnable);
  }

  public static function login($login,$password){

    $user_db = user_exists($login);

    if ($user_db){

      if ($password == $user_db['password']){

        $current_user = new User($user_db);
        $_SESSION['active_user_id'] = $user_db['id'];

        return $current_user;
      }
      else {
        return new Exception("Combinação Login/Senha inválida");
      }
    }
    else {
      return new Exception("Usuário não cadastrado");
    }
  }

  public function add_user(){
    //Se não existir um cadastro desse usuário
    if(!user_exists($this->login)){
      $insert = set($this->get_fields());
    }
    else
      return new Exception("Usuario já existe");
  }

  public function update_user_info($login,$password){

    $result = isset($login)? $this->set_login($login): false;
    $result  = isset($password)? $this->set_password($password) : false;

    $result = update($this->get_fields());

    return $result;
  }

  public function logout(){
    unset($_SESSION['active_user_id']);
  }

  private static function user_exists($login){
    //Busca no BD se existe um registro referente ao usuário
    $result = fetch(array('login'=> $login));
    //Se existir, retorna um array com o $registro
    if ($result && sizeof($result) > 0)
      return $result;
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

    return $user_info;
  }
}
?>
