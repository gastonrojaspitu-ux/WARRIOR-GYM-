<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

/* MES / AÑO */
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('m');
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');

if ($mes < 1) { $mes = 12; $anio--; }
if ($mes > 12) { $mes = 1; $anio++; }

$primer_dia = date("N", strtotime("$anio-$mes-01"));
$dias_mes = date("t", strtotime("$anio-$mes-01"));

/* RESERVAS RESUMEN */
$sql = "SELECT fecha, COUNT(*) as total
        FROM reservas
        WHERE MONTH(fecha) = '$mes'
        AND YEAR(fecha) = '$anio'
        GROUP BY fecha";

$res = mysqli_query($conexion, $sql);

$reservas = [];
while ($row = mysqli_fetch_assoc($res)) {
    $reservas[$row['fecha']] = $row['total'];
}

/* DETALLE DEL DÍA (CLICK) */
$detalle = [];

if (isset($_GET['dia'])) {

    $fecha_click = $_GET['dia'];

    $sqlD = "SELECT r.*, c.nombre, c.apellido, a.nombre AS aparato
             FROM reservas r
             INNER JOIN clientes c ON r.id_cliente = c.id_cliente
             INNER JOIN aparatos a ON r.id_aparato = a.id_aparato
             WHERE r.fecha = '$fecha_click'";

    $resD = mysqli_query($conexion, $sqlD);

    while ($row = mysqli_fetch_assoc($resD)) {
        $detalle[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reservas PRO</title>

<style>
body{
    font-family: Arial;
    background:#111;
    color:white;
}

.contenedor{
    width:95%;
    margin:auto;
}

/* BOTONES */
.boton{
    display:inline-block;
    background:#444;
    color:white;
    padding:10px 15px;
    text-decoration:none;
    border-radius:5px;
    margin:10px 5px;
}

.boton:hover{
    background:#d40000;
}

/* CALENDARIO */
.calendario{
    background:#1a1a1a;
    padding:20px;
    border-radius:12px;
}

table{
    width:100%;
    border-collapse:separate;
    border-spacing:5px;
    text-align:center;
}

td{
    height:70px;
    border-radius:8px;
    vertical-align:top;
    padding:5px;
    cursor:pointer;
}

td:hover{
    transform:scale(1.03);
}

.rojo{
    background:#d40000;
}

.gris{
    background:#2a2a2a;
}

/* DETALLE */
.detalle{
    margin-top:20px;
    background:#222;
    padding:15px;
    border-radius:10px;
}
</style>
</head>

<body>

<div class="contenedor">

<h2 style="text-align:center;">📅 Reservas PRO</h2>

<div style="text-align:center;">
    <a class="boton" href="nueva_reserva.php">➕ Nueva Reserva</a>
    <a class="boton" href="dashboard.php">🏠 Panel</a>
</div>

<!-- NAV -->
<div style="text-align:center;">
    <a class="boton" href="?mes=<?php echo $mes-1; ?>&anio=<?php echo $anio; ?>">⬅ Mes</a>

    <b><?php echo date("F Y", strtotime("$anio-$mes-01")); ?></b>

    <a class="boton" href="?mes=<?php echo $mes+1; ?>&anio=<?php echo $anio; ?>">Mes ➡</a>
</div>

<!-- CALENDARIO -->
<div class="calendario">

<table>

<tr>
<th>Lun</th><th>Mar</th><th>Mié</th><th>Jue</th><th>Vie</th><th>Sáb</th><th>Dom</th>
</tr>

<tr>

<?php
$dia = 1;
$col = 1;

for ($i = 1; $i < $primer_dia; $i++) {
    echo "<td></td>";
    $col++;
}

while ($dia <= $dias_mes) {

    $fecha = sprintf("%04d-%02d-%02d", $anio, $mes, $dia);

    $clase = isset($reservas[$fecha]) ? "rojo" : "gris";

    echo "<td class='$clase' onclick=\"window.location='?mes=$mes&anio=$anio&dia=$fecha'\">";

    echo "<b>$dia</b><br>";

    if (isset($reservas[$fecha])) {
        echo "<span style='font-size:12px;background:#000;padding:3px 6px;border-radius:5px;'>
              {$reservas[$fecha]} reservas
              </span>";
    }

    echo "</td>";

    if ($col % 7 == 0) {
        echo "</tr><tr>";
    }

    $dia++;
    $col++;
}

while ($col % 7 != 1) {
    echo "<td></td>";
    $col++;
}
?>

</tr>
</table>

</div>

<!-- DETALLE DEL DÍA -->
<?php if (isset($_GET['dia'])) { ?>

<div class="detalle">

<h3>📌 Reservas del día: <?php echo $_GET['dia']; ?></h3>

<?php if (count($detalle) == 0) { ?>
    <p>No hay reservas este día.</p>
<?php } ?>

<?php foreach($detalle as $r) { ?>

<div style="background:#333;padding:10px;margin:5px;border-radius:8px;">
    👤 <?php echo $r['nombre']." ".$r['apellido']; ?> |
    🏋 <?php echo $r['aparato']; ?> |
    ⏰ <?php echo $r['hora_inicio']." - ".$r['hora_fin']; ?>
</div>

<?php } ?>

</div>

<?php } ?>

</div>

</body>
</html>