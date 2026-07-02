<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . "/../php/conexion.php");

/* PROTEGER SOLO ADMIN */
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

/* VALIDAR DATOS */
if (!isset($_GET['id']) || empty($_GET['id']) || !isset($_GET['accion']) || empty($_GET['accion'])) {
    header("Location: productos.php");
    exit();
}

$id_producto = intval($_GET['id']);
$accion = $_GET['accion'];

/* DEFINIR CAMBIO DE STOCK */
$cambio = 0;

if ($accion == "sumar") {
    $cambio = 1;
} elseif ($accion == "restar") {
    $cambio = -1;
} elseif ($accion == "sumar5") {
    $cambio = 5;
} elseif ($accion == "restar5") {
    $cambio = -5;
} else {
    header("Location: productos.php");
    exit();
}

mysqli_begin_transaction($conexion);

try {

    /* TRAER PRODUCTO */
    $sqlProducto = "SELECT id_producto, stock 
                    FROM productos 
                    WHERE id_producto = ?
                    LIMIT 1";

    $stmtProducto = mysqli_prepare($conexion, $sqlProducto);
    mysqli_stmt_bind_param($stmtProducto, "i", $id_producto);
    mysqli_stmt_execute($stmtProducto);
    $resProducto = mysqli_stmt_get_result($stmtProducto);

    if (!$resProducto || mysqli_num_rows($resProducto) == 0) {
        throw new Exception("Producto no encontrado.");
    }

    $producto = mysqli_fetch_assoc($resProducto);

    $stock_actual = intval($producto['stock']);
    $stock_nuevo = $stock_actual + $cambio;

    if ($stock_nuevo < 0) {
        $stock_nuevo = 0;
    }

    /* ACTUALIZAR STOCK */
    $sqlUpdate = "UPDATE productos 
                  SET stock = ?
                  WHERE id_producto = ?";

    $stmtUpdate = mysqli_prepare($conexion, $sqlUpdate);
    mysqli_stmt_bind_param($stmtUpdate, "ii", $stock_nuevo, $id_producto);

    if (!mysqli_stmt_execute($stmtUpdate)) {
        throw new Exception("Error al actualizar stock: " . mysqli_error($conexion));
    }

    mysqli_commit($conexion);

    header("Location: productos.php");
    exit();

} catch (Exception $e) {

    mysqli_rollback($conexion);

    echo "<script>
        alert('No se pudo actualizar el stock: " . addslashes($e->getMessage()) . "');
        window.location='productos.php';
    </script>";
    exit();
}
?>