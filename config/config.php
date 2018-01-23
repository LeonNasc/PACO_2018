<?php

// Exibição de erros
error_reporting(-1);
ini_set("display_errors", "1");
ini_set("log_errors", 1);
ini_set("error_log", "/bin/php-error.log");

date_default_timezone_set("America/Sao_Paulo");

//Inicia a sessão
if(!isset($_SESSION))
  session_start();

//Caso o usuário não esteja numa das páginas autorizadas, redireciona para index
if (!in_array($_SERVER["PHP_SELF"], ["/login.php", "/logout.php", "/register.php"]))
{
    if (empty($_SESSION["id"]))
    {
        redirect("index.php");
    }
}

?>
