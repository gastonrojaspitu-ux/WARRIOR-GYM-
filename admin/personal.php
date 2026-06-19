<?php
session_start();

include("../php/conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: /warrior_gym/admin/login.php");
    exit();
}

$sql = "SELECT 
            p.id_personal,
            p.nombre,
            p.apellido,
            p.usuario,
            p.email,
            p.estado,
            c.nombre_cargo
        FROM personal p
        INNER JOIN personal_cargo pc
            ON p.id_personal = pc.id_personal
        INNER JOIN cargos c
            ON pc.id_cargo = c.id_cargo
        ORDER BY p.id_personal";

$resultado = mysqli_query($conexion, $sql);

if (!$resultado) {
    die("Error SQL: " . mysqli_error($conexion));
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<title>Personal - Warrior Gym</title>

<style>

body{
    font-family: Arial, sans-serif;
    background:#111;
    color:white;
    margin:0;
}

.header{
    background:#d40000;
    padding:20px;
    text-align:center;
}

.contenido{
    padding:30px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:#222;
}

th, td{
    border:1px solid #444;
    padding:12px;
    text-align:center;
}

th{
    background:#d40000;
}

.boton{
    background:#d40000;
    color:white;
    padding:10px 15px;
    border-radius:5px;
    text-decoration:none;
    display:inline-block;
    margin-right:10px;
}

.boton:hover{
    background:#ff1a1a;
}

/* Estado visual */
.activo{
    color:lime;
    font-weight:bold;
}

.inactivo{
    color:red;
    font-weight:bold;
}

</style>

</head>

<body>

<div class="header">
    <h1>👨‍💼 PERSONAL - WARRIOR GYM</h1>
</div>

<div class="contenido">

    <!-- BOTONES -->
    <p>
        <a class="boton" href="dashboard.php">⬅ Volver al Panel</a>
        <a class="boton" href="nuevo_personal.php">➕ Nuevo Personal</a>
    </p>

    <table>

        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Usuario</th>
            <th>Email</th>
            <th>Cargo</th>
            <th>Estado</th>
        </tr>

        <?php while($fila = mysqli_fetch_assoc($resultado)) { ?>

        <tr>
            <td><?php echo $fila['id_personal']; ?></td>
            <td><?php echo $fila['nombre']; ?></td>
            <td><?php echo $fila['apellido']; ?></td>
            <td><?php echo $fila['usuario']; ?></td>
            <td><?php echo $fila['email']; ?></td>
            <td><?php echo $fila['nombre_cargo']; ?></td>

            <!-- ESTADO MEJORADO -->
            <td>
                <?php if($fila['estado'] == 'Activo') { ?>
                    <span class="activo">● Activo</span>
                <?php } else { ?>
                    <span class="inactivo">● Inactivo</span>
                <?php } ?>
            </td>
        </tr>
<td>
    <a class="boton" href="editar_personal.php?id=<?php echo $fila['id_personal']; ?>">
        Editar
    </a>
</td>
<td>
    <a class="boton" 
       href="eliminar_personal.php?id=<?php echo $fila['id_personal']; ?>"
       onclick="return confirm('¿Seguro que quieres eliminar este personal?');">
        Eliminar
    </a>
</td>
        <?php } ?>

    </table>

</div>

</body>
</html>