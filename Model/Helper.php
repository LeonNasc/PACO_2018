<?php
use Rain\Tpl;

/**
* Classe contendo métodos estáticos para funções helper para os controladores.
*
* Utiliza intensivamente o Rain\Tpl como engine de templates, servindo-os quando necessário.
*
* @see Rain\Tpl
*/
class Helper{
    
    /**
     * Exibe a landing page.
     * 
     * @return string
     */
    public static function show_landing(){

        $dados = array();
        $dados['Adicione prescrições e resultados laboratoriais de forma fácil'] = 'https://image.flaticon.com/icons/svg/1/1755.svg';
        $dados['Visualize prescrições anteriores e contraste com resultados laboratoriais'] ='https://image.flaticon.com/icons/svg/344/344074.svg';
        $dados['Comente e discuta sobre os seus pacientes de maneira rápida e simples'] = 'https://image.flaticon.com/icons/svg/134/134807.svg';
        
        return Helper::make_template('landing',$dados);
    }
    
    /**
     * Fábrica que cria uma template contendo os dados passados.
     * 
     * @param string $template_name: Nome do template a ser passado 
     * @param array $params : Parametros que serão renderizados no template
     * @param boolean $ajax : Define se será retornado somente o HTML como texto
     * 
     * @return string
     */
    public static function make_template($template_name, $params = null, $ajax = false)
    {
        $template = new Tpl;
        
        //Alguns templates não tem parametros a serem renderizados
        if(isset($params)){
            foreach($params as $key=>$value){
                $template->assign($key,$value);
            }
        }
        
        try{
            if($ajax)
                return Helper::return_template_html($template->draw($template_name, True));
            else
                $template->draw($template_name);
        }
        catch(Exception $e){
            return Helper::make_template('error_page',array("message"=>"Template não existe"), $ajax);
        }
    }
    
    /**
     * Avalia se há um usuário logado. 
     *
     * @return boolean, void
     */
    public static function check_login_status(){
        
        if(!isset($_SESSION)){
            session_start();
        }
        
        if(isset($_SESSION['active_user_id'])){
            
            return True;
        }
        
        return False;
        
    }
    
    /**
     * Imprime html a partir de um template pre-renderizado.
     * Importante para chamadas JSON
     *
     * @return void
     */
    public static function return_template_html($template){
        $content = $template;
        header("Content-type:text/plain");
        print((utf8_encode(addslashes($content))));
    }
}
?>