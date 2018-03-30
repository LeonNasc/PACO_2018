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


    const default_recents = 7;


    /**
     * Exibe a landing page.
     *
     * @return string
     */
    public static function show_landing(){

        $icon_url = array();
        $icon_url['Adicione prescrições e resultados laboratoriais de forma fácil'] = 'https://image.flaticon.com/icons/svg/1/1755.svg';
        $icon_url['Visualize prescrições anteriores e contraste com resultados laboratoriais'] ='https://image.flaticon.com/icons/svg/344/344074.svg';
        $icon_url['Comente e discuta sobre os seus pacientes de maneira rápida e simples'] = 'https://image.flaticon.com/icons/svg/134/134807.svg';

        $data['icon_url'] = $icon_url;
        return Helper::make_template('landing',$data);
    }

    /**
     * Fábrica que cria uma template contendo os dados passados.
     *
     * É de suma importância fornecer permissão de escrita para a pasta cache,
     * ou essa função não funciona
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
            return $template->draw($template_name, $ajax);
        }
        catch(Exception $e){
            Helper::make_template('error_page',array("message"=>"Template não existe"), $ajax);
        }
    }

    /**
     * Avalia se há um usuário logado.
     *
     * @return boolean
     */
    public static function check_login_status(){

        if(isset($_SESSION['active_user_id'])){
            return True;
        }
        return False;
    }


    /**
     * Gera uma senha aleatória a partir do alfabeto padronizado
     *
     * @return string
     */
    public static function random_password(){

    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $password = array();
    $alpha_length = strlen($alphabet) - 1;
    for ($i = 0; $i < 8; $i++)
    {
        $n = rand(0, $alpha_length);
        $password[] = $alphabet[$n];
    }
    return implode($password);
  }

  public static function render_patient_data($type){

    $list = PatientData::get_recent_data($_SESSION['active_patient'],$type, Helper::default_recents);
    $list = //Set list as session variable
    print(Helper::make_template('show_'.$type, $list, true));

  }

  public static function update_list($list_type){
      

    switch($list_type){
      case 'patient':
      $_SESSION['patient_list'] = json_decode(Patient::get_patient_list($_SESSION['active_user_id']['id']),true);
      break;
      case 'COMMENTS':
      $list = PatientData::get_recent_data($_SESSION['active_patient'],PatientData::COMMENT);
      var_dump($list);
      $_SESSION['on_view'] = $list;
      break;
      case 'RESULTS':
      $list = PatientData::get_recent_data($_SESSION['active_patient'],PatientData::LAB_RESULT);
      $_SESSION['on_view'] = $list;
      break;
      case 'PRESCRIPTIONS':
      $list = PatientData::get_recent_data($_SESSION['active_patient'],PatientData::PRESCRIPTION);
      $_SESSION['on_view'] = $list;
      break;
    }
  }
}
?>
