<?php

if ($_SERVER['REQUEST_METHOD'] == GET){

    //Mostrar dados a partir de um ID de paciente passado via GET
}
else if ($_SERVER['REQUEST_METHOD'] == POST){


}
else {
  throw new Exception("Invalid method exception");
}



?>
