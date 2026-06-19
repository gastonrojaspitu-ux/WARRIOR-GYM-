<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . "/php/conexion.php");

/* 1. RECIBIR JSON */
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data || !isset($data["items"]) || empty($data["items"])) {
    die("NO LLEGA CARRITO");
}

$items = $data["items"];

/* 2. CLIENTE (temporal fijo) */
session_start();

if (!isset($_SESSION['id_cliente'])) {
    die("NO HAY USUARIO LOGUEADO");
}

$id_cliente = $_SESSION['id_cliente'];

/* 3. INSERT VENTA */
$sql = "INSERT INTO ventas (id_cliente, fecha)
        VALUES ('$id_cliente', NOW())";

if (!mysqli_query($conexion, $sql)) {
    die("ERROR VENTA: " . mysqli_error($conexion));
}

$id_venta = mysqli_insert_id($conexion);

/* 4. DETALLE VENTAS */
foreach ($items as $item) {

    $id_producto = (int)$item["id"];
    $cantidad = (int)$item["cantidad"];
    $precio = (float)$item["precio"];

    $sql2 = "INSERT INTO detalle_ventas 
    (id_venta, id_producto, cantidad, precio_unitario)
    VALUES 
    ('$id_venta', '$id_producto', '$cantidad', '$precio')";

    if (!mysqli_query($conexion, $sql2)) {
        die("ERROR DETALLE: " . mysqli_error($conexion));
    }
}

echo "OK";
?>