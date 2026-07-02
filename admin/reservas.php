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

/* MES / AÑO */
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : intval(date('m'));
$anio = isset($_GET['anio']) ? intval($_GET['anio']) : intval(date('Y'));

if ($mes < 1) {
    $mes = 12;
    $anio--;
}

if ($mes > 12) {
    $mes = 1;
    $anio++;
}

$primer_dia = date("N", strtotime("$anio-$mes-01"));
$dias_mes = date("t", strtotime("$anio-$mes-01"));

$meses_es = [
    1 => "Enero",
    2 => "Febrero",
    3 => "Marzo",
    4 => "Abril",
    5 => "Mayo",
    6 => "Junio",
    7 => "Julio",
    8 => "Agosto",
    9 => "Septiembre",
    10 => "Octubre",
    11 => "Noviembre",
    12 => "Diciembre"
];

/* RESERVAS ACTIVAS O PENDIENTES DEL MES */
$sqlMes = "SELECT r.fecha, COUNT(*) AS total
           FROM reservas r
           INNER JOIN clientes c ON r.id_cliente = c.id_cliente
           WHERE MONTH(r.fecha) = ?
           AND YEAR(r.fecha) = ?
           AND r.estado_reserva IN ('Activa', 'Pendiente')
           GROUP BY r.fecha";

$stmtMes = mysqli_prepare($conexion, $sqlMes);
mysqli_stmt_bind_param($stmtMes, "ii", $mes, $anio);
mysqli_stmt_execute($stmtMes);
$resMes = mysqli_stmt_get_result($stmtMes);

$reservas = [];

while ($row = mysqli_fetch_assoc($resMes)) {
    $reservas[$row['fecha']] = $row['total'];
}

/* DETALLE DEL DÍA */
$detalle = [];
$fecha_click = "";

if (isset($_GET['dia']) && !empty($_GET['dia'])) {

    $fecha_click = $_GET['dia'];

    $sqlDetalle = "SELECT 
                        r.id_reserva,
                        r.fecha,
                        r.hora_inicio,
                        r.hora_fin,
                        r.estado_reserva,
                        c.nombre,
                        c.apellido,
                        a.nombre AS aparato
                   FROM reservas r
                   INNER JOIN clientes c 
                        ON r.id_cliente = c.id_cliente
                   INNER JOIN aparatos a 
                        ON r.id_aparato = a.id_aparato
                   WHERE r.fecha = ?
                   ORDER BY r.hora_inicio ASC";

    $stmtDetalle = mysqli_prepare($conexion, $sqlDetalle);
    mysqli_stmt_bind_param($stmtDetalle, "s", $fecha_click);
    mysqli_stmt_execute($stmtDetalle);
    $resDetalle = mysqli_stmt_get_result($stmtDetalle);

    while ($row = mysqli_fetch_assoc($resDetalle)) {
        $detalle[] = $row;
    }
}

function formatoHora($hora) {
    return date("H:i", strtotime($hora));
}

function formatoFecha($fecha) {
    return date("d/m/Y", strtotime($fecha));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reservas - Admin Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    font-family: Arial, sans-serif;
    background: #111;
    color: white;
}

.contenedor {
    width: 95%;
    margin: auto;
    padding: 30px 0;
}

.titulo {
    text-align: center;
    color: #dc3545;
    font-weight: bold;
    margin-bottom: 20px;
}

.boton {
    display: inline-block;
    background: #444;
    color: white;
    padding: 10px 15px;
    text-decoration: none;
    border-radius: 6px;
    margin: 8px 5px;
}

.boton:hover {
    background: #d40000;
    color: white;
}

.calendario {
    background: #1c1c1c;
    padding: 20px;
    border-radius: 15px;
    border: 1px solid #333;
    margin-top: 20px;
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 6px;
    text-align: center;
}

th {
    color: #dc3545;
    padding: 10px;
}

td {
    height: 85px;
    border-radius: 10px;
    vertical-align: top;
    padding: 8px;
    cursor: pointer;
    transition: 0.2s;
}

td:hover {
    transform: scale(1.03);
}

.dia-con-reserva {
    background: #d40000;
}

.dia-sin-reserva {
    background: #2a2a2a;
}

.dia-vacio {
    background: transparent;
    cursor: default;
}

.dia-vacio:hover {
    transform: none;
}

.badge-reserva {
    font-size: 12px;
    background: #000;
    padding: 4px 7px;
    border-radius: 5px;
    margin-top: 8px;
    display: inline-block;
}

.detalle {
    margin-top: 25px;
    background: #1c1c1c;
    padding: 20px;
    border-radius: 15px;
    border: 1px solid #333;
}

.reserva-card {
    background: #2a2a2a;
    padding: 12px;
    margin-bottom: 10px;
    border-radius: 10px;
    border-left: 5px solid #dc3545;
}

.estado-activa {
    color: #00ff88;
    font-weight: bold;
}

.estado-pendiente {
    color: #ffcc00;
    font-weight: bold;
}

