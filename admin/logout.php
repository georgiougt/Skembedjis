<?php
// admin/logout.php
// Logout logic

session_start();
$_SESSION = [];
session_destroy();

header("Location: login.php");
exit;
