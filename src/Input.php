<?php
// New Changes
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
        if ($this->get('type')=='hidden'){
            return '<input type="hidden" '.
                ($this->get('name')?'name="'.$this->get('name').'" ':'').
                ($this->get('value')?'value="'.$this->get('value').'" ':'').
                ($this->get('id')?'id="'.$this->get('id').'" ':'id="'.$this->get('name').'" ').
                '>'."\n";
        }
        $return = '<div class="form-group input-group '. $this->get('groupClass') .'" '.($this->get('groupId')? ' id='. $this->get('groupId') :'').'>';
        if ($this->get('label')!=""){
            $return .= ($this->get('label')?'<label'.($this->get('id')? ' for="'.$this->get('id').'"':'').'>'.$this->get('label').'</label> ':'');
        }elseif ($this->get('addon-feather')!=""){
            $icons = new \Feather\Icons;
            $icon = $icons->get($this->get('addon-feather'),[],false);
            $return .= '<span class="input-group-text">' . $icon . '</span>';
        }
        //input-group-addon
        if(in_array($this->get('type'),["radio","checkbox"])){
            $this->set("class",str_replace("form-control","form-check",$this->get('class')));
        }
        /// TODO
        /*
         * <div class="form-check form-check-inline">
         * 
         *<div class="form-check"> 
         * 
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
          <label class="form-check-label" for="inlineCheckbox1">1</label>
        </div>
        
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3" disabled>
          <label class="form-check-label" for="inlineRadio3">3 (disabled)</label>
        </div>
        */
        
        unset($this->attributes['label']);
        foreach($this->attributes as $attribute => $value){
            if(!in_array($attribute,["options","addon-feather","groupClass"])) {
                if(in_array($this->get('type'),["textarea","radio"]) && in_array($attribute,["value","type"])) continue;
                $atts .= (!empty($atts) ? ' ' : '') . $attribute . '="' . $value . '" ';
            }
        }
        
        if($this->get('type') == "textarea"){
            $return .= '<textarea '.$atts.'>'.$this->get('value').'</textarea>';
        }elseif($this->get('type') == "radio"){
            $return .= '<div class="bg-white p-2">';
            $rowid = 1;
            foreach($this->get('options') as $label => $value){
                $return .= "\n\t". $label.' <input type="radio" id="' . $this->get('name') . '_' . $rowid . '" ' . $atts . ' value="'.$value.'"'.($this->get("value")==$value?' checked="checked"':'').'/>  ';
                $rowid = $rowid + 1;
            }
            $return .= '</div>'."\n";
        }else{
            $return .= '<input '.$atts.'/>';
        }
        $return .= '</div>'."\n";
        return $return;
    }
    
}
