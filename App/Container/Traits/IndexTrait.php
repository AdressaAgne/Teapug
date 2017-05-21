<?php

namespace App\Container\Traits;

use View;

trait IndexTrait {
    public function index(){
        return View::make('index');
    }
}