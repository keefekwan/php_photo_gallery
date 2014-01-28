<?php
// Define the core paths
// Define them as absolute paths to make sure that require_once works as expected

// DIRECTORY_SEPARATOR is a PHP pre-defined constant 
// (\ for Windows, / for Unix)
defined("DS") 		 ? null : define("DS", DIRECTORY_SEPARATOR);
defined("SITE_ROOT") ? null : define("SITE_ROOT", DS."xampp".DS."htdocs".DS."projects".DS."kskoglund".DS."photo_gallery");
defined("LIB-PATH")  ? null : define("LIB_PATH", SITE_ROOT.DS."includes");

// load db config file first
require_once(LIB_PATH.DS."db_config.php");

// load basic functions next so that everything can use them
require_once(LIB_PATH.DS."functions.php");

// load core objects
require_once(LIB_PATH.DS."session.php");
require_once(LIB_PATH.DS."database.php");
require_once(LIB_PATH.DS."database_object.php");
require_once(LIB_PATH.DS."pagination.php");
require_once(LIB_PATH.DS."phpMailer".DS."class.phpmailer.php");
require_once(LIB_PATH.DS."phpMailer".DS."class.smtp.php");

// load database-related classes
require_once(LIB_PATH.DS."user.php");
require_once(LIB_PATH.DS."photograph.php");
require_once(LIB_PATH.DS."comment.php");

?>
