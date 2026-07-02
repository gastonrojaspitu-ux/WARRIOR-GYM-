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

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);

    if (empty($nombre) || $precio <= 0 || $stock < 0) {

        $error = "Completá correctamente nombre, precio y stock.";

    } else {

        mysqli_begin_transaction($conexion);

        try {

            /* VERIFICAR SI YA EXISTE */
            $sqlCheck = "SELECT id_producto 
                         FROM productos 
                         WHERE nombre = ?
                         LIMIT 1";

            $stmtCheck = mysqli_prepare($conexion, $sqlCheck);
            mysqli_stmt_bind_param($stmtCheck, "s", $nombre);
            mysqli_stmt_execute($stmtCheck);
            $resCheck = mysqli_stmt_get_result($stmtCheck);

            if ($resCheck && mysqli_num_rows($resCheck) > 0) {

                $producto = mysqli_fetch_assoc($resCheck);
                $id_producto = $producto['id_producto'];

                /* SI EXISTE, SUMA STOCK Y ACTUALIZA PRECIO/DESCRIPCIÓN */
                $sqlUpdate = "UPDATE productos 
                              SET descripcion = ?,
                                  precio = ?,
                                  stock = stock + ?
                              WHERE id_producto = ?";

                $stmtUpdate = mysqli_prepare($conexion, $sqlUpdate);
                mysqli_stmt_bind_param($stmtUpdate, "sdii", $descripcion, $precio, $stock, $id_producto);

                if (!mysqli_stmt_execute($stmtUpdate)) {
                    throw new Exception("Error al actualizar producto: " . mysqli_error($conexion));
                }

            } else {

                /* SI NO EXISTE, INSERTA */
                $sqlInsert = "INSERT INTO productos 
                              (nombre, descripcion, precio, stock)
                              VALUES (?, ?, ?, ?)";

                $stmtInsert = mysqli_prepare($conexion, $sqlInsert);
                mysqli_stmt_bind_param($stmtInsert, "ssdi", $nombre, $descripcion, $precio, $stock);

                if (!mysqli_stmt_execute($stmtInsert)) {
                    throw new Exception("Error al crear producto: " . mysqli_error($conexion));
                }
            }

            mysqli_commit($conexion);

            echo "<script>
                alert('Producto guardado correctamente.');
                window.location='productos.php';
            </script>";
            exit();

        } catch (Exception $e) {

            mysqli_rollback($conexion);
            $error = $e->getMessage();
        }
    }
}

function valor($dato) {
    return htmlspecialchars($dato ?? '');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Nuevo Producto - Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #111;
    color: white;
    font-family: Arial, sans-serif;
}

.header {
    background: #d40000;
    padding: 25px;
    text-align: center;
}

.header h1 {
    margin: 0;
    font-weight: bold;
}

.form-box {
    max-width: 600px;
    margin: 40px auto;
    background: #1c1c1c;
    padding: 25px;
    border-radius: 15px;
    border: 1px solid #333;
    box-shadow: 0 0 20px rgba(220,53,69,0.20);
}

.form-control {
    background: #111;
    color: white;
    border: 1px solid #333;
}

.form-control:focus {
    background: #111;
    color: white;
    border-color: #dc3545;
    box-shadow: none;
}

.btn-warrior {
    background: #d40000;
    color: white;
    border: none;
}

.btn-warrior:hover {
    background: #ff1a1a;
    color: white;
}
</style>

</head>

<body>

<div class="header">
    <h1>➕ Nuevo Producto</h1>
</div>

<div class="container">

    <div class="form-box">

        <div class="mb-3">
            <a href="productos.php" class="btn btn-outline-light btn-sm">
                ⬅ Volver a Productos
            </a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label class="mb-1">Nombre del producto</label>
            <input 
                type="text" 
                name="nombre" 
                class="form-control mb-3" 
                placeholder="Ej: Whey Protein Warrior 1kg"
                value="<?= valor($_POST['nombre'] ?? '') ?>"
                required>

            <label class="mb-1">Descripción</label>
            <textarea 
                name="descripcion" 
                class="form-control mb-3" 
                rows="4"
                placeholder="Descripción del producto"><?= valor($_POST['descripcion'] ?? '') ?></textarea>

            <label class="mb-1">Precio</label>
            <input 
                type="number" 
                step="0.01" 
                name="precio" 
                class="form-control mb-3" 
                placeholder="Ej: 25000"
                value="<?= valor($_POST['precio'] ?? '') ?>"
                required>

            <label class="mb-1">Stock</label>
            <input 
                type="number" 
                name="stock" 
                class="form-control mb-3" 
                placeholder="Ej: 10"
                value="<?= valor($_POST['stock'] ?? '') ?>"
                required>

            <button type="submit" class="btn btn-warrior w-100">
                Guardar Producto
            </button>

        </form>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>