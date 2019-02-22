<?php
namespace Teknicode\Form;
class Layout{
    private $instance;
    private $columns;

    public function __construct($columns){
        $this->columns=$columns;
        /*if(!isset($this->instance)){
            $this->instance= new Layout($columns);
        }
        return $this->instance;*/
    }

    public function columns(){
        return $this->columns;
    }
}