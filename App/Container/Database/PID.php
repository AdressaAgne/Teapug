<?php

namespace App\Container\Database;

use DB;

class PID extends DB{
    
    public function toString(){
        return new Row('id', 'int', null, true, true);
    }
    
}