<?php
namespace Teknicode\Form;
class Recaptcha{
    public $width=12;
    private $google_recaptcha_version;
    private $public_key;
    private $private_key;

    function __construct($public_key=null,$private_key=null,$version=2){
        $this->public_key=$public_key;
        $this->private_key=$private_key;
        $this->google_recaptcha_version=$version;
    }

    public function html(){
        return '<div class="form-group">
          <div class="g-recaptcha" data-sitekey="'.$this->public_key.'"></div>
        </div>';
    }
}
