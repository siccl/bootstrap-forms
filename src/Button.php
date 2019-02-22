<?php
// New Changes
namespace Teknicode\Form;
class Button{
    private $attributes = array("class"=>"btn");
    public $width;

    function __construct($width){
        $this->width=$width;
    }
    public function set(){
        if(func_num_args() == 2) {
            $key = func_get_arg(0);
            $value = func_get_arg(1);
            if ($key == "class") {
                $value = "form-control " . $value;
            }
            $this->attributes[$key] = $value;
        }else{
            foreach(func_get_args()[0] as $key => $value){
                if($key == "class")$value = "btn ".$value;
                $this->attributes[$key]=$value;
            }
        }
        return $this;
    }

    public function get($key){
        return (!empty($this->attributes[$key]) ? $this->attributes[$key] : null);
    }

    public function html(){
        $atts = '';
        foreach($this->attributes as $attribute => $value){
            if($attribute != "text")$atts .= (!empty($atts)?' ':'').$attribute.'="'.$value.'"';
        }
        return '<button '.$atts.'>'.$this->attributes['text'].'</button>'."\n";
    }
}