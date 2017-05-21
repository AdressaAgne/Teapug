<?php

namespace App\Container\Database;

use DB, Account, Config, Direct;

class Migrations{

	public static function install(){
		//$name, $type, $default = null, $not_null = true, $auto_increment = false)
		
		
		
		$db = new DB();
		
		$db->clearOut(); // delete database

		// User Account
		$db->createTable('users', [
			new PID(),
			new Timestamp(),
			new Row('cookie', 'varchar'),
			new Row('name', 'varchar'),
			new Row('surname', 'varchar'),
			new Row('mail', 'varchar'),
			new Row('password', 'varchar'),
		]);

		 
		self::populate();
		
	
		return [$db->tableStatus];
	}

	public static function populate(){
		$db = new DB();

		$adminId = Account::register('admin', 'admin', 'admin', 'admin@admin.admin');
		
		$db->updateWhere('users',[
			'name' => 'admin',
			'surname' => 'adminsen',
			'approved' => '1',
			'visible' => '0',
			'dob' => '2017-03-16 20:16:28',
			'mobile_phone' => '47343090',
			'type' => '3',
		], ['id' => $adminId]);
		

	}
}
