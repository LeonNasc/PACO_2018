<?php
use PHPMailer\PHPMailer\PHPMailer;

//TODO: Documentar classe, retirar senha do email do produto final
/**
* Classe com finalidade de abstrair as funções de email
*
* É um singleton que mantém uma instância do PHPMailer disponível,
* permite as funções write(escrever) e send(enviar)
*
* @see PHPMailer
*/
class Mailer {

  /**
  * Mantém a instância atual
  *
  * @var Mailer instance
  */
  private static $instance;

  /**
  * Mantém o objeto PHPMailer em uso pela instância
  *
  * @var PHPMailer instance
  */
  private $mailer = null;

  private function __construct(){
      //Configura a instância do PHPMailer
      $this->mailer = new PHPMailer;
      $this->mailer->isSMTP();
      $this->mailer->SMTPDebug = 0; //Não mostrar mensagens de debug
      $this->mailer->Host = 'smtp.gmail.com';
      $this->mailer->Port = 587;
      $this->mailer->SMTPSecure = 'tls';
      $this->mailer->SMTPAuth = true;
      $this->mailer->Username = "noreplypaco.bot@gmail.com"; //Retirar depois
      $this->mailer->Password = "f9ry4oo2"; //Retirar depois
      $this->mailer->setFrom($this->mailer->Username, 'PACO Bot');
  }

  /**
  * Verifica se existe uma instância de Mailer ativa e a cria, caso não exista
  *
  * @return Mailer
  */
  public static function get_instance(){
    if(!self::$instance){
      self::$instance = new self();
      }

    return self::$instance;
  }

  /**
  * Prepara o mailer de instância com os campos de uma mensagem a ser enviada.
  *
  * @param string $subject
  * @param array $mailto
  *      -- $mailto['email'] : Endereço de email a ser enviado
  *      -- $mailto['name'] : Nome do destinatário
  * @param string $content : Conteúdo da mensagem a ser enviada (HTML)
  *
  * @return boolean
  */
  public function write($subject, $mailto, $content){
    //$mailto deve ser array
    //$subject e content devem ser strings
    if (!$subject || !$mailto || !$content)
      throw new Exception("Não existe um email a ser enviado");

    $this->mailer->addAddress($mailto['email'],$mailto['name']);
    $this->mailer->Subject = $subject;
    $this->mailer->msgHTML($content);

    return true;
  }

  /**
  * Considerando que os campos de uma mensagem estejam preenchidos
  *
  * @return boolean
  */
  public function send(){
    //Envia a mensagem: Requer que exista uma mensagem já escrita
    if(!isset($this->mailer->Subject) || !isset($this->mailer->Body))
      throw new Exception("Não existe um email a ser enviado");

    if (!$this->mailer->send()) {
      echo "Mailer Error: " . $this->mailer->ErrorInfo;
      return false;
    }

    return true;
  }

  /**
  * Desabilita a função mágica clone
  *
  */
  private function __clone(){
    throw new Exception("Mailer não pode ser clonado");
  }
}
?>
