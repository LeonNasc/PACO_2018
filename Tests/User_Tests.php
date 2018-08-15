<?php
use PHPUnit\Framework\TestCase;

final class UserTests extends TestCase{

  public function deve_retornar_true_com_login_e_senha_validos($login,$senha){

  }
  public function deve_retornar_exception_com_dados_invalidos($login,$senha){
    $this->expectException(Exception::class);

    User::login('a','b');
  }
}
?>