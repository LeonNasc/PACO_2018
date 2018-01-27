<?php

require_once("config/config.php");

$user_data = array();
$user_data['login']= "leon2";
$user_data['email']= "doctor@test.com";
$user_data['password']= "test";

$user = new User($user_data);

//$user->add_user();
//$user->update_user_info('spaghetti', 'toucha');

//echo $user->get_user_data(). "<br>";
$logged_user = User::login('leon2','test');


//echo $logged_user->get_user_data(). "<br>";
echo "logged user is:";
echo $logged_user->get_user_data(). "<br>";

echo "Confimando o ID na sessão:" . $_SESSION['active_user_id'];

echo "Saindo da sessão";

$logged_user->logout();
echo "Confimando o ID na sessão:" . $_SESSION['active_user_id'];



?>
