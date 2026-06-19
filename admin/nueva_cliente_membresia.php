<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: /warrior_gym/admin/login.php");
    exit();
}

/* Clientes */
$clientes = mysqli_query($conexion, "SELECT * FROM clientes");

/* Membresías */
$membresias = mysqli_query($conexion, "SELECT * FROM membresias");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_cliente = $_POST['id_cliente'];
    $id_membresia = $_POST['id_membresia'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    // 🔥 Estado automático
    $fecha_actual = date('Y-m-d');

    if ($fecha_fin >= $fecha_actual) {
        $estado = "Activa";
    } else {
        $estado = "Vencida";
    }

    $sql = "INSERT INTO cliente_membresia 
    (id_cliente, id_membresia, fecha_inicio, fecha_fin, estado)
    VALUES 
    ('$id_cliente', '$id_membresia', '$fecha_inicio', '$fecha_fin', '$estado')";

    mysqli_query($conexion, $sql);

    header("Location: cliente_membresia.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Asignar Membresía</title>

<style>
body{
    font-family: Arial;
    background:#111;
    color:white;
    margin:0;
}

h2{
    text-align:center;
    margin-top:20px;
}

form{
    width:420px;
    margin:40px auto;
    background:#222;
    padding:20px;
    border-radius:10px;
    box-shadow:0 5px 15px rgba(0,0,0,0.5);
}

label{
    display:block;
    margin-top:10px;
    margin-bottom:5px;
}

select, input{
    width:100%;
    padding:10px;
    border:none;
    border-radius:5px;
    margin-bottom:10px;
}

button{
    width:100%;
    padding:12px;
    background:#d40000;
    color:white;
    border:none;
    cursor:pointer;
    border-radius:5px;
    font-size:16px;
}

button:hover{
    background:#ff1a1a;
}
.boton-volver{
    display:inline-block;
    margin:15px;
    background:#444;
    color:white;
    padding:10px 15px;
    border-radius:5px;
    text-decoration:none;
}

.boton-volver:hover{
    background:#d40000;
}
</style>
</head>

<body>
<a href="dashboard.php" class="boton-volver">
    
    
    ⬅ Volver al Panel
</a>
<h2>🏋 Asignar Membresía a Cliente</h2>

<form method="POST">

    <!-- CLIENTE -->
    <label>Cliente</label>
    <select name="id_cliente" required>
        <?php while($c = mysqli_fetch_assoc($clientes)) { ?>
            <option value="<?php echo $c['id_cliente']; ?>">
                <?php echo $c['nombre'] . " " . $c['apellido']; ?>
            </option>
        <?php } ?>
    </select>

    <!-- MEMBRESÍA -->
    <label>Membresía</label>
    <select name="id_membresia" required>
        <?php while($m = mysqli_fetch_assoc($membresias)) { ?>
            <option value="<?php echo $m['id_membresia']; ?>">
                <?php echo $m['nombre']; ?>
            </option>
        <?php } ?>
    </select>

    <!-- FECHAS -->
    <label>Fecha inicio</label>
    <input type="date" name="fecha_inicio" required>

    <label>Fecha fin</label>
    <input type="date" name="fecha_fin" required>

    <button type="submit">Guardar Membresía</button>

</form>

</body>
</html>