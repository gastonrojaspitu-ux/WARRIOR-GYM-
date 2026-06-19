<?php
session_start();

echo "ID CLIENTE: " . $_SESSION['id_cliente'];
echo "<br>";
echo "USUARIO: " . $_SESSION['usuario'];
?>