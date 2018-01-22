<?php

// Exibição de erros
error_reporting(-1);
ini_set("display_errors", "1");
ini_set("log_errors", 1);
ini_set("error_log", "/bin/php-error.log");

require_once("Model/DBObj.php");

DBObj::configuraDB();



?>
