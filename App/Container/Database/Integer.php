<?php

namespace App\Container\Database;

use DB;

class Integer extends DB{
    
    function __construct($name, $default = null){
        $this->name = $name;
        $this->default = $default;
    }
    
    public function toString(){
        return new Row($this->name, 'int', $this->default);
    }
    
}