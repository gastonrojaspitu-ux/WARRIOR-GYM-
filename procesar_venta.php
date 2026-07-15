<?php
session_start();
include(__DIR__ . "/php/conexion.php");

header("Content-Type: text/plain; charset=utf-8");

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['id_cliente'])) {
    die("ERROR: usuario no logueado");
}

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'cliente') {
    die("ERROR: acceso no permitido");
}

$id_usuario = intval($_SESSION['id_usuario']);

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['items'])) {
    die("ERROR: carrito vacío");
}
/* VALIDAR DATOS DE PAGO SIMULADO */
if (
    !isset($data['pago']) ||
    !isset($data['pago']['metodo']) ||
    !isset($data['pago']['titular']) ||
    !isset($data['pago']['ultimos_digitos'])
) {
    die("ERROR: faltan datos de pago");
}

$metodo_pago = trim($data['pago']['metodo']);
$titular_pago = trim($data['pago']['titular']);
$ultimos_digitos = trim($data['pago']['ultimos_digitos']);

if ($metodo_pago != "Tarjeta") {
    die("ERROR: método de pago inválido");
}

if (!preg_match("/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/u", $titular_pago)) {
    die("ERROR: titular de tarjeta inválido");
}

if (!preg_match("/^[0-9]{4}$/", $ultimos_digitos)) {
    die("ERROR: datos de tarjeta inválidos");
}

mysqli_begin_transaction($conexion);

try {

    $items_procesados = [];
    $total = 0;

    foreach ($data['items'] as $item) {

        $id_producto = intval($item['id']);
        $cantidad = intval($item['cantidad']);

        if ($id_producto <= 0 || $cantidad <= 0) {
            throw new Exception("Producto o cantidad inválida.");
        }

        /* BUSCAR PRODUCTO REAL EN MYSQL */
        $sqlProducto = "SELECT id_producto, nombre, precio, stock 
                        FROM productos 
                        WHERE id_producto = ?
                        FOR UPDATE";

        $stmtProducto = mysqli_prepare($conexion, $sqlProducto);
        mysqli_stmt_bind_param($stmtProducto, "i", $id_producto);
        mysqli_stmt_execute($stmtProducto);

        $resProducto = mysqli_stmt_get_result($stmtProducto);

        if (mysqli_num_rows($resProducto) == 0) {
            throw new Exception("Producto no encontrado.");
        }

        $producto = mysqli_fetch_assoc($resProducto);

        $precio_real = floatval($producto['precio']);
        $stock_actual = intval($producto['stock']);

        if ($stock_actual < $cantidad) {
            throw new Exception("Stock insuficiente para: " . $producto['nombre']);
        }

        $subtotal = $precio_real * $cantidad;
        $total += $subtotal;

        $items_procesados[] = [
            "id_producto" => $id_producto,
            "cantidad" => $cantidad,
            "precio" => $precio_real
        ];
    }

    if ($total <= 0) {
        throw new Exception("Total inválido.");
    }

    /* CREAR VENTA */
    $sqlVenta = "INSERT INTO ventas (id_usuario, fecha, total)
                 VALUES (?, NOW(), ?)";

    $stmtVenta = mysqli_prepare($conexion, $sqlVenta);
    mysqli_stmt_bind_param($stmtVenta, "id", $id_usuario, $total);
    mysqli_stmt_execute($stmtVenta);

    $id_venta = mysqli_insert_id($conexion);

    if ($id_venta <= 0) {
        throw new Exception("No se pudo registrar la venta.");
    }

    /* CREAR DETALLE Y DESCONTAR STOCK */
    foreach ($items_procesados as $item) {

        $id_producto = $item['id_producto'];
        $cantidad = $item['cantidad'];
        $precio = $item['precio'];

        $sqlDetalle = "INSERT INTO detalle_ventas
                       (id_venta, id_producto, cantidad, precio_unitario)
                       VALUES (?, ?, ?, ?)";

        $stmtDetalle = mysqli_prepare($conexion, $sqlDetalle);
        mysqli_stmt_bind_param($stmtDetalle, "iiid", $id_venta, $id_producto, $cantidad, $precio);
        mysqli_stmt_execute($stmtDetalle);

        $sqlStock = "UPDATE productos
                     SET stock = stock - ?
                     WHERE id_producto = ?";

        $stmtStock = mysqli_prepare($conexion, $sqlStock);
        mysqli_stmt_bind_param($stmtStock, "ii", $cantidad, $id_producto);
        mysqli_stmt_execute($stmtStock);
    }

    mysqli_commit($conexion);

    echo "OK";
    exit();

} catch (Exception $e) {

    mysqli_rollback($conexion);

    echo "ERROR: " . $e->getMessage();
    exit();
}
?>