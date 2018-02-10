<?php if(!class_exists('Rain\Tpl')){exit;}?><html lang="pt-br">
    <?php require $this->checkTemplate("meta");?>

<body>
    <?php require $this->checkTemplate("header");?>

    <div class="test panel panel-default">
    <fieldset id="registro">
      <legend>Cadastrar usuário </legend>
      <form action="Controller/users.php" method="get">

          <input name="task" type=hidden value="registrar" required>
        <div>
          <label for="nome">Nome:</label>
          <input type="text" name="nome"/ required>
        </div>
        <div>
          <label for="email">E-mail:</label>
          <input type="text" name="email"/ required>
        </div>
          <label for="login">Login:</label>
          <input type="text" name="login"/ required>
        <div>
          <label for="senha">Senha:</label>
          <input type="text" name="senha"/ required>
        </div>
          <input type="submit" style="width:100%" value="Registrar"/>

        Todos os campos são obrigatórios
      </form>
    </fieldset>
    </div>
    <?php require $this->checkTemplate("footer");?>

</body>
</html>
