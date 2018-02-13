<?php if(!class_exists('Rain\Tpl')){exit;}?><html lang="pt-br">
    <?php require $this->checkTemplate("meta");?>

<body>
    <?php require $this->checkTemplate("header");?>

    <div> <?php echo htmlspecialchars( $error_message, ENT_COMPAT, 'UTF-8', FALSE ); ?> </div>
    <?php require $this->checkTemplate("footer");?>

</body>
</html>
