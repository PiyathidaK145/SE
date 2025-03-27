<?php 

session_start();
include 'connect.php';

unset($_SESSION[WP . 'checklogin']);
unset($_SESSION[WP . 'member_id']);
unset($_SESSION[WP . 'position_id']);

header("Location: {$base_url}/login.php");
exit();
?>