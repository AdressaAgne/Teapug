<?php
namespace App\Controllers;

use Controller, Request, View;

class MainController extends Controller {

	use \MigrateTrait;

	public function index(Request $data){
		$user = $this->all('users', 'User');

		return $user;
	}
}
