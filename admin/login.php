<?php
session_start();

include("../php/conexion.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    $sql = "SELECT * FROM personal 
            WHERE usuario = '$usuario'
            AND contrasena = '$contrasena'
            AND estado = 'Activo'";

    $resultado = mysqli_query($conexion, $sql);

    if (mysqli_num_rows($resultado) == 1) {

        $datos = mysqli_fetch_assoc($resultado);

        $_SESSION['id_personal'] = $datos['id_personal'];
        $_SESSION['usuario'] = $datos['usuario'];

        header("Location: dashboard.php");
        exit();

    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - Warrior Gym</title>

    <style>
        body{
            font-family: Arial, sans-serif;
            background:#111;
            color:white;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
        }

        .login{
            background:#222;
            padding:30px;
            border-radius:10px;
            width:300px;
        }

        input{
            width:100%;
            padding:10px;
            margin:10px 0;
        }

        button{
            width:100%;
            padding:10px;
            background:red;
            color:white;
            border:none;
            cursor:pointer;
        }

        .error{
            color:#ff6666;
        }
    </style>
</head>
<body>

<div class="login">

    <h2>Warrior Gym Admin</h2>

    <?php if($error != ""){ ?>
        <p class="error"><?php echo $error; ?></p>
    <?php } ?>

    <form method="POST">

        <input type="text"
               name="usuario"
               placeholder="Usuario"
               required>

        <input type="password"
               name="contrasena"
               placeholder="Contraseña"
               required>

        <button type="submit">
            Ingresar
        </button>

    </form>

</div>

</body>
</html>