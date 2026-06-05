<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

/* CARGAR CARGOS */
$cargos = mysqli_query($conexion, "SELECT * FROM cargos");

/* OBTENER ID */
$id = $_GET['id'];

/* DATOS DEL PERSONAL */
$sql = "SELECT * FROM personal WHERE id_personal = '$id'";
$resultado = mysqli_query($conexion, $sql);
$personal = mysqli_fetch_assoc($resultado);

/* CARGO ACTUAL */
$sql2 = "SELECT * FROM personal_cargo WHERE id_personal = '$id'";
$res2 = mysqli_query($conexion, $sql2);
$pc = mysqli_fetch_assoc($res2);

$id_cargo_actual = $pc['id_cargo'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $usuario = $_POST['usuario'];
    $email = $_POST['email'];
    $estado = $_POST['estado'];
    $id_cargo = $_POST['id_cargo'];

    /* UPDATE PERSONAL */
    $sql = "UPDATE personal SET 
            nombre='$nombre',
            apellido='$apellido',
            usuario='$usuario',
            email='$email',
            estado='$estado'
            WHERE id_personal='$id'";

    mysqli_query($conexion, $sql);

    /* UPDATE CARGO */
    mysqli_query($conexion, "UPDATE personal_cargo 
        SET id_cargo='$id_cargo' 
        WHERE id_personal='$id'");

    header("Location: personal.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Personal</title>

<style>
body{
    font-family: Arial;
    background:#111;
    color:white;
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

.boton-volver{
    display:inline-block;
    margin:15px;
    background:#444;
    color:white;
    padding:10px;
    text-decoration:none;
    border-radius:5px;
}
</style>
</head>

<body>

<a class="boton-volver" href="personal.php">⬅ Volver</a>

<h2 align="center">✏️ Editar Personal</h2>

<form method="POST">

    <input type="text" name="nombre" value="<?php echo $personal['nombre']; ?>" required>

    <input type="text" name="apellido" value="<?php echo $personal['apellido']; ?>" required>

    <input type="text" name="usuario" value="<?php echo $personal['usuario']; ?>" required>

    <input type="email" name="email" value="<?php echo $personal['email']; ?>">

    <select name="estado">
        <option value="Activo" <?php if($personal['estado']=="Activo") echo "selected"; ?>>
            Activo
        </option>
        <option value="Inactivo" <?php if($personal['estado']=="Inactivo") echo "selected"; ?>>
            Inactivo
        </option>
    </select>

    <label>Cargo</label>
    <select name="id_cargo">
        <?php while($c = mysqli_fetch_assoc($cargos)) { ?>
            <option value="<?php echo $c['id_cargo']; ?>"
                <?php if($c['id_cargo'] == $id_cargo_actual) echo "selected"; ?>>
                <?php echo $c['nombre_cargo']; ?>
            </option>
        <?php } ?>
    </select>

    <button type="submit">Guardar Cambios</button>

</form>

</body>
</html>