<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

/* Datos */
$clientes = mysqli_query($conexion, "SELECT * FROM clientes");
$productos = mysqli_query($conexion, "SELECT * FROM productos");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_cliente = $_POST['id_cliente'];
    $id_producto = $_POST['id_producto'];
    $cantidad = $_POST['cantidad'];

    /* PRODUCTO */
    $sqlProd = "SELECT precio, stock FROM productos WHERE id_producto='$id_producto'";
    $resProd = mysqli_query($conexion, $sqlProd);
    $prod = mysqli_fetch_assoc($resProd);

    if (!$prod) {
        die("Producto no encontrado");
    }

    $precio = $prod['precio'];
    $stock = $prod['stock'];

    /* VALIDAR STOCK */
    if ($cantidad > $stock) {
        echo "<script>
            alert('Stock insuficiente');
            window.location='nueva_venta.php';
        </script>";
        exit();
    }

    /* 1. INSERT VENTA (SIN TOTAL) */
    $sqlVenta = "INSERT INTO ventas (id_cliente, fecha)
                 VALUES ('$id_cliente', CURDATE())";

    if (!mysqli_query($conexion, $sqlVenta)) {
        die("Error venta: " . mysqli_error($conexion));
    }

    $id_venta = mysqli_insert_id($conexion);

    /* 2. INSERT DETALLE */
    $sqlDetalle = "INSERT INTO detalle_ventas 
    (id_venta, id_producto, cantidad, precio_unitario)
    VALUES 
    ('$id_venta', '$id_producto', '$cantidad', '$precio')";

    if (!mysqli_query($conexion, $sqlDetalle)) {
        die("Error detalle: " . mysqli_error($conexion));
    }

    /* 3. DESCONTAR STOCK */
    $nuevo_stock = $stock - $cantidad;

    $sqlStock = "UPDATE productos 
                 SET stock='$nuevo_stock' 
                 WHERE id_producto='$id_producto'";

    if (!mysqli_query($conexion, $sqlStock)) {
        die("Error stock: " . mysqli_error($conexion));
    }

    header("Location: ventas.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nueva Venta</title>

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
    margin:20px auto;
    background:#222;
    padding:20px;
    border-radius:10px;
}

select, input{
    width:100%;
    padding:10px;
    margin-bottom:10px;
}

button{
    width:100%;
    padding:12px;
    background:#d40000;
    color:white;
    border:none;
    cursor:pointer;
}

button:hover{
    background:#ff1a1a;
}

.boton{
    display:inline-block;
    background:#444;
    color:white;
    padding:10px 15px;
    text-decoration:none;
    border-radius:5px;
    margin:10px 5px;
}
</style>
</head>

<body>

<h2>💰 Nueva Venta</h2>

<div style="text-align:center;">
    <a class="boton" href="dashboard.php">⬅ Volver al Panel</a>
    <a class="boton" href="ventas.php">📄 Ver Ventas</a>
</div>

<form method="POST">

    <label>Cliente</label>
    <select name="id_cliente" required>
        <?php while($c = mysqli_fetch_assoc($clientes)) { ?>
            <option value="<?php echo $c['id_cliente']; ?>">
                <?php echo $c['nombre']." ".$c['apellido']; ?>
            </option>
        <?php } ?>
    </select>

    <label>Producto</label>
    <select name="id_producto" required>
        <?php while($p = mysqli_fetch_assoc($productos)) { ?>
            <option value="<?php echo $p['id_producto']; ?>">
                <?php echo $p['nombre']; ?> - $<?php echo $p['precio']; ?> (Stock: <?php echo $p['stock']; ?>)
            </option>
        <?php } ?>
    </select>

    <label>Cantidad</label>
    <input type="number" name="cantidad" min="1" required>

    <button type="submit">Registrar Venta</button>

</form>

</body>
</html>