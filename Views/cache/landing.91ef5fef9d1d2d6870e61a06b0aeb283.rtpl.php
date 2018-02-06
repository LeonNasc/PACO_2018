<?php if(!class_exists('Rain\Tpl')){exit;}?><!DOCTYPE html>
<html lang="pt-br">
    <?php require $this->checkTemplate("meta");?>

<body>
    <?php require $this->checkTemplate("header");?>


    <?php require $this->checkTemplate("landing_paco_jumbo");?>


    <div class = 'jumbotron' id = 'Sobre'>
      <div class = 'container'>
        <div class ='row'>
            <div class = 'col-xs-12 col-sm-12 col-md-12 col-lg-12'>

                <div class = 'row'>
                    <?php require $this->checkTemplate("landing_icon_template");?>

                </div>

                <br><br>
                <span style="font-size:0.9em"> O Acompanhamento Farmacoterapêutico é
                  definido como uma prática personalizada na qual o farmacêutico
                  tem a responsabilidade de orientar o paciente, além de
                  detectar, prevenir e resolver todos os problemas relacionados
                  com medicamentos (PRM) de uma maneira contínua, sistemática e
                  documentada, em colaboração com o paciente e equipe
                  multiprofissional. O <strong>programa de acompanhamento
                  farmacoterapêutico</strong> é uma ferramenta elaborada para
                  facilitar o acompanhamento de prescrições de pacientes
                  internados em ambiente hospitalar ou ambulatorial.
                </span>
            </div>
        </div>
    	</div>
    </div>

    <?php require $this->checkTemplate("footer");?>

</body>
</html>
