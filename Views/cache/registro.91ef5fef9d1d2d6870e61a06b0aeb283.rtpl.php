<?php if(!class_exists('Rain\Tpl')){exit;}?><html lang="pt-br">
    <?php require $this->checkTemplate("meta");?>

<body>
    <?php require $this->checkTemplate("header");?>

    <fieldset id="registro">
      <form action="/Controller/login.php" method="post">
        <div>
          <label for="name">Nome</label>
          <input id="name" type="text" required>
        </div>
        <div>
          <label for"email">E-mail</label>
          <input id="email" type="text" required>
        </div>
        <div>
          <label for="login">Login</label>
          <input id="login"type="text" required>
        </div>
        <div>
          <label for="password">Senha</label>
          <input id="password" type='password' required>
        </div>
        Todos os campos são obrigatórios
      </form>
    </fieldset>
    <?php require $this->checkTemplate("footer");?>

</body>
</html>
