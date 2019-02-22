<?php
// New Changes
namespace Teknicode\Form;
class Form{
    private $inputs=[];
    private $form;
    private $layout;

    public function open(){
        $args=func_get_args()[0];
        $html = '<form action="'.(empty($args['action'])? $_SERVER['REQUEST_URI'] : $args['action']).'" method="'.(empty($args['method'])? "post" : $args['method']).'" ';
        foreach( $args as $key => $value ){
            if($key == "class")$value = "row ".$value;
            if(!in_array($key,["action","method"])){
                $html .= $key.'="'.$value.'" ';
            }
        }
        $html .= '>'."\n";
        $this->form = $html;
    }

    public function layout($columns){
        if(!isset($this->layout)){
            $this->layout = new Layout($columns);
        }
        return $this->layout;
    }

    public function input($width=12){
        $i = new Input($width);
        $this->inputs[]=$i;
        return $i;
    }

    public function select($width=12){
        $i = new Select($width);
        $this->inputs[]=$i;
        return $i;
    }

    public function recaptcha($public_key,$private_key=null,$version=2,$groupId=null){
        //todo: use private key here when form and process are merged
        $i = new Recaptcha($public_key,$private_key,$version,$groupId);
        $this->inputs[]=$i;
        return $i;
    }

    public function html($width=12,$content=null){
        $i = new \stdClass();
        $i->width = $width;
        $i->html = $content;
        $this->inputs[]=$i;
        return $i;
    }

    public function button($width=12){
        $i = new Button($width);
        $this->inputs[]=$i;
        return $i;
    }

    public function compile(){
        $this->_compile();

        $this->form .= '</form>'."\n";

        return $this->form;
    }

    private function _compile(){
        foreach($this->inputs as $input){
            //$this->form .= '<div class="col-md-'.$input->width.'">';
            $this->form .= (method_exists($input,"html") ? $input->html() :$input->html );
            //$this->form .= '</div>';
        }
    }
}
