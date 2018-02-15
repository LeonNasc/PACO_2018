<?PHP

if ($_SERVER['method'] == GET){
  // Deve devolver um JSON com a lista de pacientes do usuário logado
  echo "Hello world!";
}
else if ($_SERVER['method'] == POST){
  //Deve lidar com as funções ADD, DELETE e UPDATE paciente
  echo "Hello world!2";
}
else {
  throw new Exception("Metodo de acesso inválido");
}
?>
