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

/* OBTENER ID */
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: personal.php");
    exit();
}

$id_personal = intval($_GET['id']);

/* TRAER TIPOS DE DOCUMENTO */
$tipos_documento = mysqli_query($conexion, "
    SELECT * 
    FROM tipo_documento 
    ORDER BY id_tipo_documento
");

if (!$tipos_documento) {
    die("Error al cargar tipos de documento: " . mysqli_error($conexion));
}

/* TRAER DATOS DEL PERSONAL */
$sqlPersonal = "SELECT * FROM personal WHERE id_personal = ? LIMIT 1";
$stmtPersonal = mysqli_prepare($conexion, $sqlPersonal);
mysqli_stmt_bind_param($stmtPersonal, "i", $id_personal);
mysqli_stmt_execute($stmtPersonal);
$resPersonal = mysqli_stmt_get_result($stmtPersonal);

if (!$resPersonal || mysqli_num_rows($resPersonal) == 0) {
    echo "<script>
        alert('Personal no encontrado.');
        window.location='personal.php';
    </script>";
    exit();
}

$personal = mysqli_fetch_assoc($resPersonal);

/* ACTUALIZAR PERSONAL */
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

    if (
        $id_tipo_documento <= 0 ||
        empty($numero_documento) ||
        empty($nombre) ||
        empty($apellido) ||
        empty($usuario) ||
        empty($estado)
    ) {
        $error = "Completá todos los campos obligatorios.";
    } else {

        /*
            Si escribe una contraseña nueva, se actualiza.
            Si deja el campo vacío, mantiene la contraseña anterior.
        */
        if (!empty($contrasena)) {

            $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

            $sqlUpdate = "UPDATE personal SET
                            id_tipo_documento = ?,
                            numero_documento = ?,
                            nombre = ?,
                            apellido = ?,
                            telefono = ?,
                            email = ?,
                            usuario = ?,
                            contrasena = ?,
                            estado = ?
                          WHERE id_personal = ?";

            $stmtUpdate = mysqli_prepare($conexion, $sqlUpdate);

            mysqli_stmt_bind_param(
                $stmtUpdate,
                "issssssssi",
                $id_tipo_documento,
                $numero_documento,
                $nombre,
                $apellido,
                $telefono,
                $email,
                $usuario,
                $contrasena_hash,
                $estado,
                $id_personal
            );

        } else {

            $sqlUpdate = "UPDATE personal SET
                            id_tipo_documento = ?,
                            numero_documento = ?,
                            nombre = ?,
                            apellido = ?,
                            telefono = ?,
                            email = ?,
                            usuario = ?,
                            estado = ?
                          WHERE id_personal = ?";

            $stmtUpdate = mysqli_prepare($conexion, $sqlUpdate);

            mysqli_stmt_bind_param(
                $stmtUpdate,
                "isssssssi",
                $id_tipo_documento,
                $numero_documento,
                $nombre,
                $apellido,
                $telefono,
                $email,
                $usuario,
                $estado,
                $id_personal
            );
        }

        if (mysqli_stmt_execute($stmtUpdate)) {
            echo "<script>
                alert('Personal actualizado correctamente.');
                window.location='personal.php';
            </script>";
            exit();
        } else {
            $error = "Error al actualizar personal: " . mysqli_error($conexion);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Editar Personal - Warrior Gym</title>

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
    max-width: 650px;
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
    <h1>✏️ Editar Personal</h1>
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
                    <option value="<?= $td['id_tipo_documento'] ?>"
                        <?= $td['id_tipo_documento'] == $personal['id_tipo_documento'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($td['descripcion']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label class="mb-1">Número de documento</label>
            <input 
                type="text" 
                name="numero_documento" 
                class="form-control mb-3" 
                value="<?= htmlspecialchars($personal['numero_documento']) ?>"
                required>

            <label class="mb-1">Nombre</label>
            <input 
                type="text" 
                name="nombre" 
                class="form-control mb-3" 
                value="<?= htmlspecialchars($personal['nombre']) ?>"
                required>

            <label class="mb-1">Apellido</label>
            <input 
                type="text" 
                name="apellido" 
                class="form-control mb-3" 
                value="<?= htmlspecialchars($personal['apellido']) ?>"
                required>

            <label class="mb-1">Teléfono</label>
            <input 
                type="text" 
                name="telefono" 
                class="form-control mb-3" 
                value="<?= htmlspecialchars($personal['telefono'] ?? '') ?>">

            <label class="mb-1">Email</label>
            <input 
                type="email" 
                name="email" 
                class="form-control mb-3" 
                value="<?= htmlspecialchars($personal['email'] ?? '') ?>">

            <label class="mb-1">Usuario</label>
            <input 
                type="text" 
                name="usuario" 
                class="form-control mb-3" 
                value="<?= htmlspecialchars($personal['usuario']) ?>"
                required>

            <label class="mb-1">Nueva contraseña</label>
            <input 
                type="password" 
                name="contrasena" 
                class="form-control mb-2"
                placeholder="Dejar vacío si no querés cambiarla">

            <p class="text-secondary small mb-3">
                Si no escribís una nueva contraseña, se mantiene la anterior.
            </p>

            <label class="mb-1">Estado</label>
            <select name="estado" class="form-select mb-3" required>
                <option value="Activo" <?= $personal['estado'] == 'Activo' ? 'selected' : '' ?>>
                    Activo
                </option>

                <option value="Inactivo" <?= $personal['estado'] == 'Inactivo' ? 'selected' : '' ?>>
                    Inactivo
                </option>
            </select>

            <button type="submit" class="btn btn-warrior w-100">
                Guardar Cambios
            </button>

        </form>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>