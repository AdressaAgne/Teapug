<?php

namespace App\Container\Database;

use DB;

class Row extends DB{
    
    public $name;
    public $type;
    public $not_null;
    public $defaults;
    public $auto_increment;
    public $stuff;
    
    
    public function __construct($name, $type, $default = null, $not_null = true, $auto_increment = false, $sql = ''){
        $this->name = $name;
        $this->type = $type;
        $this->defaults = $default;
        $this->not_null = $not_null;
        $this->auto_increment = $auto_increment;
        $this->stuff = $sql;
        
    }
    
    public function null(){
        $this->not_null = false;
    }
    
    public function auto_increment(){
        $this->$auto_increment = true;
    }
    
    public function default($str = null){
        $this->default = $str;
    }
    
    public function toString(){
        $str = "`".$this->name."`";
        $str .= " ".parent::types($this->type)." ";
        $str .= ($this->not_null ? "NOT NULL " : "");
        $str .= (!is_null($this->defaults) ? " DEFAULT '".$this->defaults."' " : "");
        $str .= ($this->auto_increment ? " PRIMARY KEY AUTO_INCREMENT" : "");
        $str .= !empty($this->stuff) ? ' '.$this->stuff : '';
        return $str;
    }
    
    public function __toString(){
        return $this->toString();
    }
    
}