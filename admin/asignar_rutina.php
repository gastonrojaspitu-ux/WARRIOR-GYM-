<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$clientes = mysqli_query($conexion, "SELECT * FROM clientes");

$id_rutina = isset($_GET['id_rutina']) ? $_GET['id_rutina'] : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Asignar Rutina</title>

<style>
body{
    background:#111;
    color:white;
    font-family:Arial;
}

form{
    width:400px;
    margin:30px auto;
}

select, input{
    width:100%;
    padding:10px;
    margin:8px 0;
}

button{
    width:100%;
    padding:10px;
    background:#28a745;
    border:none;
    color:white;
    cursor:pointer;
}
</style>
</head>

<body>

<h2 align="center">🏋 Asignar Rutina a Cliente</h2>

<form method="POST">

    <select name="id_cliente" required>
        <option value="">Seleccionar cliente</option>
        <?php while($c = mysqli_fetch_assoc($clientes)) { ?>
            <option value="<?= $c['id_cliente'] ?>">
                <?= $c['nombre'] . " " . $c['apellido'] ?>
            </option>
        <?php } ?>
    </select>

    <select name="id_rutina" required>
        <option value="">Seleccionar rutina</option>

        <?php
        $rutinas = mysqli_query($conexion, "SELECT * FROM rutinas");

        while($r = mysqli_fetch_assoc($rutinas)) {
        ?>
            <option value="<?= $r['id_rutina'] ?>"
                <?= ($r['id_rutina'] == $id_rutina) ? 'selected' : '' ?>>
                <?= $r['nombre_rutina'] ?>
            </option>
        <?php } ?>

    </select>

    <input type="date" name="fecha_inicio" required>
    <input type="date" name="fecha_fin" required>

    <button type="submit">Asignar Rutina</button>

</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_cliente = $_POST['id_cliente'];
    $id_rutina = $_POST['id_rutina'];
    $inicio = $_POST['fecha_inicio'];
    $fin = $_POST['fecha_fin'];

    $sql = "INSERT INTO rutinas_cliente (id_cliente, id_rutina, fecha_inicio, fecha_fin)
            VALUES ('$id_cliente', '$id_rutina', '$inicio', '$fin')";

    if (mysqli_query($conexion, $sql)) {
        echo "<script>alert('Rutina asignada correctamente'); window.location='rutinas.php';</script>";
    } else {
        echo mysqli_error($conexion);
    }
}
?>

</body>
</html>