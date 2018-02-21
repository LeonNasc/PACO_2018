<?php
use PHPMailer\PHPMailer\PHPMailer;
class Mailer {

  private static $instance;
  private $mailer = "null";


  private function __construct(){
      //Configura a instância do PHPMailer
      $this->mailer = new PHPMailer;
      $this->mailer->isSMTP();
      $this->mailer->SMTPDebug = 0;
      $this->mailer->Host = 'smtp.gmail.com';
      $this->mailer->Port = 587;
      $this->mailer->SMTPSecure = 'tls';
      $this->mailer->SMTPAuth = true;
      $this->mailer->Username = "noreplypaco.bot@gmail.com"; //Retirar depois
      $this->mailer->Password = "f9ry4oo2"; //Retirar depois
      $this->mailer->setFrom($this->mailer->Username, 'PACO Bot');
  }

  public static function get_instance(){
    if(!self::$instance){
      self::$instance = new self();
      }

    return self::$instance;
  }

  public function write($subject, $target, $content){
    //target deve ser array
    //$subject e content devem ser strings
    if (!$subject || !$target || !$content)
      throw new Exception("Não existe um email a ser enviado");

    $this->mailer->addAddress($target['email'],$target['name']);
    $this->mailer->Subject = $subject;
    $this->mailer->msgHTML($content);
  }

  public function send(){
    //Envia a mensagem: Requer que exista uma mensagem já escrita
    if(!isset($this->mailer->Subject) || !isset($this->mailer->Subject))
      throw new Exception("Não existe um email a ser enviado");

    if (!$this->mailer->send()) {
      echo "Mailer Error: " . $this->mailer->ErrorInfo;
    }
  }

  private function __clone(){
    //remover clonagem
  }
}
?>
