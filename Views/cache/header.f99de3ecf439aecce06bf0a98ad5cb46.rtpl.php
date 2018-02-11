<?php if(!class_exists('Rain\Tpl')){exit;}?><!-- HEADER AND NAVBAR -->
<header>
    <nav class="navbar navbar-default">
        <div class="navbar-header">
            <a class="navbar-brand" href="/">PACO</a>
        </div>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="#about"><i class="fa fa-shield"></i> About</a></li>
            <li><a href="#contact"><i class="fa fa-comment"></i> Contact</a></li>
            <li><a>Seja bem vindo, <?php echo (htmlspecialchars( isset($name), ENT_COMPAT, 'UTF-8', FALSE )?htmlspecialchars( $name , ENT_COMPAT, 'UTF-8', FALSE ): "Usuário"); ?><br><small>Não é cadastrado?</a></li>
        </ul>
    </nav>
</header>
