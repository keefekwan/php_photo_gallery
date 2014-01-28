<?php

//Database Constants
defined("DB_SERVER") ? null : define("DB_SERVER", "localhost");
defined("DB_USER")   ? null : define("DB_USER",   "gallery");
defined("DB_PASS")   ? null : define("DB_PASS",   "phpOTL123");
defined("DB_NAME")   ? null : define("DB_NAME",   "photo_gallery");

$connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

?>
