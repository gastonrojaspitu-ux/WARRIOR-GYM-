<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../php/conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

/* =========================
   CLIENTES
========================= */
$clientes = mysqli_query($conexion, "SELECT id_cliente, nombre, apellido FROM clientes");

/* =========================
   GUARDAR PAGO + SISTEMA CONECTADO
========================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_cliente = intval($_POST['id_cliente']);
    $monto = floatval($_POST['monto']);
    $fecha_pago = $_POST['fecha_pago'];
    $metodo_pago = $_POST['metodo_pago'];
    $concepto = trim($_POST['concepto']);
    $estado = $_POST['estado'];

    if ($id_cliente > 0 && $monto > 0) {

        // 1. GUARDAR PAGO
        $sql = "INSERT INTO pagos 
                (id_cliente, monto, fecha_pago, metodo_pago, concepto, estado)
                VALUES 
                ('$id_cliente', '$monto', '$fecha_pago', '$metodo_pago', '$concepto', '$estado')";

        mysqli_query($conexion, $sql);

        // 2. CONECTAR CON MEMBRESÍA
        $check = "SELECT * FROM cliente_membresia WHERE id_cliente = $id_cliente LIMIT 1";
        $resCheck = mysqli_query($conexion, $check);

        if (mysqli_num_rows($resCheck) > 0) {

            // actualizar estado
            $update = "UPDATE cliente_membresia 
                       SET estado = 'Activa'
                       WHERE id_cliente = $id_cliente";

            mysqli_query($conexion, $update);

        } else {

            // crear membresía automática
            $insert = "INSERT INTO cliente_membresia 
                      (id_cliente, estado)
                      VALUES ($id_cliente, 'Activa')";

            mysqli_query($conexion, $insert);
        }

        echo "<script>
            alert('Pago registrado y membresía actualizada');
            window.location='pagos.php';
        </script>";
        exit();
    }
}

/* =========================
   LISTAR PAGOS
========================= */
$pagos = mysqli_query($conexion, "
SELECT p.*, c.nombre, c.apellido
FROM pagos p
INNER JOIN clientes c ON p.id_cliente = c.id_cliente
ORDER BY p.id_pago DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Sistema de Pagos - Warrior Gym</title>

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
    width:420px;
    margin:20px auto;
    background:#1c1c1c;
    padding:20px;
    border-radius:10px;
}

input, select{
    width:100%;
    padding:10px;
    margin:8px 0;
    border-radius:5px;
    border:none;
}

button{
    width:100%;
    padding:10px;
    background:#28a745;
    border:none;
    color:white;
    cursor:pointer;
    border-radius:5px;
    font-weight:bold;
}

table{
    width:95%;
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

.estado-pagado{
    color:#00ff88;
    font-weight:bold;
}

.estado-pendiente{
    color:#ffcc00;
    font-weight:bold;
}

.badge{
    padding:3px 6px;
    border-radius:4px;
}
</style>
</head>

<body>

<h2>💰 Sistema de Pagos + Membresías</h2>

<!-- ================= FORMULARIO ================= -->
<form method="POST">

    <select name="id_cliente" required>
        <option value="">Seleccionar cliente</option>
        <?php while($c = mysqli_fetch_assoc($clientes)) { ?>
            <option value="<?= $c['id_cliente'] ?>">
                <?= $c['nombre'] . " " . $c['apellido'] ?>
            </option>
        <?php } ?>
    </select>

    <input type="number" name="monto" placeholder="Monto" required>

    <input type="date" name="fecha_pago" required>

    <select name="metodo_pago" required>
        <option value="Efectivo">Efectivo</option>
        <option value="Tarjeta">Tarjeta</option>
        <option value="Transferencia">Transferencia</option>
        <option value="Mercado Pago">Mercado Pago</option>
    </select>

    <input type="text" name="concepto" placeholder="Concepto (membresía, cuota, etc)" required>

    <select name="estado" required>
        <option value="Pagado">Pagado</option>
        <option value="Pendiente">Pendiente</option>
    </select>

    <button type="submit">Registrar Pago</button>

</form>

<!-- ================= LISTADO ================= -->
<h2>📋 Historial de Pagos</h2>

<table>
<tr>
    <th>ID</th>
    <th>Cliente</th>
    <th>Monto</th>
    <th>Fecha</th>
    <th>Método</th>
    <th>Concepto</th>
    <th>Estado</th>
</tr>

<?php while($p = mysqli_fetch_assoc($pagos)) { ?>
<tr>
    <td><?= $p['id_pago'] ?></td>
    <td><?= $p['nombre'] . " " . $p['apellido'] ?></td>
    <td>$<?= $p['monto'] ?></td>
    <td><?= $p['fecha_pago'] ?></td>
    <td><?= $p['metodo_pago'] ?></td>
    <td><?= $p['concepto'] ?></td>
    <td class="<?= $p['estado'] == 'Pagado' ? 'estado-pagado' : 'estado-pendiente' ?>">
        <?= $p['estado'] ?>
    </td>
</tr>
<?php } ?>

</table>

</body>
</html>