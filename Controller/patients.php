<?PHP

if ($_SERVER['method'] == GET){
  // Deve devolver um JSON com a lista de pacientes do usuário logado

}
else if ($_SERVER['method'] == POST){
  //Deve lidar com as funções ADD, DELETE e UPDATE paciente
}
else {
  throw new Exception("Metodo de acesso inválido");
}
?>
