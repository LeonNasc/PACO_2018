<?php

require_once("vendor/autoload.php"); //Autoloader do composer
require_once("config/config.php"); //Arquivo de configuração

use Rain\Tpl;
$t = new Tpl;
$t->assign('title','Hello!');
$t->assign('name', 'banana');
$t->assign('brand', 'PACO');
$t->draw('test');

?>
<!-- index.html -->
    <!DOCTYPE html>
    <html>
    <head>
      <!-- CSS e similares -->
      <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" />

      <!-- Scripts -->
      <script src="script.js"></script>
    </head>
    <body>

        <div id="main">

            <!-- Área dos templates -->

        </div>

    </body>
    </html>
