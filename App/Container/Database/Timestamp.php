<?php

namespace App\Container\Database;

use DB;

class Timestamp extends DB{
    
    public function toString(){
        return "`time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
    }
    
}