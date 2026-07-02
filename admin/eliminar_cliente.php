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
    header("Location: clientes.php");
    exit();
}

$id_cliente = intval($_GET['id']);

mysqli_begin_transaction($conexion);

try {

    /* TRAER CLIENTE */
    $sqlCliente = "SELECT id_cliente, id_usuario 
                   FROM clientes 
                   WHERE id_cliente = ?
                   LIMIT 1";

    $stmtCliente = mysqli_prepare($conexion, $sqlCliente);
    mysqli_stmt_bind_param($stmtCliente, "i", $id_cliente);
    mysqli_stmt_execute($stmtCliente);
    $resCliente = mysqli_stmt_get_result($stmtCliente);

    if (!$resCliente || mysqli_num_rows($resCliente) == 0) {
        throw new Exception("Cliente no encontrado.");
    }

    $cliente = mysqli_fetch_assoc($resCliente);

    /* NO BORRAR: SOLO INACTIVAR CLIENTE */
    $sqlUpdateCliente = "UPDATE clientes 
                         SET estado = 'Inactivo'
                         WHERE id_cliente = ?";

    $stmtUpdateCliente = mysqli_prepare($conexion, $sqlUpdateCliente);
    mysqli_stmt_bind_param($stmtUpdateCliente, "i", $id_cliente);

    if (!mysqli_stmt_execute($stmtUpdateCliente)) {
        throw new Exception("Error al inactivar cliente: " . mysqli_error($conexion));
    }

    /* SI TIENE USUARIO VINCULADO, INACTIVAR USUARIO TAMBIÉN */
    if (!empty($cliente['id_usuario'])) {

        $sqlUpdateUsuario = "UPDATE usuarios 
                             SET estado = 'Inactivo'
                             WHERE id_usuario = ?";

        $stmtUpdateUsuario = mysqli_prepare($conexion, $sqlUpdateUsuario);
        mysqli_stmt_bind_param($stmtUpdateUsuario, "i", $cliente['id_usuario']);

        if (!mysqli_stmt_execute($stmtUpdateUsuario)) {
            throw new Exception("Error al inactivar usuario: " . mysqli_error($conexion));
        }
    }

    mysqli_commit($conexion);

    echo "<script>
        alert('Cliente inactivado correctamente.');
        window.location='clientes.php';
    </script>";
    exit();

} catch (Exception $e) {

    mysqli_rollback($conexion);

    echo "<script>
        alert('No se pudo inactivar el cliente: " . addslashes($e->getMessage()) . "');
        window.location='clientes.php';
    </script>";
    exit();
}
?>