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
    $this->email = isset($user_info['email'])? $user_info['email'] : null;
    $this->user_name = isset($user_info['user_name'])? $user_info['user_name'] : 'Sem Nome';
    $this->login = $user_info['login'];
    $this->password = strlen($user_info['password'])>= 64 ? $user_info['password'] : hash('sha256',$user_info['password']);
    $this->registration_date = isset($user_info['registration_date'])?
    $user_info['registration_date']:$now->format("m-d-Y");
    $this->table_name = User::TABLE_NAME;

    $this->configura_DB();

    return $this;
  }

  //Getters e Setters

  private function set_login($login){
    $this->login = $login;
    return true;
  }

  private function set_email($email){
    $this->email = $email;
    return true;
  }

  private function set_password($new_password){
    $this->password = hash('sha256',$new_password);
    return true;
  }

  public function get_user_data($tipo_de_chamada = false){
    //Argumento refere à chamada da função: Uso interno *true ou externo *false
    if(!is_bool($tipo_de_chamada))
      throw new Exception("Chamada inválida");

    $returnable = $this->get_fields();
    //Chamada externa: Remove campos sensíveis (senha e email)
    if (!$tipo_de_chamada){
      //unset($returnable['email']);
      unset($returnable['password']);
    }

    return json_encode($returnable);
  }

  //Funções de cadastro

  public function add_user(){
    //Se não existir um cadastro desse usuário
    if((User::user_exists($this->login,'login') || User::user_exists($this->email,'login')))
      throw new Exception("Usuario já existe");
    else
      return $insert = $this->set($this->get_fields());
  }

  public function update_user_info($login=null,$password=null,$email=null){

    if(!isset($login) && !isset($password) && !isset($email)){
      throw new Exception("Nenhum alteração foi passada");
      return;
    }

    $result = isset($login)? $this->set_login($login): false;
    $result = isset($password)? $this->set_password($password) : false;
    $result = isset($email)? $this->set_email($email) : false;

    if(User::user_exists($login,'login') || User::user_exists($email,'email'))
      throw new Exception("Já em uso");
    else
      $result = $this->update($this->get_fields());

    return $result;
  }

  public function delete($data){
    DBObj::delete($data);
  }

  //Funções de acesso

  public static function login($login,$password){

    $user_db = User::user_exists($login,'login');

    if ($user_db){

      if (hash('sha256',$password) === $user_db["password"]){

        $current_user = new User($user_db);
        $_SESSION['active_user_id'] = json_decode($current_user->get_user_data(),true);

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

  public static function logout(){
    session_destroy();
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

  public static function user_exists($identifier,$type){

    $db = new DBOBj(User::TABLE_NAME);

    //Busca no BD se existe um registro referente ao usuário
    $result = $db->fetch(array($type=> $identifier));
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
