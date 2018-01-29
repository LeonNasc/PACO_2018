<?php

require_once("config/config.php");

echo "AMOR LINDO DEMAIS";

$user = array();
$user['user_name'] = "Vinicius mlk Piranha 2018";
$user['login']= "nydyow";
$user['password'] = "12345";
$user['email'] = "test@test.com";

$new_user = new User($user);

echo $new_user->get_user_data();

?>
