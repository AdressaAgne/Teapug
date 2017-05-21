<?php

namespace App\Container\Interfaces;
    
interface StackController {
    
    public function index();
    
    public function item($url);
    
    public function put($data);
    
    public function patch($data);
    
    public function edit($url);
    
    public function delete($data);
}