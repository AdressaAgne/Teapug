<?php

namespace App\Container\Helpers;

class Sorting {
    
    public static function pages(array &$array, $order = 'desc') {
        
        if($order == 'desc') return usort($array, function($a, $b) {
            return strcmp($b->arrangement, $a->arrangement);
        });
        
        if($order == 'asc') return usort($array, function($a, $b) {
            return strcmp($a->arrangement, $b->arrangement);
        });
        
    }
}
