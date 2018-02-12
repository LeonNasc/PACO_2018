<?php

if ($_SERVER['method'] == GET){

    //Mostrar dados a partir de um ID de paciente passado via GET
}
else if ($_SERVER['method'] == POST){


}
else {
  throw new Exception("Invalid method exception");
}



?>
