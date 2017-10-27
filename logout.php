<?php
require_once 'php/session_check.php';
session_destroy();
header("Location: login"); 
?>
