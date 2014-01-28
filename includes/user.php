<?php
require_once(LIB_PATH.DS."database.php");

class User extends DatabaseObject{

	protected static $table_name = "users";
	protected static $db_fields  = array("id", "username", "password", "first_name", "last_name");

	public $id;
	public $username; 
	public $password; 
	public $first_name; 
	public $last_name; 

	public function full_name() {
		if (isset($this->first_name) && isset($this->last_name)) {
			return $this->first_name .  " " . $this->last_name;
		} else {
			return "";
		}
	}

	public static function authenticate($username = "", $password = "") {
		global $database;
		$username = $database->escape_value($username);
		$password = $database->escape_value($password);
		
		$sql  = "SELECT * FROM users ";
		$sql .= "WHERE username = \"{$username}\" ";
		$sql .= "AND password = \"{$password}\" ";
		$sql .= "LIMIT 1";
		$result_array = self::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false;
	}

	// Common database methods
	public static function find_all() {
		// global $database;
		// $result_set = $database->query("Select * FROM users");
		// return self::find_by_sql("SELECT * FROM " .self::$table_name);
		global $database;
		$result_set = $database->prepare('SELECT * from users');
		$result_set->execute();
		return $result_set;

	}

	public static function find_by_id($id = 0) {
		// $result_array = self::find_by_sql("SELECT * FROM " .self::$table_name. " WHERE id={$id} LIMIT 1");
		// return !empty($result_array) ? array_shift($result_array) : false;	
		global $database;
		$result_set = $database->prepare('SELECT * FROM users WHERE id = :id');
		$result_set->bind(':id', '$id');
		$found = $database->fetch_array($result_set);
	}

	public static function find_by_sql($sql = "") {
		global $database;
		$result_set = $database->query($sql);
		$object_array = array();
		while ($row = $database->fetch_array($result_set)) {
			$object_array[] = self::instantiate($row);
		}
		return $object_array;
	}

	public static function count_all() {
		global $database;
		$sql = "SELECT COUNT(*) FROM " . self::$table_name;
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
		return array_shift($row);
	}

	private static function instantiate($record) { // Creates a object and instantiates using static method
		// Could check that $record exists and is an array
		// Simple, long-form approach:
		$object = new self;
		// $object->id 		   = $record["id"];
		// $object->username   = $record["username"];
		// $object->password   = $record["password"];
		// $object->first_name = $record["first_name"];
		// $object->last_name  = $record["last_name"];
		// return $object;
	
		// More dynamic, short-form approach:
		foreach ($record as $attribute => $value) {
			if ($object->has_attribute($attribute)) {
				$object->$attribute = $value;
			}
		}
		return $object;
	}

	private function has_attribute($attribute) {
		// We don't care about the value, we just want ot know if they key exists
		// Wil return true or false
		return array_key_exists($attribute, $this->attributes());
	}

	protected function attributes() {
		// return an array of attribute names and their values
		$attributes = array();
		foreach (self::$db_fields as $field) {
			if (property_exists($this, $field)) {
				$attributes[$field] = $this->$field;
			}
		}
		return $attributes;
	}

	protected function sanitized_attributes() {
		global $database;
		$clean_attributes = array();
		// sanitize the values before submitting 
		// Note: does not alter the acutal value of each attribute
		foreach ($this->attributes() as $key => $value) {
			$clean_attributes[$key] = $database->escape_value($value);
		}
		return $clean_attributes;
	}


	public function save() {
		// A new record won't have an id yet (if there is no update, item will be created vice versa updated etc.)
		return isset($this->id) ? $this->update() : $this->create();
	}

	public function create() {
	global $database;
		// Don't forget your SQL syntax and good habits:
		// - INSERT INTO table (key, key) VALUES ("value", "value")
		// - singe-quotes around all values
		// - escape all values to present SQL injection
		$attributes = $this->sanitized_attributes();
		$sql  = "INSERT INTO ".self::$table_name." (";
		$sql .= join(", ", array_keys($attributes));
		$sql .= ")VALUES('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";
		if($database->query($sql)) {
			$this->id = $database->insert_id();
			return true;
		} else {
			return false;
		}
	}

	public function update() {
		global $database;
		// Don't forget your SQL syntax and good habits:
		// - UPDATE table SET key='value', key='value' WHERE condition
		// - single-quotes around all values
		// - escape all values to prevent SQL injection
		$attributes = $this->sanitized_attributes();
		$attribute_pairs = array();
		foreach ($attributes as $key => $value) {
			$attribute_pairs[] = "{$key}='{$value}'";
		}
		$sql  = "UPDATE ".self::$table_name." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE id=" . $database->escape_value($this->id);
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;
	}

	public function delete() {
		global $database;
		// Don't forget your SQL syntax and good habits:
		// - DELETE FROM table WHERE condition LIMIT 1 
		// - escape all values to prevent SQL injection
		// - use LIMIT 1
		$sql  = "DELETE FROM ".self::$table_name." ";		
		$sql .= "WHERE id=" . $database->escape_value($this->id);
		$sql .= " LIMIT 1";
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;
	}

}
?>
