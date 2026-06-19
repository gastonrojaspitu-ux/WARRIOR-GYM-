<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: /warrior_gym/admin/login.php");
    exit();
}

$clientes = mysqli_query($conexion, "SELECT * FROM clientes");
$aparatos = mysqli_query($conexion, "SELECT * FROM aparatos");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_cliente = $_POST['id_cliente'];
    $id_aparato = $_POST['id_aparato'];
    $fecha = $_POST['fecha'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fin = $_POST['hora_fin'];
    $estado = $_POST['estado_reserva'];

    /* 🔥 VALIDAR SOLAPAMIENTO DE HORARIOS */
    $sqlCheck = "SELECT * FROM reservas
                 WHERE id_aparato = '$id_aparato'
                 AND fecha = '$fecha'
                 AND (
                    (hora_inicio < '$hora_fin' AND hora_fin > '$hora_inicio')
                 )";

    $resCheck = mysqli_query($conexion, $sqlCheck);

    if (mysqli_num_rows($resCheck) > 0) {
        echo "<script>
            alert('❌ Este aparato ya está reservado en ese horario');
            window.location='nueva_reserva.php';
        </script>";
        exit();
    }

    /* INSERT RESERVA */
    $sql = "INSERT INTO reservas 
    (id_cliente, id_aparato, fecha, hora_inicio, hora_fin, estado_reserva)
    VALUES 
    ('$id_cliente', '$id_aparato', '$fecha', '$hora_inicio', '$hora_fin', '$estado')";

    if (!mysqli_query($conexion, $sql)) {
        die("Error: " . mysqli_error($conexion));
    }

    header("Location: reservas.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nueva Reserva</title>

<style>
body{
    font-family: Arial;
    background:#111;
    color:white;
}

h2{
    text-align:center;
}

form{
    width:420px;
    margin:20px auto;
    background:#222;
    padding:20px;
    border-radius:10px;
}

select, input{
    width:100%;
    padding:10px;
    margin-bottom:10px;
}

button{
    width:100%;
    padding:10px;
    background:#d40000;
    color:white;
    border:none;
    cursor:pointer;
}

button:hover{
    background:#ff1a1a;
}

.boton{
    display:inline-block;
    background:#444;
    color:white;
    padding:10px 15px;
    text-decoration:none;
    border-radius:5px;
    margin:10px 5px;
}
</style>
</head>

<body>

<h2>📅 Nueva Reserva</h2>

<div style="text-align:center;">
    <a class="boton" href="reservas.php">⬅ Volver</a>
    <a class="boton" href="dashboard.php">🏠 Panel</a>
</div>

<form method="POST">

    <label>Cliente</label>
    <select name="id_cliente" required>
        <?php while($c = mysqli_fetch_assoc($clientes)) { ?>
            <option value="<?php echo $c['id_cliente']; ?>">
                <?php echo $c['nombre']." ".$c['apellido']; ?>
            </option>
        <?php } ?>
    </select>

    <label>Aparato</label>
    <select name="id_aparato" required>
        <?php while($a = mysqli_fetch_assoc($aparatos)) { ?>
            <option value="<?php echo $a['id_aparato']; ?>">
                <?php echo $a['nombre']; ?>
            </option>
        <?php } ?>
    </select>

    <label>Fecha</label>
    <input type="date" name="fecha" required>

    <label>Hora inicio</label>
    <input type="time" name="hora_inicio" required>

    <label>Hora fin</label>
    <input type="time" name="hora_fin" required>

    <label>Estado</label>
    <select name="estado_reserva">
        <option value="Activa">Activa</option>
        <option value="Cancelada">Cancelada</option>
        <option value="Finalizada">Finalizada</option>
    </select>

    <button type="submit">Guardar Reserva</button>

</form>

</body>
</html>