.estado-cancelada {
    color: #ff4d4d;
    font-weight: bold;
}

.estado-finalizada {
    color: #0dcaf0;
    font-weight: bold;
}
</style>
</head>

<body>

<div class="contenedor">

    <h2 class="titulo">📅 Reservas - Warrior Gym</h2>

    <div class="text-center mb-3">
        <a class="boton" href="dashboard.php">
            🏠 Volver al Panel
        </a>

        <a class="boton" href="nueva_reserva.php">
            ➕ Nueva Reserva
        </a>
    </div>

    <div class="text-center mb-3">

        <a class="boton" href="?mes=<?= $mes - 1 ?>&anio=<?= $anio ?>">
            ⬅ Mes anterior
        </a>

        <strong class="fs-4 mx-3">
            <?= $meses_es[$mes] . " " . $anio ?>
        </strong>

        <a class="boton" href="?mes=<?= $mes + 1 ?>&anio=<?= $anio ?>">
            Mes siguiente ➡
        </a>

    </div>

    <div class="calendario">

        <table>

            <tr>
                <th>Lun</th>
                <th>Mar</th>
                <th>Mié</th>
                <th>Jue</th>
                <th>Vie</th>
                <th>Sáb</th>
                <th>Dom</th>
            </tr>

            <tr>

            <?php
            $dia = 1;
            $col = 1;

            for ($i = 1; $i < $primer_dia; $i++) {
                echo "<td class='dia-vacio'></td>";
                $col++;
            }

            while ($dia <= $dias_mes) {

                $fecha = sprintf("%04d-%02d-%02d", $anio, $mes, $dia);
                $clase = isset($reservas[$fecha]) ? "dia-con-reserva" : "dia-sin-reserva";

                echo "<td class='$clase' onclick=\"window.location='?mes=$mes&anio=$anio&dia=$fecha'\">";

                echo "<strong>$dia</strong><br>";

                if (isset($reservas[$fecha])) {
                    echo "<span class='badge-reserva'>{$reservas[$fecha]} reserva/s</span>";
                }

                echo "</td>";

                if ($col % 7 == 0) {
                    echo "</tr><tr>";
                }

                $dia++;
                $col++;
            }

            while ($col % 7 != 1) {
                echo "<td class='dia-vacio'></td>";
                $col++;
            }
            ?>

            </tr>

        </table>

    </div>

    <?php if (!empty($fecha_click)): ?>

        <div class="detalle">

            <h3 class="text-danger mb-3">
                📌 Reservas del día: <?= formatoFecha($fecha_click) ?>
            </h3>

            <?php if (count($detalle) == 0): ?>

                <p class="text-secondary">
                    No hay reservas registradas este día.
                </p>

            <?php else: ?>

                <?php foreach($detalle as $r): ?>

                    <div class="reserva-card">

                        <div>
                            👤 <strong>Cliente:</strong>
                            <?= htmlspecialchars($r['apellido'] . " " . $r['nombre']) ?>
                        </div>

                        <div>
                            🏋 <strong>Aparato:</strong>
                            <?= htmlspecialchars($r['aparato']) ?>
                        </div>

                        <div>
                            ⏰ <strong>Horario:</strong>
                            <?= formatoHora($r['hora_inicio']) ?> - <?= formatoHora($r['hora_fin']) ?>
                        </div>

                        <div>
                            📌 <strong>Estado:</strong>

                            <?php if ($r['estado_reserva'] == 'Activa'): ?>

                                <span class="estado-activa">Activa</span>

                            <?php elseif ($r['estado_reserva'] == 'Pendiente'): ?>

                                <span class="estado-pendiente">Pendiente</span>

                            <?php elseif ($r['estado_reserva'] == 'Finalizada'): ?>

                                <span class="estado-finalizada">Finalizada</span>

                            <?php else: ?>

                                <span class="estado-cancelada">
                                    <?= htmlspecialchars($r['estado_reserva']) ?>
                                </span>

                            <?php endif; ?>
                        </div>

                        <?php if ($r['estado_reserva'] == 'Pendiente'): ?>

                            <div class="mt-3">
                                <a 
                                    href="aprobar_reserva.php?id=<?= $r['id_reserva'] ?>" 
                                    class="btn btn-success btn-sm"
                                    onclick="return confirm('¿Aprobar esta reserva?');">
                                    ✔ Aprobar reserva
                                </a>

                                <a 
                                    href="cancelar_reserva.php?id=<?= $r['id_reserva'] ?>" 
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('¿Cancelar esta reserva?');">
                                    ❌ Cancelar reserva
                                </a>
                            </div>

                        <?php elseif ($r['estado_reserva'] == 'Activa'): ?>

                            <div class="mt-3">
                                <a 
                                    href="cancelar_reserva.php?id=<?= $r['id_reserva'] ?>" 
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('¿Cancelar esta reserva?');">
                                    ❌ Cancelar reserva
                                </a>
                            </div>

                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>