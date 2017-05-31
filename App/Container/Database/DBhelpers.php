<?php

namespace App\Container\Database;

use Config;
use PDO;

class DBhelpers{
	public static $db;

	/**
	 * Init Database connection
	 * @public
	 */
	public function __construct(){
		try {
			$dns = 'mysql:host='.Config::$host.';dbname='.Config::$database;
			self::$db = new PDO($dns, Config::$username, Config::$password);

			self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			self::$db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

		} catch (PDOException $e) {
			 die('Could not connect to Database'. $e);
		}
	}

	/**
	 * Bind Values to PDO prepare
	 * @param object &$query
	 * @param array  &$args
	 */
	protected static function arrayBinder(&$query, &$args) {
		foreach ($args as $key => $value) {
			$query->bindValue(':'.$key, htmlspecialchars($value));
		}
	}

	/**
	 * Execute a PDO mysql Query
	 * @param  string   $sql
	 * @param  array    [$args = null]
	 * @return object
	 */
	public static function query($sql, $args = null, $class = null){
		$query = self::$db->prepare($sql);
		if($class !== null || gettype($args) == 'string'){
			$class = ($class == null) ? $args : $class;
			$query->setFetchMode(PDO::FETCH_CLASS, $class);
		}
		if($args !== null && gettype($args) == 'array'){
			self::arrayBinder($query, $args);
		}
		return $query->execute() ? $query : false;
	}

	/**
	 * clear a table
	 * @param  string  $table MySQL table
	 * @return boolean
	 */
	public static function clearTable($table){
		 return self::query("DELETE from $table");
	}

	/**
	 * clear the database
	 * @author Agne *degaard
	 * @return boolean [[Description]]
	 */
	public function clearOut(){
		$this->query('DROP DATABASE IF EXISTS '.Config::$database);

		if($this->query('CREATE DATABASE IF NOT EXISTS `'.Config::$database.'` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci; USE `'.Config::$database.'`;')){
			return true;
		}
	}

	/**
	 * create a new table
	 * @param  string  $name table name
	 * @param  array   $rows arrow of Row objects
	 * @return boolean
	 */
	public $tableStatus = [];
	public function createTable($table, array $rows, $drop = true){
		$query = "";
		if($drop) {
			$this->query("DROP TABLE IF EXISTS `".$table."`");
			$query .= "CREATE TABLE ";
		} else {
			$query .= "CREATE TABLE IF NOT EXISTS ";
		}
		$query .= "`".$table."` (";
		$row_arr = [];
		foreach($rows as $key => $row){
			 $row_arr[] = $row->toString();
		}

		$query .= implode(", ", $row_arr);
		$query .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$return = $this->query($query);
		$this->tableStatus[] = $return;
		return $return;
	}

	public function addConstraint(){
		
	}

	/**
	 * covert variables types to sql variable types
	 * @author Agne *degaard
	 * @param  string $type
	 * @return string string
	 */
	protected static function types($type){
		$types = [
			'int'       => 'int(11)',
			'varchar'   => 'varchar(255)',
			'var'       => 'varchar(255)',
			'tinyint'   => 'tinyint(1)',
			'boolean'   => 'tinyint(1)',
			'bool'      => 'tinyint(1)',
		];
		return array_key_exists($type, $types) ? $types[$type] : $type;
	}

	/**
	 * Delete table
	 * @author Ø
	 * @param  string $name
	 * @return true/false
	 */
	 public function deleteTable($table){
		 return self::$db->query('DROP TABLE IF EXISTS '.$table);
	 }

}