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

/* FUNCIÓN SEGURA PARA NOMBRES DE TABLA */
function protegerTabla($tabla) {
    return "`" . str_replace("`", "``", $tabla) . "`";
}

/* OBTENER TODAS LAS TABLAS */
$tablas = [];

$resTablas = mysqli_query($conexion, "SHOW TABLES");

if ($resTablas) {
    while ($fila = mysqli_fetch_array($resTablas)) {
        $tablas[] = $fila[0];
    }
}

$tablaSeleccionada = $_GET['tabla'] ?? "";
$columnas = [];
$registros = [];
$error = "";

/* VALIDAR QUE LA TABLA EXISTA */
if (!empty($tablaSeleccionada)) {

    if (!in_array($tablaSeleccionada, $tablas)) {

        $error = "La tabla seleccionada no existe.";

    } else {

        $tablaSQL = protegerTabla($tablaSeleccionada);

        /* CARGAR COLUMNAS */
        $resColumnas = mysqli_query($conexion, "DESCRIBE $tablaSQL");

        if ($resColumnas) {
            while ($col = mysqli_fetch_assoc($resColumnas)) {
                $columnas[] = $col;
            }
        }

        /* CARGAR REGISTROS */
        $resRegistros = mysqli_query($conexion, "SELECT * FROM $tablaSQL LIMIT 100");

        if ($resRegistros) {
            while ($reg = mysqli_fetch_assoc($resRegistros)) {
                $registros[] = $reg;
            }
        } else {
            $error = "No se pudieron cargar los registros.";
        }
    }
}

/* CONTAR REGISTROS POR TABLA */
function contarRegistros($conexion, $tabla) {
    $tablaSQL = protegerTabla($tabla);
    $res = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM $tablaSQL");

    if ($res) {
        $fila = mysqli_fetch_assoc($res);
        return $fila['total'] ?? 0;
    }

    return 0;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Base de Datos - Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    font-family: Arial, sans-serif;
    background: #111;
    color: white;
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

.contenedor {
    width: 95%;
    margin: auto;
    padding: 30px 0;
}

.card-dark {
    background: #1c1c1c;
    border: 1px solid #333;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
}

.tabla-link {
    display: block;
    background: #222;
    color: white;
    text-decoration: none;
    padding: 12px;
    border-radius: 10px;
    margin-bottom: 10px;
    border: 1px solid #333;
    transition: 0.2s;
}

.tabla-link:hover {
    background: #d40000;
    color: white;
}

.tabla-activa {
    background: #d40000;
    border-color: #ff4444;
}

.table {
    color: white;
}

.table th {
    background: #d40000;
    color: white;
    white-space: nowrap;
}

.table td {
    background: #1c1c1c;
    color: white;
    border-color: #333;
    white-space: nowrap;
}

.badge-total {
    background: #000;
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    float: right;
}

.titulo-seccion {
    color: #dc3545;
    font-weight: bold;
}
</style>

</head>

<body>

<div class="header">
    <h1>🗄 Base de Datos - Warrior Gym</h1>
</div>

<div class="contenedor">

    <div class="mb-3 text-center">
        <a href="dashboard.php" class="btn btn-outline-light">
            ⬅ Volver al Panel
        </a>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="row">

        <!-- LISTA DE TABLAS -->
        <div class="col-md-3">

            <div class="card-dark">

                <h4 class="titulo-seccion mb-3">
                    Tablas del sistema
                </h4>

                <?php if (count($tablas) == 0): ?>

                    <p class="text-secondary">
                        No se encontraron tablas.
                    </p>

                <?php else: ?>

                    <?php foreach ($tablas as $tabla): ?>

                        <a 
                            href="base_datos.php?tabla=<?= urlencode($tabla) ?>" 
                            class="tabla-link <?= ($tablaSeleccionada == $tabla) ? 'tabla-activa' : '' ?>"
                        >
                            <?= htmlspecialchars($tabla) ?>
                            <span class="badge-total">
                                <?= contarRegistros($conexion, $tabla) ?>
                            </span>
                        </a>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </div>

        <!-- DETALLE DE TABLA -->
        <div class="col-md-9">

            <div class="card-dark">

                <?php if (empty($tablaSeleccionada)): ?>

                    <h3 class="titulo-seccion">
                        Seleccioná una tabla
                    </h3>

                    <p class="text-secondary">
                        Desde acá el administrador puede ver las tablas principales de la base de datos del sistema.
                    </p>

                <?php else: ?>

                    <h3 class="titulo-seccion">
                        Tabla: <?= htmlspecialchars($tablaSeleccionada) ?>
                    </h3>

                    <p class="text-secondary">
                        Se muestran hasta 100 registros para revisión administrativa.
                    </p>

                    <h5 class="text-danger mt-4">
                        Estructura de la tabla
                    </h5>

                    <div class="table-responsive mb-4">

                        <table class="table table-bordered table-sm align-middle">

                            <thead>
                                <tr>
                                    <th>Campo</th>
                                    <th>Tipo</th>
                                    <th>Nulo</th>
                                    <th>Clave</th>
                                    <th>Default</th>
                                    <th>Extra</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($columnas as $col): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($col['Field']) ?></td>
                                        <td><?= htmlspecialchars($col['Type']) ?></td>
                                        <td><?= htmlspecialchars($col['Null']) ?></td>
                                        <td><?= htmlspecialchars($col['Key']) ?></td>
                                        <td><?= htmlspecialchars($col['Default'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($col['Extra']) ?></td>
                                    </tr>
                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                    <h5 class="text-danger">
                        Registros
                    </h5>

                    <?php if (count($registros) == 0): ?>

                        <p class="text-secondary">
                            Esta tabla no tiene registros cargados.
                        </p>

                    <?php else: ?>

                        <div class="table-responsive">

                            <table class="table table-bordered table-sm align-middle">

                                <thead>
                                    <tr>
                                        <?php foreach (array_keys($registros[0]) as $campo): ?>
                                            <th><?= htmlspecialchars($campo) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php foreach ($registros as $reg): ?>
                                        <tr>
                                            <?php foreach ($reg as $valor): ?>
                                                <td><?= htmlspecialchars((string)$valor) ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    <?php endif; ?>

                <?php endif; ?>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>