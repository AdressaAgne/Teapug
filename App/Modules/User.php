<?php

namespace App\Modules;

use DB, Module;


class User extends Module {

	private $password;
	private $approved;
	private $visible;

	public function __construct($id = null){

	}

	public function changePassword($pw){
		$this->update(['password' => bcrypt($pw)]);
	}


}