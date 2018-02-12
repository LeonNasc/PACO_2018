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
     * @return void
     */
    public static function show_error_page($message){
        
        $template = new Tpl;
        $template->assign("error_message", $message);
        $template->draw('error_page');
    }   
    
    /**
     * Exibe a landing page.
     * 
     * Temporariamente aqui até eu fazer uma função mais generalizada
     *
     * @return void
     */
    public static function show_landing(){

        $template = new Tpl;
        $urls = array();
        $urls['Adicione prescrições e resultados laboratoriais de forma fácil'] = 'https://image.flaticon.com/icons/svg/1/1755.svg';
        $urls['Visualize prescrições anteriores e contraste com resultados laboratoriais'] ='https://image.flaticon.com/icons/svg/344/344074.svg';
        $urls['Comente e discuta sobre os seus pacientes de maneira rápida e simples'] = 'https://image.flaticon.com/icons/svg/134/134807.svg';
        $template->assign('icon_url',$urls);
        $template->assign('title','PACO');
        $template->assign('name',isset($_SESSION['active_user_id'])?$_SESSION['active_user_id']:null);
    
        $template->draw('registro');
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
}
?>