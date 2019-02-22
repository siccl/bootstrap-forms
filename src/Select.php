<?php
namespace Teknicode\Form;
class Select{
    private $attributes = array("class"=>"form-control");
    public $width;

    function __construct($width=12){
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
            foreach(func_get_args()[0] as  $key => $value){
                if($key == "class")$value = "form-control ".$value;
                $this->attributes[$key]=$value;
            }
        }
        return $this;
    }

    public function get($key){
        return (isset($this->attributes[$key]) ? $this->attributes[$key] : null);
    }

    public function html(){
        $html = '';

        $return = '<div class="form-group">'.($this->get('label')?'<label'.($this->get('id')? ' for="'.$this->get('id').'"':'').'>'.$this->get('label').'</label>':'');

        unset($this->attributes['label']);
        foreach($this->attributes as $attribute => $value){
            if(!in_array($attribute,["options","value"])){
                $html .= (!empty($html)?' ':'').$attribute.'="'.$value.'"';
            }
        }

        $return .= '<select '.$html.'>';
        $group_open = false;
        foreach($this->get('options') as $label => $value){
            if($value === "--group--"){
                $return .= ($group_open == true ? '</optgroup>' : '').'<optgroup label="'.$label.'">';
                $group_open=true;
            }elseif( is_array($value) ){
                $return .= '<option ';
                foreach( $value as $att => $val ){
                    $return .= $att.'="'.$val.'" '.($att == "value" && $val == $this->get('value') ? 'selected="selected" ' : '');
                }
                $return .= '>' . $label . '</option>';
            }else{
                $return .= '<option value="' . $value . '"'.($value == $this->get('value') ? ' selected="selected"' : '').'>' . $label . '</option>';
            }
        }
        $return .= '</select>
        </div>';
        return $return;
    }
}
