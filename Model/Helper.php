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
     * Exibe uma mensagem de erro a partir do template padrão
     *
     * @param string $message Mensagem de erro que será exibida
     *
     * @return string
     */
    public static function show_error_page($message){
        
        $template = new Tpl;
        $template->assign("error_message", $message);
        return  Helper::return_template_html($template->draw('error_page', True));
    }   
    
    /**
     * Exibe a landing page.
     * 
     * Temporariamente aqui até eu fazer uma função mais generalizada
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
     * @param boolean $ajax : Define se será retornado somente o HTML como texto ou se renderizará a página
     * @return string
     */
    public static function make_template($template_name, $params = null, $ajax = false)
    {
        $template = new Tpl;
        
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
            return Helper::make_template('error_page',array("error_message"=>"Template não existe"), $ajax);
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
            
            return true;
        }
        
        else{
            //Redireciona para index
            //header("https://playground-leon19.c9users.io/PACO_2018/");
            //exit();    
        }
        
    }
    
    public static function return_template_html($template){
        $content = $template;
        header("Content-type:text/plain");
        print((utf8_encode(addslashes($content))));
    }
}
?>