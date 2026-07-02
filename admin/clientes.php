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

/* LISTAR CLIENTES */
$sql = "
SELECT 
    c.id_cliente,
    c.nombre,
    c.apellido,
    c.numero_documento,
    c.telefono,
    c.email,
    c.usuario,
    c.estado,
    c.fecha_registro,
    c.id_usuario,
    td.descripcion AS tipo_documento,
    u.estado AS estado_usuario
FROM clientes c
INNER JOIN tipo_documento td 
    ON c.id_tipo_documento = td.id_tipo_documento
LEFT JOIN usuarios u 
    ON c.id_usuario = u.id_usuario
ORDER BY c.id_cliente DESC
";

$resultado = mysqli_query($conexion, $sql);

if (!$resultado) {
    die("Error SQL: " . mysqli_error($conexion));
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Clientes - Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    font-family: Arial, sans-serif;
    background: #111;
    color: white;
    margin: 0;
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

.contenido {
    padding: 30px;
}

.table-box {
    background: #1c1c1c;
    padding: 20px;
    border-radius: 15px;
    border: 1px solid #333;
}

.table-dark th {
    background: #d40000;
    color: white;
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

.activo {
    color: #00ff88;
    font-weight: bold;
}

.inactivo {
    color: #ff4d4d;
    font-weight: bold;
}

.suspendido {
    color: #ffcc00;
    font-weight: bold;
}

.vinculado {
    color: #00ff88;
    font-weight: bold;
}

.no-vinculado {
    color: #ff4d4d;
    font-weight: bold;
}
</style>

</head>

<body>

<div class="header">
    <h1>👥 CLIENTES - WARRIOR GYM</h1>
</div>

<div class="contenido">

    <div class="mb-4">
        <a href="dashboard.php" class="btn btn-outline-light">
            ⬅ Volver al Panel
        </a>
    </div>

    <div class="alert alert-warning">
        Los clientes se crean desde <strong>register.php</strong>. 
        Desde este panel solo se visualizan y editan datos básicos para evitar cuentas sin usuario vinculado.
    </div>

    <div class="table-box">

        <h3 class="text-danger mb-4">Listado de Clientes</h3>

        <?php if (mysqli_num_rows($resultado) > 0): ?>

            <div class="table-responsive">

                <table class="table table-dark table-hover text-center align-middle">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Documento</th>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Usuario</th>
                            <th>Registro</th>
                            <th>Vinculación</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php while($fila = mysqli_fetch_assoc($resultado)): ?>

                            <tr>
                                <td><?= $fila['id_cliente'] ?></td>

                                <td>
                                    <?= htmlspecialchars($fila['tipo_documento']) ?>
                                    <?= htmlspecialchars($fila['numero_documento']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($fila['apellido'] . " " . $fila['nombre']) ?>
                                </td>

                                <td>
                                    <?= !empty($fila['telefono']) ? htmlspecialchars($fila['telefono']) : '-' ?>
                                </td>

                                <td>
                                    <?= !empty($fila['email']) ? htmlspecialchars($fila['email']) : '-' ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($fila['usuario']) ?>
                                </td>

                                <td>
                                    <?= !empty($fila['fecha_registro']) ? date("d/m/Y", strtotime($fila['fecha_registro'])) : '-' ?>
                                </td>

                                <td>
                                    <?php if (!empty($fila['id_usuario'])): ?>
                                        <span class="vinculado">● Vinculado</span>
                                    <?php else: ?>
                                        <span class="no-vinculado">● Sin usuario</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if($fila['estado'] == 'Activo'): ?>
                                        <span class="activo">● Activo</span>
                                    <?php elseif($fila['estado'] == 'Suspendido'): ?>
                                        <span class="suspendido">● Suspendido</span>
                                    <?php else: ?>
                                        <span class="inactivo">● Inactivo</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <a 
                                        href="editar_cliente.php?id=<?= $fila['id_cliente'] ?>" 
                                        class="btn btn-warning btn-sm">
                                        Editar
                                    </a>

                                    <a 
    href="eliminar_cliente.php?id=<?= $fila['id_cliente'] ?>" 
    class="btn btn-danger btn-sm"
    onclick="return confirm('¿Seguro que querés inactivar este cliente? No se borrarán sus reservas, pagos ni rutinas.');">
    Inactivar
</a>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <p class="text-secondary text-center">
                No hay clientes registrados todavía.
            </p>

        <?php endif; ?>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>