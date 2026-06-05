<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

/* MES Y AÑO ACTUAL */
$mes = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$anio = isset($_GET['anio']) ? $_GET['anio'] : date('Y');

$primer_dia = date("N", strtotime("$anio-$mes-01"));
$dias_mes = date("t", strtotime("$anio-$mes-01"));

/* RESERVAS DEL MES */
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Calendario de Reservas</title>

<style>
body{
    font-family: Arial;
    background:#111;
    color:white;
    text-align:center;
}

.calendario{
    width:90%;
    margin:auto;
}

table{
    width:100%;
    border-collapse:collapse;
    background:#222;
}

th, td{
    border:1px solid #444;
    padding:15px;
    height:80px;
}

th{
    background:#d40000;
}

td:hover{
    background:#333;
}

.reserva{
    background:#d40000;
    color:white;
    border-radius:5px;
    padding:5px;
    font-size:12px;
    display:inline-block;
    margin-top:5px;
}

.nav{
    margin:20px;
}

.nav a{
    color:white;
    text-decoration:none;
    background:#444;
    padding:10px 15px;
    margin:5px;
    border-radius:5px;
}

.nav a:hover{
    background:#d40000;
}
</style>
</head>

<body>

<h2>📅 Calendario de Reservas</h2>

<div class="nav">
    <a href="calendario_reservas.php?mes=<?php echo $mes-1; ?>&anio=<?php echo $anio; ?>">⬅ Mes anterior</a>
    <a href="dashboard.php">🏠 Panel</a>
    <a href="calendario_reservas.php?mes=<?php echo $mes+1; ?>&anio=<?php echo $anio; ?>">Mes siguiente ➡</a>
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

/* ESPACIOS INICIALES */
for ($i = 1; $i < $primer_dia; $i++) {
    echo "<td></td>";
    $col++;
}

/* DÍAS */
while ($dia <= $dias_mes) {

    $fecha = sprintf("%04d-%02d-%02d", $anio, $mes, $dia);

    echo "<td>";

    echo "<b>$dia</b><br>";

    if (isset($reservas[$fecha])) {
        echo "<div class='reserva'>".$reservas[$fecha]." reservas</div>";
    }

    echo "</td>";

    if ($col % 7 == 0) {
        echo "</tr><tr>";
    }

    $dia++;
    $col++;
}

/* COMPLETAR FILA */
while ($col % 7 != 1) {
    echo "<td></td>";
    $col++;
}
?>

</tr>

</table>

</div>

</body>
</html>