<?php if(!class_exists('Rain\Tpl')){exit;}?><!-- HEADER AND NAVBAR -->
<header>
    <nav class="navbar navbar-light bg-light">
        <div class="navbar-header">
            <a class="navbar-brand" href="/">PACO</a>
        </div>
        <ul class="nav justify-content-end">
            <li class="nav-item">
                <a class="nav-link" href="#">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#about">About</a>
                </li>
            <li class="nav-item">
                <a class="nav-link" href="#contact">Contact</a>
            </li>
            <li class="nav-item">
                <a class="nav-link">Seja bem vindo, <?php echo (htmlspecialchars( isset($name), ENT_COMPAT, 'UTF-8', FALSE )?htmlspecialchars( $name , ENT_COMPAT, 'UTF-8', FALSE ): "Usuário"); ?>

                <!--<span><br><small><a>Não é cadastrado? Cadastre-se</a></small></span>-->
                </a>
            </li>
        </ul>
    </nav>
</header>
<div id="main-content">
