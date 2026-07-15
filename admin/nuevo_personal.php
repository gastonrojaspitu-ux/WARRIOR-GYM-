<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . "/../php/conexion.php");

/* PROTEGER SOLO ADMIN */
if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$error = "";

/* TRAER TIPOS DE DOCUMENTO */
$tipos_documento = mysqli_query($conexion, "
    SELECT * 
    FROM tipo_documento 
    ORDER BY id_tipo_documento
");

if (!$tipos_documento) {
    die("Error al cargar tipos de documento: " . mysqli_error($conexion));
}

/* GUARDAR PERSONAL */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_tipo_documento = intval($_POST['id_tipo_documento']);
    $numero_documento = trim($_POST['numero_documento']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $usuario = trim($_POST['usuario']);
    $contrasena = trim($_POST['contrasena']);
    $estado = trim($_POST['estado']);

    if ($id_tipo_documento <= 0 || $numero_documento == "" || $nombre == "" || $apellido == "" || $usuario == "" || $contrasena == "" || $estado == "") {

        $error = "Completá todos los campos obligatorios.";

    } else {

        /* VALIDAR DOCUMENTO REPETIDO */
       $sqlDoc = "SELECT id_personal 
           FROM personal 
           WHERE id_tipo_documento = ? 
           AND numero_documento = ? 
           LIMIT 1";

$stmtDoc = mysqli_prepare($conexion, $sqlDoc);
mysqli_stmt_bind_param($stmtDoc, "is", $id_tipo_documento, $numero_documento);
mysqli_stmt_execute($stmtDoc);
$resDoc = mysqli_stmt_get_result($stmtDoc);

        if ($resDoc && mysqli_num_rows($resDoc) > 0) {

            $error = "Ya existe un personal con ese número de documento.";

        } else {

            /* VALIDAR USUARIO REPETIDO */
            $sqlUsuario = "SELECT id_personal FROM personal WHERE usuario = '$usuario' LIMIT 1";
            $resUsuario = mysqli_query($conexion, $sqlUsuario);

            if ($resUsuario && mysqli_num_rows($resUsuario) > 0) {

                $error = "Ya existe un personal con ese usuario.";

            } else {

                /* VALIDAR EMAIL REPETIDO SOLO SI SE CARGÓ */
                if ($email != "") {
                    $sqlEmail = "SELECT id_personal FROM personal WHERE email = '$email' LIMIT 1";
                    $resEmail = mysqli_query($conexion, $sqlEmail);

                    if ($resEmail && mysqli_num_rows($resEmail) > 0) {
                        $error = "Ya existe un personal con ese email.";
                    }
                }

                if ($error == "") {

                    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

                    $sql = "INSERT INTO personal 
                    (id_tipo_documento, numero_documento, nombre, apellido, telefono, email, usuario, contrasena, estado)
                    VALUES 
                    ('$id_tipo_documento', '$numero_documento', '$nombre', '$apellido', '$telefono', '$email', '$usuario', '$contrasena_hash', '$estado')";

                    if (mysqli_query($conexion, $sql)) {
                        echo "<script>
                            alert('Personal registrado correctamente.');
                            window.location='personal.php';
                        </script>";
                        exit();
                    } else {
                        $error = "Error al registrar personal: " . mysqli_error($conexion);
                    }
                }
            }
        }
    }
}

function valor($dato) {
    return htmlspecialchars($dato ?? '');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Nuevo Personal - Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    font-family: Arial, sans-serif;
    background: #111;
    color: white;
    margin: 0;
}

.header {
    background: #d40000;
    padding: 25px;
    text-align: center;
}

.header h1 {
    margin: 0;
    font-weight: bold;
}

.form-box {
    max-width: 600px;
    margin: 40px auto;
    background: #1c1c1c;
    padding: 25px;
    border-radius: 15px;
    border: 1px solid #333;
    box-shadow: 0 0 20px rgba(220,53,69,0.20);
}

.form-control,
.form-select {
    background: #111;
    color: white;
    border: 1px solid #333;
}

.form-control:focus,
.form-select:focus {
    background: #111;
    color: white;
    border-color: #dc3545;
    box-shadow: none;
}

.btn-warrior {
    background: #d40000;
    color: white;
    border: none;
}

.btn-warrior:hover {
    background: #ff1a1a;
    color: white;
}
</style>
</head>

<body>

<div class="header">
    <h1>➕ Nuevo Entrenador / Personal</h1>
</div>

<div class="container">

    <div class="form-box">

        <div class="mb-3">
            <a href="personal.php" class="btn btn-outline-light btn-sm">
                ⬅ Volver a Personal
            </a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label class="mb-1">Tipo de documento</label>
            <select name="id_tipo_documento" class="form-select mb-3" required>
                <option value="">Seleccionar tipo</option>

                <?php while($td = mysqli_fetch_assoc($tipos_documento)): ?>
                    <option value="<?= $td['id_tipo_documento'] ?>" <?= (isset($_POST['id_tipo_documento']) && $_POST['id_tipo_documento'] == $td['id_tipo_documento']) ? "selected" : "" ?>>
                        <?= htmlspecialchars($td['descripcion']) ?>
               </option>
                <?php endwhile; ?>
            </select>

            <label class="mb-1">Número de documento</label>
            <input type="text" name="numero_documento" class="form-control mb-3" placeholder="Ej: 45123456" value="<?= valor($_POST['numero_documento'] ?? '') ?>" required>

            <label class="mb-1">Nombre</label>
            <input type="text" name="nombre" class="form-control mb-3" placeholder="Nombre" value="<?= valor($_POST['nombre'] ?? '') ?>" required>

            <label class="mb-1">Apellido</label>
            <input type="text" name="apellido" class="form-control mb-3" placeholder="Apellido" value="<?= valor($_POST['apellido'] ?? '') ?>" required>

            <label class="mb-1">Teléfono</label>
            <input type="text" name="telefono" class="form-control mb-3" placeholder="Teléfono" value="<?= valor($_POST['telefono'] ?? '') ?>">

            <label class="mb-1">Email</label>
            <input type="email" name="email" class="form-control mb-3" placeholder="Email" value="<?= valor($_POST['email'] ?? '') ?>">

            <label class="mb-1">Usuario</label>
            <input type="text" name="usuario" class="form-control mb-3" placeholder="Usuario" value="<?= valor($_POST['usuario'] ?? '') ?>" required>

            <label class="mb-1">Contraseña</label>
            <input type="password" name="contrasena" class="form-control mb-3" placeholder="Contraseña" required>

            <label class="mb-1">Estado</label>
            <select name="estado" class="form-select mb-3" required>
                <option value="Activo" <?= (($_POST['estado'] ?? 'Activo') == 'Activo') ? "selected" : "" ?>>Activo</option>
                <option value="Inactivo" <?= (($_POST['estado'] ?? '') == 'Inactivo') ? "selected" : "" ?>>Inactivo</option>
            </select>

            <button type="submit" class="btn btn-warrior w-100">
               Guardar Entrenador / Personal
            </button>

        </form>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>