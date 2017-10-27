<?php
//To be included on every page that requires the user to be logged in
//If user is not logged in, redirects them to login page
session_start();
if(empty($_SESSION['username'])){
	session_destroy();
	header("Location: login");
}
?>
