<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . "/php/conexion.php");
$cliente_logueado = false;

if (
    isset($_SESSION['id_usuario']) &&
    isset($_SESSION['id_cliente']) &&
    isset($_SESSION['rol']) &&
    $_SESSION['rol'] == 'cliente'
) {
    $cliente_logueado = true;
}

$sql = "SELECT * FROM productos ORDER BY id_producto";
$resultado = mysqli_query($conexion, $sql);

if (!$resultado) {
    die("Error SQL: " . mysqli_error($conexion));
}

$mapaImagenes = [
    "Whey Protein Warrior 1kg" => "whey1kg.png",
    "Whey Protein Warrior 2kg" => "whey2kg.png",
    "Isolate Protein Premium" => "isolate.jpeg",
    "Mass Gainer 3kg" => "massgainer.jpeg",
    "Creatina Monohidratada 300g" => "creatina300.jpeg",
    "Creatina Monohidratada 500g" => "creatina500.jpeg",
    "Creatina Micronizada Premium" => "creatina_micronizada.jpeg",
    "Pre Workout Energy" => "preworkout_energy.jpeg",
    "Pre Workout Extreme" => "preworkout_extreme.jpeg",
    "BCAA 2:1:1" => "bcaa.jpeg",
    "Glutamina 300g" => "glutamina.jpeg",
    "Multivitamínico Deportivo" => "multivitaminico.png",
    "Omega 3 Premium" => "omega3.jpeg",
    "ThermoFit Fat Burner" => "thermofit.jpeg",
    "Recovery Complex" => "recovery_complex.jpeg"
];

$productos = [];

