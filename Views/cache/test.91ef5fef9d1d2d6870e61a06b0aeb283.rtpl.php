<?php if(!class_exists('Rain\Tpl')){exit;}?><!DOCTYPE html>
<html lang="en">
<head>
	<title></title>
	<link rel="stylesheet" href="CSS/style.css" type="text/css"/>
</head>
<body>
      <!-- if remove_comments is enabled this will disappear -->
    <?php require $this->checkTemplate("header");?>

    Hello <?php echo htmlspecialchars( $name, ENT_COMPAT, 'UTF-8', FALSE ); ?>!


</body>
</html>
