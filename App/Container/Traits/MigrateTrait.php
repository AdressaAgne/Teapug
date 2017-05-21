<?php

namespace App\Container\Traits;

use Migrations;

trait MigrateTrait {
    public function migrate(){
        return Migrations::install();
    }
}