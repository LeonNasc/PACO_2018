<?php
use Rain\Tpl;

class Helper{
 
    public static function show_error_page($message){
        
        $template = new Tpl;
        $template->assign("error_message", $message);
        $template->draw('error_page');
    }   
    
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

}
?>