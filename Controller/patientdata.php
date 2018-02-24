<?php

if ($_SERVER['REQUEST_METHOD'] == GET){

  switch($_POST['task']){

    //Caminhos de exibição
    case 'URI_view'

    break;
    case 'list_pre'

    break;
    case 'list_res'

    break;
    case 'list_com'

    break;
    default:

    break;

    //Controla a exibição dos views pertinentes
    case 'add_pre'

    break;
    case 'add_res'

    break;
    case 'add_com'

    break;
    case 'edit_pre'

    break;
    case 'edit_res'

    break;
    case 'edit_com'

    break;
  }
}
else if ($_SERVER['REQUEST_METHOD'] == POST){

  switch($_POST['task']){
    case 'add_pre'

    break;
    case 'add_res'

    break;
    case 'add_com'

    break;
    case 'edit_pre'

    break;
    case 'edit_res'

    break;
    case 'edit_com'

    break;
    default:

    break;
  }
}
else {
  throw new Exception("Invalid method exception");
}



?>