while ($fila = mysqli_fetch_assoc($resultado)) {

    $imagen = "dashboard.png";

    if (isset($mapaImagenes[$fila['nombre']])) {
        $imagen = $mapaImagenes[$fila['nombre']];
    }

    $productos[] = [
        "id" => (int)$fila["id_producto"],
        "nombre" => $fila["nombre"],
        "descripcion" => $fila["descripcion"] ?? "",
        "precio" => (float)$fila["precio"],
        "stock" => (int)$fila["stock"],
        "imagen" => $imagen
    ];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Tienda - Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

<style>
body {
    background: #0f0f0f;
    color: white;
}

.navbar {
    background: #111;
    border-bottom: 1px solid #222;
}

.hero {
    background:
        linear-gradient(rgba(0,0,0,.75), rgba(0,0,0,.85)),
        url('img/gym-principal.jpg') center/cover;
    padding: 90px 0;
}

.hero h1 {
    color: #dc3545;
    font-weight: 900;
    letter-spacing: 1px;
}

.product-card {
    background: #181818;
    border: 1px solid #252525;
    border-radius: 20px;
    overflow: hidden;
    transition: .3s;
    height: 100%;
}

.product-card:hover {
    transform: translateY(-6px);
    border-color: #dc3545;
    box-shadow: 0 10px 25px rgba(220,53,69,.25);
}

.product-card img {
    height: 240px;
    width: 100%;
    object-fit: contain;
    background: #fff;
    padding: 15px;
}

.product-card .card-body {
    display: flex;
    flex-direction: column;
}

.product-card p {
    color: #bbb;
    flex-grow: 1;
}

.price {
    color: #dc3545;
    font-size: 1.5rem;
    font-weight: bold;
}

.cart {
    background: #181818;
    border: 1px solid #252525;
    border-radius: 20px;
    padding: 22px;
    position: sticky;
    top: 20px;
}

.cart-item {
    border-bottom: 1px solid #333;
    padding: 12px 0;
}

.cart-total {
    font-size: 1.4rem;
    font-weight: bold;
    color: #dc3545;
}

.form-control {
    background: #111;
    border: 1px solid #333;
    color: white;
}

.form-control:focus {
    background: #111;
    color: white;
    border-color: #dc3545;
    box-shadow: none;
}

.form-control::placeholder {
    color: #999;
}
</style>

</head>

<body>

<nav class="navbar navbar-dark navbar-expand-lg">
<div class="container">

    <a class="navbar-brand fw-bold" href="index.html">
        🏋️ Warrior Gym
    </a>

    <div class="navbar-nav ms-auto">

        <a class="nav-link text-white" href="index.html">Inicio</a>

        <?php if ($cliente_logueado): ?>

            <a class="nav-link text-white" href="dashboard_usuario.php">Panel</a>
            <a class="nav-link text-white" href="clientes_mis_compras.php">Mis compras</a>
            <a class="nav-link text-white" href="logout.php">Cerrar sesión</a>

        <?php else: ?>

            <a class="nav-link text-white" href="login.php">Iniciar sesión</a>
            <a class="nav-link text-white" href="register.php">Registrarse</a>

        <?php endif; ?>

    </div>

</div>
</nav>

<section class="hero">
    <div class="container text-center">
        <h1 class="display-5">TIENDA OFICIAL WARRIOR GYM</h1>
        <p class="lead text-light">
            Suplementos deportivos para potenciar tus entrenamientos.
        </p>
    </div>
</section>

<div class="container py-5">

    <div class="row g-4">

        <div class="col-lg-8">

            <div class="mb-4">
                <input
                    type="text"
                    id="buscador"
                    class="form-control form-control-lg"
                    placeholder="Buscar suplementos...">
            </div>

            <div class="row g-4" id="productosContainer"></div>

        </div>

        <div class="col-lg-4">

            <div class="cart">

                <h3 class="text-danger mb-3">
                    <i class="bi bi-cart-fill"></i> Carrito
                </h3>

                <div id="cartItems">
                    <p class="text-secondary">No hay productos.</p>
                </div>

                <hr>

                <p>
                    Cantidad:
                    <strong id="cantidad">0</strong>
                </p>

                <p class="cart-total">
                    Total:
                    <span id="total">$0</span>
                </p>

                <button class="btn btn-danger w-100 mb-2" onclick="vaciarCarrito()">
                    Vaciar carrito
                </button>

                <button class="btn btn-success w-100" onclick="finalizarCompra()">
                    Finalizar compra
                </button>

            </div>

        </div>

    </div>

</div>
<!-- MODAL PAGO CON TARJETA -->
<div class="modal fade" id="modalTarjeta" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border border-danger">

            <div class="modal-header border-danger">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-credit-card"></i> Datos de pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Titular de la tarjeta</label>
                    <input type="text" id="titularTarjeta" class="form-control" placeholder="Ej: Gastón Rojas">
                </div>

                <div class="mb-3">
                    <label class="form-label">Número de tarjeta</label>
                    <input type="text" id="numeroTarjeta" class="form-control" maxlength="16" placeholder="Solo números">
                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vencimiento</label>
                        <input type="month" id="vencimientoTarjeta" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">CVV</label>
                        <input type="text" id="cvvTarjeta" class="form-control" maxlength="4" placeholder="123">
                    </div>

                </div>

                <p class="text-secondary small mb-0">
                    Pago simulado para fines académicos. No se realiza un cobro real.
                </p>

            </div>

            <div class="modal-footer border-danger">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="button" class="btn btn-success" onclick="confirmarCompraConTarjeta()">
                    Confirmar compra
                </button>
            </div>

        </div>
    </div>
</div>
<script>
const productos = <?php echo json_encode($productos, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
const clienteLogueado = <?= $cliente_logueado ? 'true' : 'false' ?>;
const container = document.getElementById("productosContainer");
const buscador = document.getElementById("buscador");

let carrito = [];

function formato(precio) {
    return "$" + Number(precio).toLocaleString("es-AR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function escaparHTML(texto) {
    return String(texto)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

function renderProductos() {

    const texto = buscador.value.toLowerCase();

    const productosFiltrados = productos.filter(p =>
        p.nombre.toLowerCase().includes(texto) ||
        p.descripcion.toLowerCase().includes(texto)
    );

    if (productosFiltrados.length === 0) {
        container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-dark border border-danger text-center text-white">
                    No se encontraron productos.
                </div>
            </div>
        `;
        return;
    }

    container.innerHTML = productosFiltrados.map(p => {

        const sinStock = p.stock <= 0;

        return `
            <div class="col-md-6">
                <div class="card product-card">

                    <img src="img/${escaparHTML(p.imagen)}" alt="${escaparHTML(p.nombre)}">

                    <div class="card-body">

                        <h5>${escaparHTML(p.nombre)}</h5>

                        <p>${escaparHTML(p.descripcion)}</p>

                        <span class="badge ${sinStock ? 'bg-danger' : 'bg-success'} mb-2">
                            ${sinStock ? 'Sin stock' : 'Stock: ' + p.stock}
                        </span>

                        <div class="price mb-3">
                            ${formato(p.precio)}
                        </div>

                        <button 
                            class="btn ${sinStock ? 'btn-secondary' : 'btn-warning'} mt-auto"
                            onclick="agregar(${p.id})"
                            ${sinStock ? 'disabled' : ''}>
                            ${sinStock ? 'No disponible' : 'Agregar al carrito'}
                        </button>

                    </div>

                </div>
            </div>
        `;
    }).join('');
}

function agregar(id) {

    const producto = productos.find(p => Number(p.id) === Number(id));

    if (!producto) return;

    if (producto.stock <= 0) {
        alert("Producto sin stock");
        return;
    }

    const item = carrito.find(p => Number(p.id) === Number(id));

    if (item) {

        if (item.cantidad >= producto.stock) {
            alert("No hay más stock disponible de este producto.");
            return;
        }

        item.cantidad++;

    } else {

        carrito.push({
            id: producto.id,
            nombre: producto.nombre,
            precio: Number(producto.precio),
            stock: Number(producto.stock),
            cantidad: 1
        });
    }

    actualizarCarrito();
}

function actualizarCarrito() {

    const cartItems = document.getElementById("cartItems");

    if (carrito.length === 0) {

        cartItems.innerHTML = "<p class='text-secondary'>No hay productos.</p>";
        document.getElementById("cantidad").textContent = 0;
        document.getElementById("total").textContent = "$0";
        return;
    }

    let html = "";
    let total = 0;
    let cantidadTotal = 0;

    carrito.forEach(item => {

        const subtotal = item.precio * item.cantidad;

        total += subtotal;
        cantidadTotal += item.cantidad;

        html += `
            <div class="cart-item">

                <strong>${escaparHTML(item.nombre)}</strong>

                <div class="d-flex align-items-center gap-2 my-2">

                    <button class="btn btn-sm btn-secondary" onclick="disminuir(${item.id})">
                        -
                    </button>

                    <strong>${item.cantidad}</strong>

                    <button class="btn btn-sm btn-success" onclick="aumentar(${item.id})">
                        +
                    </button>

                </div>

                <p class="mb-2">Subtotal: ${formato(subtotal)}</p>

                <button class="btn btn-sm btn-danger" onclick="eliminar(${item.id})">
                    Eliminar
                </button>

            </div>
        `;
    });

    cartItems.innerHTML = html;
    document.getElementById("cantidad").textContent = cantidadTotal;
    document.getElementById("total").textContent = formato(total);
}

function eliminar(id) {
    carrito = carrito.filter(p => Number(p.id) !== Number(id));
    actualizarCarrito();
}

function aumentar(id) {

    const item = carrito.find(p => Number(p.id) === Number(id));

    if (!item) return;

    if (item.cantidad >= item.stock) {
        alert("No hay más stock disponible de este producto.");
        return;
    }

    item.cantidad++;
    actualizarCarrito();
}

function disminuir(id) {

    const item = carrito.find(p => Number(p.id) === Number(id));

    if (!item) return;

    item.cantidad--;

    if (item.cantidad <= 0) {
        carrito = carrito.filter(p => Number(p.id) !== Number(id));
    }

    actualizarCarrito();
}

function vaciarCarrito() {
    carrito = [];
    actualizarCarrito();
}

function finalizarCompra() {

    if (carrito.length === 0) {
        alert("El carrito está vacío.");
        return;
    }

    if (!clienteLogueado) {
        alert("Para finalizar la compra tenés que iniciar sesión.");
        window.location.href = "login.php";
        return;
    }

    const modal = new bootstrap.Modal(document.getElementById("modalTarjeta"));
    modal.show();
}

function confirmarCompraConTarjeta() {

    const titular = document.getElementById("titularTarjeta").value.trim();
    const numero = document.getElementById("numeroTarjeta").value.trim();
    const vencimiento = document.getElementById("vencimientoTarjeta").value.trim();
    const cvv = document.getElementById("cvvTarjeta").value.trim();

    if (titular === "" || numero === "" || vencimiento === "" || cvv === "") {
        alert("Completá todos los datos de la tarjeta.");
        return;
    }

    if (!/^[a-zA-ZÁÉÍÓÚáéíóúÑñ\s]+$/.test(titular)) {
        alert("El titular solo puede contener letras.");
        return;
    }

    if (!/^[0-9]{16}$/.test(numero)) {
        alert("El número de tarjeta debe tener 16 números.");
        return;
    }

    if (!/^[0-9]{3,4}$/.test(cvv)) {
        alert("El CVV debe tener 3 o 4 números.");
        return;
    }

    const items = carrito.map(item => ({
        id: item.id,
        nombre: item.nombre,
        precio: item.precio,
        cantidad: item.cantidad
    }));

    fetch("procesar_venta.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ 
            items: items,
            pago: {
                metodo: "Tarjeta",
                titular: titular,
                ultimos_digitos: numero.slice(-4)
            }
        })
    })
    .then(r => r.text())
    .then(d => {

        console.log("RESPUESTA PHP:", d);

        if (d.trim() === "OK") {

            alert("Compra realizada correctamente.");

            carrito = [];
            actualizarCarrito();

            window.location.href = "clientes_mis_compras.php";

        } else {
            alert("Error en la compra: " + d);
        }

    })
    .catch(err => {
        console.log(err);
        alert("Error al procesar la compra.");
    });
}

buscador.addEventListener("keyup", renderProductos);

renderProductos();
actualizarCarrito();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>