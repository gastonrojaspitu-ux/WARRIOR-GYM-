<?php
session_start();

include(__DIR__ . "/../php/conexion.php");

/* PROTEGER ADMIN */
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$sql = "SELECT * FROM solicitudes_membresia ORDER BY id_solicitud DESC";
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

<title>Solicitudes de Membresía - Warrior Gym</title>

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
    padding: 20px;
    text-align: center;
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

.boton-volver {
    background: #d40000;
    color: white;
    padding: 10px 15px;
    border-radius: 5px;
    text-decoration: none;
}

.boton-volver:hover {
    background: #b00000;
    color: white;
}
</style>

</head>

<body>

<div class="header">
    <h1>SOLICITUDES DE MEMBRESÍA</h1>
</div>

<div class="contenido">

    <p>
        <a class="boton-volver" href="dashboard.php">
            ← Volver al Panel
        </a>
    </p>

    <div class="table-box">

        <?php if (mysqli_num_rows($resultado) > 0): ?>

            <div class="table-responsive">

                <table class="table table-dark table-hover text-center align-middle">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Plan Solicitado</th>
                            <th>Fecha Solicitud</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php while($fila = mysqli_fetch_assoc($resultado)): ?>

                            <tr>
                                <td><?= $fila['id_solicitud'] ?></td>
                                <td><?= htmlspecialchars($fila['nombre']) ?></td>
                                <td><?= htmlspecialchars($fila['apellido']) ?></td>
                                <td><?= htmlspecialchars($fila['email']) ?></td>
                                <td><?= htmlspecialchars($fila['telefono']) ?></td>
                                <td><?= htmlspecialchars($fila['plan_solicitado']) ?></td>
                                <td><?= date("d/m/Y", strtotime($fila['fecha_solicitud'])) ?></td>

                                <td>
                                    <?php if ($fila['estado'] == 'Pendiente'): ?>
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    <?php elseif ($fila['estado'] == 'Aprobada'): ?>
                                        <span class="badge bg-success">Aprobada</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Rechazada</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($fila['estado'] == 'Pendiente'): ?>

                                        <a 
                                            href="aprobar.php?id=<?= $fila['id_solicitud'] ?>" 
                                            class="btn btn-success btn-sm"
                                            onclick="return confirm('¿Aprobar esta solicitud?');">
                                            ✔ Aprobar
                                        </a>

                                        <a 
                                            href="rechazar.php?id=<?= $fila['id_solicitud'] ?>" 
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('¿Rechazar esta solicitud?');">
                                            ❌ Rechazar
                                        </a>

                                    <?php else: ?>

                                        <span class="text-secondary">Ya gestionada</span>

                                    <?php endif; ?>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <p class="text-center text-secondary mb-0">
                No hay solicitudes de membresía registradas.
            </p>

        <?php endif; ?>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>