<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

/* Traer cargos */
$cargos = mysqli_query($conexion, "SELECT * FROM cargos");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $usuario = $_POST['usuario'];
    $email = $_POST['email'];
    $estado = $_POST['estado'];
    $id_cargo = $_POST['id_cargo'];

    // 1. Insertar personal
    $sql = "INSERT INTO personal (nombre, apellido, usuario, email, estado)
            VALUES ('$nombre', '$apellido', '$usuario', '$email', '$estado')";

    mysqli_query($conexion, $sql);

    // 2. Obtener ID del personal recién creado
    $id_personal = mysqli_insert_id($conexion);

    // 3. Relacionar con cargo
    $sql2 = "INSERT INTO personal_cargo (id_personal, id_cargo)
             VALUES ('$id_personal', '$id_cargo')";

    mysqli_query($conexion, $sql2);

    header("Location: personal.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nuevo Personal</title>

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
    width:400px;
    margin:40px auto;
    background:#222;
    padding:20px;
    border-radius:10px;
}

input, select{
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
</style>
</head>

<body>

<h2>➕ Nuevo Personal</h2>

<form method="POST">

    <input type="text" name="nombre" placeholder="Nombre" required>

    <input type="text" name="apellido" placeholder="Apellido" required>

    <input type="text" name="usuario" placeholder="Usuario" required>

    <input type="email" name="email" placeholder="Email">

    <select name="estado" required>
        <option value="Activo">Activo</option>
        <option value="Inactivo">Inactivo</option>
    </select>

    <label>Cargo</label>
    <select name="id_cargo" required>
        <?php while($c = mysqli_fetch_assoc($cargos)) { ?>
            <option value="<?php echo $c['id_cargo']; ?>">
                <?php echo $c['nombre_cargo']; ?>
            </option>
        <?php } ?>
    </select>

    <button type="submit">Guardar Personal</button>

</form>

</body>
</html>