<?php
/* Website Configuration File */

$website_title = "Personal Books";
date_default_timezone_set('America/Indianapolis');
$open_registration = TRUE;


/* Database Connection */
$database_host = "127.0.0.1";
$database_user = "root";
$database_pass = "";
$database_name = "personal_books";

$dbconnect = new mysqli($database_host, $database_user, $database_pass, $database_name);
/* Database Connection Trouble shoot

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
else{
  echo $mysqli->host_info . "\n";
}
*/
?>
