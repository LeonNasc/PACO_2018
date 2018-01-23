<?php

Class Users extends DBObj{

  private $id, $email, $login, $password, $registro;

  function __construct($user_info){

    $this->id = isset($user_info['id']) ? $user_info['id'] : null;
    $this->email = $user_info['email']? $user_info['email'] : null;
    $this->login = $user_info['login'];
    $this->password = set_password($user_info['password']);
    $this->registro = new date('Y-m-d H:i:s');
    $this->table_name = "users_db";
  }

  public function set_login($login){$this->login = $login;}

  public function set_password($password){
    $this->password = hash('sha256',$password);
  }

  public function login(){

    $user_db = exists($login);

    if ($user_db){

      if ($this->password = $user_db['password']){
        $this->id = $user_db['id'];
        $this->registro = $user_db['registro'];
        $this->email ] $user_db['email'];
        $_SESSION['id'] = $this->id;

        return true;
      }
      else {
        return //SENHA ERRADA ERRO
      }
    }
    else {
      return //NAO CADASTRADO ERRO
    }
  }

  public function add_user(){
    //Se não existir um cadastro desse usuário
    if(!exists($this->login)){


    }
    else
      return //USUARIO JA EXISTE ERRO

  }

  private function exists(){
    //Busca no BD se existe um registro referente ao usuário
    $result = fetch(array('login'=> $this->login));
    //Se existir, retorna um array com o $registro
    if ($result && sizeof($result) > 0)
      return $result
    else //Se não, retorna false
      return false;
  }
}
?>
