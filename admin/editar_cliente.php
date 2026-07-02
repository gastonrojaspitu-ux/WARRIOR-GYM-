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

/* VALIDAR ID */
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: clientes.php");
    exit();
}

$id_cliente = intval($_GET['id']);

/* TRAER TIPOS DE DOCUMENTO */
$tipos_documento = mysqli_query($conexion, "
    SELECT * 
    FROM tipo_documento 
    ORDER BY id_tipo_documento
");

if (!$tipos_documento) {
    die("Error al cargar tipos de documento: " . mysqli_error($conexion));
}

/* TRAER CLIENTE */
$sqlCliente = "
SELECT 
    c.*,
    u.email AS email_usuario,
    u.estado AS estado_usuario
FROM clientes c
LEFT JOIN usuarios u 
    ON c.id_usuario = u.id_usuario
WHERE c.id_cliente = ?
LIMIT 1
";

$stmtCliente = mysqli_prepare($conexion, $sqlCliente);
mysqli_stmt_bind_param($stmtCliente, "i", $id_cliente);
mysqli_stmt_execute($stmtCliente);
$resCliente = mysqli_stmt_get_result($stmtCliente);

if (!$resCliente || mysqli_num_rows($resCliente) == 0) {
    echo "<script>
        alert('Cliente no encontrado.');
        window.location='clientes.php';
    </script>";
    exit();
}

$cliente = mysqli_fetch_assoc($resCliente);

/* GUARDAR CAMBIOS */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_tipo_documento = intval($_POST['id_tipo_documento']);
    $numero_documento = trim($_POST['numero_documento']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $direccion = trim($_POST['direccion']);
    $fecha_nacimiento = trim($_POST['fecha_nacimiento']);
    $estado = trim($_POST['estado']);

    if (
        $id_tipo_documento <= 0 ||
        empty($numero_documento) ||
        empty($nombre) ||
        empty($apellido) ||
        empty($estado)
    ) {
        $error = "Completá todos los campos obligatorios.";
    } else {

        mysqli_begin_transaction($conexion);

        try {

            /* ACTUALIZAR CLIENTE */
            $sqlUpdate = "UPDATE clientes SET
                            id_tipo_documento = ?,
                            numero_documento = ?,
                            nombre = ?,
                            apellido = ?,
                            telefono = ?,
                            email = ?,
                            direccion = ?,
                            fecha_nacimiento = ?,
                            estado = ?
                          WHERE id_cliente = ?";

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
                $direccion,
                $fecha_nacimiento,
                $estado,
                $id_cliente
            );

            if (!mysqli_stmt_execute($stmtUpdate)) {
                throw new Exception("Error al actualizar cliente: " . mysqli_error($conexion));
            }

            /*
                Si el cliente está vinculado a usuarios,
                actualizamos también email y estado del usuario.
                No tocamos contraseña ni rol.
            */
            if (!empty($cliente['id_usuario'])) {

                $estado_usuario = ($estado == "Activo") ? "Activo" : "Inactivo";

                $sqlUsuario = "UPDATE usuarios SET
                                email = ?,
                                estado = ?
                               WHERE id_usuario = ?";

                $stmtUsuario = mysqli_prepare($conexion, $sqlUsuario);

                mysqli_stmt_bind_param(
                    $stmtUsuario,
                    "ssi",
                    $email,
                    $estado_usuario,
                    $cliente['id_usuario']
                );

                if (!mysqli_stmt_execute($stmtUsuario)) {
                    throw new Exception("Error al actualizar usuario vinculado: " . mysqli_error($conexion));
                }
            }

            mysqli_commit($conexion);

            echo "<script>
                alert('Cliente actualizado correctamente.');
                window.location='clientes.php';
            </script>";
            exit();

        } catch (Exception $e) {

            mysqli_rollback($conexion);
            $error = $e->getMessage();
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

<title>Editar Cliente - Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #111;
    color: white;
    font-family: Arial, sans-serif;
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

.info-box {
    background: #111;
    border: 1px solid #333;
    border-radius: 10px;
    padding: 12px;
}
</style>

</head>

<body>

<div class="header">
    <h1>✏️ Editar Cliente</h1>
</div>

<div class="container">

    <div class="form-box">

        <div class="mb-3">
            <a href="clientes.php" class="btn btn-outline-light btn-sm">
                ⬅ Volver a Clientes
            </a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="info-box mb-3">
            <?php if (!empty($cliente['id_usuario'])): ?>
                <span class="text-success fw-bold">● Cliente vinculado a usuario</span>
            <?php else: ?>
                <span class="text-danger fw-bold">● Cliente sin usuario vinculado</span>
                <p class="text-secondary mb-0 mt-1">
                    Este cliente probablemente fue creado antes del nuevo registro. Lo recomendable es no usarlo para login.
                </p>
            <?php endif; ?>
        </div>

        <form method="POST">

            <label class="mb-1">Tipo de documento</label>
            <select name="id_tipo_documento" class="form-select mb-3" required>
                <option value="">Seleccionar tipo</option>

                <?php while($td = mysqli_fetch_assoc($tipos_documento)): ?>
                    <option value="<?= $td['id_tipo_documento'] ?>"
                        <?= $td['id_tipo_documento'] == $cliente['id_tipo_documento'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($td['descripcion']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label class="mb-1">Número de documento</label>
            <input 
                type="text" 
                name="numero_documento" 
                class="form-control mb-3" 
                value="<?= valor($cliente['numero_documento']) ?>"
                required>

            <label class="mb-1">Nombre</label>
            <input 
                type="text" 
                name="nombre" 
                class="form-control mb-3" 
                value="<?= valor($cliente['nombre']) ?>"
                required>

            <label class="mb-1">Apellido</label>
            <input 
                type="text" 
                name="apellido" 
                class="form-control mb-3" 
                value="<?= valor($cliente['apellido']) ?>"
                required>

            <label class="mb-1">Teléfono</label>
            <input 
                type="text" 
                name="telefono" 
                class="form-control mb-3" 
                value="<?= valor($cliente['telefono']) ?>">

            <label class="mb-1">Email</label>
            <input 
                type="email" 
                name="email" 
                class="form-control mb-3" 
                value="<?= valor($cliente['email']) ?>">

            <label class="mb-1">Dirección</label>
            <input 
                type="text" 
                name="direccion" 
                class="form-control mb-3" 
                value="<?= valor($cliente['direccion']) ?>">

            <label class="mb-1">Fecha de nacimiento</label>
            <input 
                type="date" 
                name="fecha_nacimiento" 
                class="form-control mb-3" 
                value="<?= valor($cliente['fecha_nacimiento']) ?>">

            <label class="mb-1">Estado</label>
            <select name="estado" class="form-select mb-3" required>
                <option value="Activo" <?= $cliente['estado'] == 'Activo' ? 'selected' : '' ?>>
                    Activo
                </option>

                <option value="Inactivo" <?= $cliente['estado'] == 'Inactivo' ? 'selected' : '' ?>>
                    Inactivo
                </option>

                <option value="Suspendido" <?= $cliente['estado'] == 'Suspendido' ? 'selected' : '' ?>>
                    Suspendido
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