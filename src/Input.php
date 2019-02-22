<?php
namespace Teknicode\Form;
class Input{
    private $attributes = array("class"=>"form-control");
    public $width;

    function __construct($width){
        $this->width=$width;
    }

    public function set(){
        if(func_num_args() == 2) {
            $key = func_get_arg(0);
            $value = func_get_arg(1);
            if ($key == "class" && !in_array($this->get('type'),["radio","checkbox"])) {
                $value = "form-control " . $value;
            }
            $this->attributes[$key] = $value;
        }else{
            foreach(func_get_args()[0] as  $key => $value){
                if($key == "class" && !in_array($this->get('type'),["radio","checkbox"]))$value = "form-control ".$value;
                $this->attributes[$key]=$value;
            }
        }
        return $this;
    }

    public function get($key){
        return (isset($this->attributes[$key]) ? $this->attributes[$key] : null);
    }

    public function html(){
        $atts = '';
        $return = '<div class="form-group">'.($this->get('label')?'<label'.($this->get('id')? ' for="'.$this->get('id').'"':'').'>'.$this->get('label').'</label> ':'');

        if(in_array($this->get('type'),["radio","checkbox"])){
            $this->set("class",str_replace("form-control","",$this->get('class')));
        }

        unset($this->attributes['label']);
        foreach($this->attributes as $attribute => $value){
            if($attribute!="options") {
                if(in_array($this->get('type'),["textarea","radio"]) && in_array($attribute,["value","type"])) continue;
                $atts .= (!empty($atts) ? ' ' : '') . $attribute . '="' . $value . '"';
            }
        }

        if($this->get('type') == "textarea"){
            $return .= '<textarea '.$atts.'>'.$this->get('value').'</textarea>';
        }elseif($this->get('type') == "radio"){
            $return .= '<div class="bg-white p-2">';
            foreach($this->get('options') as $label => $value){
                $return .= $label.' <input type="radio" '.$atts.' value="'.$value.'"'.($this->get("value")==$value?' checked="checked"':'').'/> ';
            }
            $return .= '</div>';
        }else{
            $return .= '<input '.$atts.'/>';
        }


        $return .= '</div>';
        return $return;
    }

}
