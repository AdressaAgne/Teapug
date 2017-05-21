<?php

namespace App\Container\Database;

use DB;

class Boolean extends DB{
    
    function __construct($name, $default = 0){
        $this->name = $name;
        $this->default = $default;
    }
    
    public function toString(){
        return new Row($this->name, 'bool', $this->default);
    }
    
}