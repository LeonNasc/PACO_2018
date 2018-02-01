<?PHP

if ($_SERVER['method'] == GET){
  // Deve fornecer a view de registro e adicionar usuário

}
else if ($_SERVER['method'] == POST){
  //Deve lidar com as funções LOGIN, DELETE e UPDATE usuário
}
else {
  throw new Exception("Metodo de acesso inválido");
}
?>
