<?php
namespace Teknicode\Form;
class Select{
    private $attributes = array("class"=>"form-control");
    public $width;
    protected $dbh;
    function __construct($width, $dbh=""){
        if (version_compare(phpversion(), '5.6.0', '<')) {
            die('PHP 5.6.0 required for PHPAuth engine!');
        }
        $this->dbh = $dbh;
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
        
        //$return = '<div class="form-group">'.($this->get('label')?'<label'.($this->get('id')? ' for="'.$this->get('id').'"':'').'>'.$this->get('label').'</label>':'');
        $return = '<div class="form-group input-group '. $this->get('groupClass') .'" '.($this->get('groupId')? ' id='. $this->get('groupId') :'').'>';
        if ($this->get('label')!=""){
            $return .= ($this->get('label')?'<label'.($this->get('id')? ' for="'.$this->get('id').'"':'').'>'.$this->get('label').'</label> ':'');
        }elseif ($this->get('addon-feather')!=""){
            $icons = new \Feather\Icons;
            $icon = $icons->get($this->get('addon-feather'),[],false);
            $return .= '<span class="input-group-text">' . $icon . '</span>';
        }
        unset($this->attributes['label']);
        foreach($this->attributes as $attribute => $value){
            if(!in_array($attribute,["options","value","query"])){
                $html .= (!empty($html)?' ':'').$attribute.'="'.$value.'"';
            }
        }
        
        $return .= '<select '.$html.'>';
        $group_open = false;
        if ($this->get('options')!=""){
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
        }
        if ($this->get('query')!=""){
            $query = $this->dbh->prepare($this->get('query'));
            $query->execute();
            if ($query->rowCount() == 0) {
                $data = [];
            }
            //$data = $query->fetch(\PDO::FETCH_ASSOC);
            while( $row = $query->fetch()) {
                $return .= '<option value="' . $row[1] . '"'.($row[1] == $this->get('value') ? ' selected="selected"' : '').'>' . $row[0] . '</option>';
            }
        }
        $return .= '</select>
        </div>';
        return $return;
    }
}
