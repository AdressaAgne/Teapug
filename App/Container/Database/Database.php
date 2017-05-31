<?php

namespace App\Container\Database;

use Config, Direct;
use PDO;

class Database extends DBhelpers{
	/**
	 * Select * from class
	 * @param  array  [$rows                  = ['*']]
	 * @return object Table[Row object] Object
	 */
	public static function all($table, $rows, $class = null){
		return self::query('SELECT '.implode(', ', (!is_array($rows) ? ['*'] : $rows)).' FROM '.$table, !is_array($rows) ? $rows : $class)->fetchAll();
	}

	public static function select($table, array $rows = ['*'], $data = null, $join = 'AND', $class = null){

		if(!($join == 'AND' || $join == 'OR')){
			$class = $join;
		}

		$args = null;
		if($data != null){
			$arg = [];
			$args = $data;
			foreach($data as $key => $value){
				$arg[] = "$key = :$key";
			}
			$where = " WHERE ".implode(" $join ", $arg);
		} else {
			$where = "";
		}

		return self::query('SELECT '.implode(', ', $rows).' FROM '.$table.$where, $args, $class);
	}

	/**
	 * Delete a row from a table
	 * @param  string       [$col = 'id']   col name
	 * @param  string       [$val = 0]      Value of col to delete
	 * @param  string       [$table = null] Table name
	 * @return object/false
	 */
	public static function deleteWhere($table, $col = 'id', $val = 0){
		return self::query("DELETE FROM {$table} WHERE {$col} = :val", ['val' => $val]);
	}



	/**
	 * insert many rows to table
	 * @param  array  array $data
	 * @param  string [$table = null] MySQL table
	 * @return boo
	 */
	public static function insert($table, array $data){
		$placeholder = [];
		$insertData = [];
		//INSERT INTO users (name, surname, username)
		// VALUES (:name0, :surname0, :username0),
		//        (:name1, :surname1, :username1)

		$table_rows = is_array(array_values($data)[0]) ? $data[0] : $data;
		$table_rows = implode(", ", array_keys($table_rows));

		foreach($data as $i => $rows){
			$p = [];
			foreach($rows as $key => $row){
				$p[] = ":".$key.$i;
				$insertData[$key.$i] = $row;
			}
			$placeholder[] = '('.implode(", ", $p).')';
		}

		$placeholder = implode(", ", $placeholder);

		$sql = "INSERT INTO {$table} ({$table_rows}) VALUES {$placeholder}";

		$query = self::query($sql, $insertData);

		$id = self::$db->lastInsertId('id');


		return ($id == 0) ? $query : $id;
	}

	/**
	 * Update one row in a table
	 * @author Agne *degaard
	 * @param array $rows
	 * @param string $table
	 * @param array $where
	 * @param string $join = '='
	 */
	public static function updateWhere($table, array $rows, array $where, $join = '=', $wherejoin = 'AND'){
		$data = [];
		$trows = [];
		$twhere = [];
		foreach($rows as $key => $row){
			$trows[] = "$key $join :key_$key";
			$data["key_$key"] = $row;
		}

		foreach($where as $key => $value){
			$twhere[] = "$key $join :where_$key";
			$data["where_$key"] = $value;
		}

		$trows = implode(', ', $trows);
		$twhere = implode(" $wherejoin ", $twhere);
		$sql = "UPDATE {$table} SET {$trows} WHERE {$twhere}";
		return self::query($sql, $data);
	}

}
