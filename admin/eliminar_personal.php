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
    header("Location: personal.php");
    exit();
}

$id_personal = intval($_GET['id']);

/* VERIFICAR QUE EXISTA */
$sqlCheck = "SELECT id_personal FROM personal WHERE id_personal = ? LIMIT 1";

$stmtCheck = mysqli_prepare($conexion, $sqlCheck);
mysqli_stmt_bind_param($stmtCheck, "i", $id_personal);
mysqli_stmt_execute($stmtCheck);

$resCheck = mysqli_stmt_get_result($stmtCheck);

if (!$resCheck || mysqli_num_rows($resCheck) == 0) {
    echo "<script>
        alert('Personal no encontrado.');
        window.location='personal.php';
    </script>";
    exit();
}

/* INACTIVAR EN VEZ DE BORRAR */
$sql = "UPDATE personal 
        SET estado = 'Inactivo'
        WHERE id_personal = ?";

$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_personal);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>
        alert('Personal inactivado correctamente.');
        window.location='personal.php';
    </script>";
    exit();
} else {
    echo "<script>
        alert('No se pudo inactivar el personal.');
        window.location='personal.php';
    </script>";
    exit();
}
?>