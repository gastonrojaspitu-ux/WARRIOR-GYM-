<?php
session_start();
include("../php/conexion.php");

session_destroy();

header("Location: http://localhost/warrior_gym/admin/login.php");
exit();
?>