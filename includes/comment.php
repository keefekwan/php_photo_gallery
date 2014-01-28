<?php
require_once(LIB_PATH.DS."database.php");

class Comment extends DatabaseObject {

	protected static $table_name = "comments";
	protected static $db_fields = array("id", "photograph_id", "created", "author", "body");

	public $id;
	public $photograph_id;
	public $created;
	public $author;
	public $body;

	// "new" is a reserved key word so we use "make" (or "build")
	public static function make($photo_id, $author="Anonymous", $body="") {
		if (!empty($photo_id) && !empty($author) && !empty($body)) {
			$comment = new Comment();
			$comment->photograph_id = (int)$photo_id;
			$comment->created = strftime("%Y-%m-%d %H:%M:%S", time());
			$comment->author = $author;
			$comment->body = $body;		
			return $comment;
		} else {
			return false;
		}
	}

public static function find_comments_on($photo_id=0) {
    global $database;
    $sql = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE photograph_id=" .$database->escape_value($photo_id);
    $sql .= " ORDER BY created ASC";
    return self::find_by_sql($sql);
	}

	public function try_to_send_notification() {
		// PHP SMTP version
		$mail = new PHPMailer();

		$mail->IsSMTP();
		$mail->SMTPAuth   = "true";
		$mail->Host 	  = ""; // e.g. smtp.gmail.com
		$mail->Port 	  = "465";
		$mail->SMTPSecure = "ssl";
		$mail->Username   = ""; // example@example.com
		$mail->Password   = ""; // password
		$mail->FromName   =	("Photo Gallery"); 
		$mail->AddAddress(""); // "example@example.com", "Photo Gallery Admin"
		$mail->AddReplyTo(""); // "example", "john doe"
		$mail->Subject 	  = "New Photo Gallery Comment";
		$created = datetime_to_text($this->created);
		// Using heredoc 
		$mail->Body       =<<<EMAILBODY
A new comment has been recieved in the Photo Gallery.

At {$created}, {$this->author} wrote:

{$this->body}

EMAILBODY;
		
		$result = $mail->Send();
		return $result;
	}

	// Common database methods
	public static function find_all() {
		// global $database;
		// $result_set = $database->query("Select * FROM users");
		return self::find_by_sql("SELECT * FROM " .self::$table_name);
	}

	public static function find_by_id($id = 0) {
		$result_array = self::find_by_sql("SELECT * FROM " .self::$table_name. " WHERE id={$id} LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;	
	}

  public static function find_by_sql($sql="") {
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
		$object_vars = get_object_vars($this);

		return array_key_exists($attribute, $object_vars);
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
		// $attributes = $this->attributes();
		// $sql  = "INSERT INTO ".self::$table_name." (";
		// $sql .= join(", ", array_keys($attributes));
		// $sql .= ")VALUES('";
		// $sql .= join("', '", array_values($attributes));
		// $sql .= "')";
		// $database->query($sql);
		// $database->execute();
		// if($database->query($sql)) {
		// 	$this->id = $database->lastInsertId($sql);
		// 	return true;
		// } else {
		// 	return false;
		// }
		$sql  = "INSERT INTO comments (id, photograph_id, created, author, body) VALUES(?, ?, ?, ?, ?)";
		$database->query($sql);
		$database->bind(1, $this->id);
		$database->bind(2, $this->photograph_id);
		$database->bind(3, $this->created); 
		$database->bind(4, $this->author); 
		$database->bind(5, $this->body);
		$database->execute();
		$this->id = $database->lastInsertId('id');
		echo $this->id;
		if ($database->query($sql)) {
			$this->id = $database->lastInsertId('id');
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
		// $attributes = $this->sanitized_attributes();
		// $attribute_pairs = array();
		// foreach ($attributes as $key => $value) {
		// 	$attribute_pairs[] = "{$key}='{$value}'";
		// }
		// $sql  = "UPDATE ".self::$table_name." SET ";
		// $sql .= join(", ", $attribute_pairs);
		// $sql .= " WHERE id=" . $database->escape_value($this->id);
		// $database->query($sql);
		// return ($database->affected_rows() == 1) ? true : false;
		$sql  = "UPDATE " . self::$table_name . " SET ";
		$sql .= "photograph_id = ?, created = ?, author = ?, body = ? WHERE id = ?";
		$database->query($sql);
		$database->bind(1, $this->photograph_id);
		$database->bind(2, $this->created);
		$database->bind(3, $this->author);
		$database->bind(4, $this->body);
		$database->execute();
		// var_dump($sql);
		return ($database->lastInsertId() == 1) ? true : false;
	}

	public function delete() {
		global $database;
		// Don't forget your SQL syntax and good habits:
		// - DELETE FROM table WHERE condition LIMIT 1 
		// - escape all values to prevent SQL injection
		// - use LIMIT 1
		$query  = "DELETE FROM ".self::$table_name." WHERE id = ? LIMIT 1";		
		// $sql  = $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql  = $database->query($query);
		$sql .= $database->bind(1, $this->id);
		$sql .= $database->execute();

		return ($database->lastInsertId() == 1) ? true : false;
	}

}

?>
