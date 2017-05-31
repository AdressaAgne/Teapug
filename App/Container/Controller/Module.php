<?php
namespace App\Container\Controller;

use DB, Account, User, Config, Direct, View;

class Module extends DB{

	protected $table = null;
	protected $id;

	protected function update(array $values){
		foreach ($values as $key => $value) {
			$this->$key = $value;
		}
		return $this->updateWhere($this->get_table(), $values, ['id' => $this->id]);
	}

	protected function delete(){
		return $this->deleteWhere($this->get_table(), 'id', $this->id);
	}

	private function get_table(){
		if(is_null($this->table)){
			$this->table = explode('\\', static::class);
			$this->table = strtolower(array_pop($this->table));
			$this->table .= "s";
		}

		return $this->table;
	}

	public function __toString(){
		unset($this->tableStatus);
		return json_encode($this);
	}

}