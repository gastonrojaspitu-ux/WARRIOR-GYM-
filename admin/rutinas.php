<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Rutinas</title>

<style>
body{
    background:#111;
    color:white;
    font-family:Arial;
}

h2{
    text-align:center;
}

form{
    width:400px;
    margin:20px auto;
}

input, textarea{
    width:100%;
    padding:10px;
    margin:8px 0;
}

button{
    width:100%;
    padding:10px;
    background:#d40000;
    border:none;
    color:white;
    cursor:pointer;
}

table{
    width:90%;
    margin:20px auto;
    border-collapse:collapse;
}

th, td{
    border:1px solid #444;
    padding:10px;
    text-align:center;
}

th{
    background:#d40000;
}

.btn{
    padding:6px 10px;
    border:none;
    cursor:pointer;
    color:white;
    text-decoration:none;
    display:inline-block;
    margin:2px;
    border-radius:4px;
}

.btn-green{
    background:#28a745;
}

.btn-blue{
    background:#007bff;
}
</style>
</head>

<body>

<h2>🏋 Crear Rutina</h2>

<form method="POST">

    <input type="text" name="nombre_rutina" placeholder="Nombre de rutina" required>

    <textarea name="descripcion" placeholder="Descripción de la rutina" required></textarea>

    <button type="submit">Guardar Rutina</button>

</form>

<?php
// GUARDAR RUTINA
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST['nombre_rutina'];
    $descripcion = $_POST['descripcion'];

    $sql = "INSERT INTO rutinas (id_personal, nombre_rutina, descripcion, fecha_creacion)
            VALUES (1, '$nombre', '$descripcion', CURDATE())";

    mysqli_query($conexion, $sql);

    echo "<script>alert('Rutina creada correctamente'); window.location='rutinas.php';</script>";
}
?>

<hr>

<h2>📋 Rutinas Creadas</h2>

<table>
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Descripción</th>
    <th>Fecha</th>
    <th>Acciones</th>
</tr>

<?php
$sql = "SELECT * FROM rutinas ORDER BY id_rutina DESC";
$res = mysqli_query($conexion, $sql);

while($row = mysqli_fetch_assoc($res)) {
?>
<tr>
    <td><?= $row['id_rutina'] ?></td>
    <td><?= $row['nombre_rutina'] ?></td>
    <td><?= $row['descripcion'] ?></td>
    <td><?= $row['fecha_creacion'] ?></td>

    <td>
        <a class="btn btn-green" href="asignar_rutina.php?id_rutina=<?= $row['id_rutina'] ?>">
            Asignar a cliente
        </a>

        <a class="btn btn-blue" href="rutinas_asignadas.php?id_rutina=<?= $row['id_rutina'] ?>">
            Ver asignaciones
        </a>
    </td>
</tr>
<?php } ?>

</table>

</body>
</html>