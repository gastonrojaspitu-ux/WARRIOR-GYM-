<?php
session_start();
include(__DIR__ . "/php/conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    die("ERROR: usuario no logueado");
}

$id_usuario = $_SESSION['id_usuario'];

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['items'])) {
    die("Carrito vacío");
}

/* VENTA */
mysqli_query($conexion,
"INSERT INTO ventas (id_usuario, fecha)
VALUES ('$id_usuario', NOW())"
);

$id_venta = mysqli_insert_id($conexion);

/* DETALLE */
foreach ($data['items'] as $item) {

    mysqli_query($conexion,
    "INSERT INTO detalle_ventas
    (id_venta, id_producto, cantidad, precio_unitario)
    VALUES
    ('$id_venta', '{$item['id']}', '{$item['cantidad']}', '{$item['precio']}')"
    );
}

echo "OK";
?>