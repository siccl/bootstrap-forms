<?php
namespace Teknicode\Form;
class Process{
  private $method; //mail, sms, mysqli
  private $attributes;
  private $compiled;
  function __construct($method="mail"){
    $this->method=$method;
  }

  public function set(){
      if(func_num_args() == 2) {
          $key = func_get_arg(0);
          $value = func_get_arg(1);
          $this->attributes[$key] = $value;
      }else{
          foreach(func_get_args()[0] as $key => $value){
              $this->attributes[$key]=$value;
          }
      }
      return $this;
  }

  public function get($key){
      return (!empty($this->attributes[$key]) ? $this->attributes[$key] : null);
  }

  public function recaptcha($private_key){
      $this->set("recaptcha",$private_key);
  }

  public function catch(){
    //check for post
    if( $_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST) ){
      if( is_array($this->attributes) && !empty($this->attributes) ){

        //check if recaptcha set
        if( !empty($this->get("recaptcha")) ){
          //recaptcha check required
          $recaptcha_check = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$this->get("recaptcha")."&response=".$_POST['g-recaptcha-response']);
          $recaptcha_check_response = json_decode($recaptcha_check);
          if( $recaptcha_check_response->success === true || $recaptcha_check_response->success === 1 ){
            unset($_POST['g-recaptcha-response']);
            $this->compile();

            $transmit = $this->transmit();
            if($transmit['status'] == "failed"){
              //append data
              $transmit['data']=$_POST;
            }
            return $transmit;
          }else{
            return [
              "status" => "failed",
              "error" => "Please complete the ReCaptcha challenge",
              "data" => $_POST
            ];
          }
        }else{
          //recaptcha check not required
          $this->compile();
          return $this->transmit();
        }


      }else{
        //output error
        return [
          "status" => "failed",
          "error" => "Required Attributes Missing",
          "data" => $_POST
        ];
      }
    }
  }

  private function compile(){
    //compile the data
    $this->compiled="";
    foreach( $_POST as $k => $v ){
      $this->compiled .= "<b>$k</b><br/>$v<br/><br/>";
    }
  }

  private function transmit(){
    //send or save
    switch( $this->method ){
      case "mail":
        return $this->transmit_mail();
      break;

      case "sms":
        return $this->transmit_sms();
      break;

      case "mysqli":
        return $this->transmit_mysqli();
      break;
    }
  }




  private function transmit_mail(){
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try{
      if( is_array($this->get("smtp")) ){
        $config = $this->get("smtp");
        //load smtp
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = $config['Host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['Username'];
        $mail->Password = $config['Password'];
        $mail->SMTPSecure = (isset($config['SSL'])?$config['SSL']:'tls');                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = $config['Port'];
      }

      if( !empty($this->get("from")) ){
        $mail->setFrom($this->get("from")['address'],$this->get("from")['name']);
      }
      if( !empty($this->get("replyto")) ){
        $mail->AddReplyTo($this->get("replyto"));
      }


      if( is_array($this->get("recipient")) ){
        foreach( $this->get("recipient") as $r ){
            $mail->addAddress( $r );
        }
      }else{
        $mail->addAddress( $this->get("recipient") );
      }
      $mail->isHTML(true);
      $mail->Subject = ( !empty($this->get("subject")) ? $this->get("subject") : 'Web Form Message');
      $mail->Body    = $this->compiled;
      $mail->AltBody = strip_tags(str_replace(["<br/>","<br>"],"\n",$this->compiled));
      $mail->send();

      return [
        "status" => "success"
      ];
    }catch(\PHPMailer\PHPMailer\Exception $e){
      return [
        "status" => "failed",
        "error" => $e->errorMessage()
      ];
    }
  }

  private function transmit_sms(){
    $number = $this->get("recipient");
    $config = $this->get("aws");
    $wrapper = new \Teknicode\Aws($config);
    $message = strip_tags(str_replace(["<br/>","<br>"],"\n",$this->compiled));
    return $wrapper->sms($number,$message);
  }

  private function transmit_mysqli(){

    //create mysql connection
    $db_creds = $this->get("mysqli");
    if( empty( $db_creds["table"] ) ){
      return [
        "status" => "failed",
        "error" => "Table name not provided"
      ];
    }

    if( is_array($db_creds) ){
      $db = new \mysqli(
        $db_creds['host'],
        $db_creds['username'],
        $db_creds['password'],
        $db_creds['database']
      );

      $values="";
      foreach( $this->get("values") as $k => $v ){
        $values .= (!empty($values)?",":"")."`$k`='".$db->escape_string($v)."'";
      }

      if( !empty($this->get("id")) ){
        //update
        $save = $db->query( "UPDATE ".$db_creds["table"]." SET ".$values." WHERE id='".$db->escape_string($this->get("id"))."'" );
      }else{
        //insert
        $save = $db->query( "INSERT INTO ".$db_creds["table"]." SET ".$values );
      }

      if( $save ){
        return [
          "status" => "success"
        ];
      }else{
        return [
          "status" => "failed",
          "error" => $db->error
        ];
      }

    }else{
      //return no config error
      return [
        "status" => "failed",
        "error" => "Required MySqli credentials missing"
      ];
    }



  }
}
