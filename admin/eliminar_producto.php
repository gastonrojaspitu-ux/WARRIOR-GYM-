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

/* VALIDAR ID */
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: productos.php");
    exit();
}

$id_producto = intval($_GET['id']);

mysqli_begin_transaction($conexion);

try {

    /* VERIFICAR SI EXISTE */
    $sqlProducto = "SELECT id_producto 
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

    /*
        Verificar si el producto ya fue vendido.
        Si tu detalle de ventas usa id_producto, esto funciona perfecto.
    */
    $sqlVentas = "SELECT COUNT(*) AS total 
                  FROM detalle_ventas 
                  WHERE id_producto = ?";

    $stmtVentas = mysqli_prepare($conexion, $sqlVentas);
    mysqli_stmt_bind_param($stmtVentas, "i", $id_producto);
    mysqli_stmt_execute($stmtVentas);
    $resVentas = mysqli_stmt_get_result($stmtVentas);
    $ventas = mysqli_fetch_assoc($resVentas);

    if ($ventas['total'] > 0) {

        /*
            No lo borramos porque tiene historial.
            Lo dejamos sin stock para que no se pueda comprar más.
        */
        $sqlStockCero = "UPDATE productos 
                         SET stock = 0 
                         WHERE id_producto = ?";

        $stmtStock = mysqli_prepare($conexion, $sqlStockCero);
        mysqli_stmt_bind_param($stmtStock, "i", $id_producto);

        if (!mysqli_stmt_execute($stmtStock)) {
            throw new Exception("Error al desactivar producto: " . mysqli_error($conexion));
        }

        mysqli_commit($conexion);

        echo "<script>
            alert('El producto tiene ventas registradas. No se borró; se dejó sin stock.');
            window.location='productos.php';
        </script>";
        exit();

    } else {

        /* Si nunca se vendió, se puede borrar */
        $sqlDelete = "DELETE FROM productos 
                      WHERE id_producto = ?";

        $stmtDelete = mysqli_prepare($conexion, $sqlDelete);
        mysqli_stmt_bind_param($stmtDelete, "i", $id_producto);

        if (!mysqli_stmt_execute($stmtDelete)) {
            throw new Exception("Error al eliminar producto: " . mysqli_error($conexion));
        }

        mysqli_commit($conexion);

        echo "<script>
            alert('Producto eliminado correctamente.');
            window.location='productos.php';
        </script>";
        exit();
    }

} catch (Exception $e) {

    mysqli_rollback($conexion);

    echo "<script>
        alert('No se pudo eliminar el producto: " . addslashes($e->getMessage()) . "');
        window.location='productos.php';
    </script>";
    exit();
}
?>