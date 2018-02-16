<?php
require("config/config.php"); //Arquivo de configuração

if(isset($_SESSION['active_user_id']))
  Helper::make_template('dashboard');
else
  Helper::show_landing();

?>
