<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . "/php/conexion.php");

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
        "descripcion" => $fila["descripcion"],
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

<title>Tienda Warrior Gym</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#0f0f0f;
    color:white;
}

.navbar{
    background:#111;
}

.product-card{
    background:#181818;
    border:none;
    border-radius:20px;
    overflow:hidden;
    transition:.3s;
    height:100%;
}

.product-card:hover{
    transform:translateY(-8px);
}

.product-card img{
    height:260px;
    width:100%;
    object-fit:contain;
    background:white;
    padding:15px;
}

.product-card .card-body{
    display:flex;
    flex-direction:column;
}

.product-card h5{
    color:white;
}

.product-card p{
    color:#bbb;
    flex-grow:1;
}

.price{
    color:#dc3545;
    font-size:1.6rem;
    font-weight:bold;
}

.cart{
    background:#181818;
    border-radius:20px;
    padding:20px;
    position:sticky;
    top:20px;
}

.cart-item{
    border-bottom:1px solid #333;
    padding:10px 0;
}

.cart-total{
    font-size:1.4rem;
    font-weight:bold;
    color:#dc3545;
}

.badge-stock{
    background:#198754;
}

.hero{
    background:url('img/gym-principal.jpg') center/cover;
    padding:100px 0;
    position:relative;
}

.hero::before{
    content:'';
    position:absolute;
    inset:0;
    background:rgba(0,0,0,.7);
}

.hero .container{
    position:relative;
    z-index:2;
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
<a class="nav-link text-white" href="nosotros.html">Nosotros</a>
<a class="nav-link text-white" href="membresias.html">Planes</a>
<a class="nav-link text-white" href="clases.html">Clases</a>
<a class="nav-link text-white" href="contacto.html">Contacto</a>
</div>

</div>
</nav>

<section class="hero">
<div class="container text-center">
<h1 class="display-4 fw-bold">
TIENDA OFICIAL WARRIOR GYM
</h1>

<p class="lead">
Suplementos deportivos premium para potenciar tus resultados.
</p>
</div>
</section>

<div class="container py-5">

<div class="row">

<div class="col-lg-8">

<div class="mb-4">
    <input
        type="text"
        id="buscador"
        class="form-control"
        placeholder="Buscar suplementos...">
</div>

<div class="row g-4" id="productosContainer">
</div>

</div>

<div class="col-lg-4">

<div class="cart">

<h3>🛒 Carrito</h3>

<div id="cartItems">
<p class="text-secondary">
No hay productos.
</p>
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

<button
class="btn btn-success w-100"
onclick="finalizarCompra()">
Finalizar compra
</button>

</div>

</div>

</div>

</div>

<script>

const productos = <?php echo json_encode($productos, JSON_UNESCAPED_UNICODE); ?>;

const container = document.getElementById("productosContainer");
const buscador = document.getElementById("buscador");

/* ✔ CARRITO ESTABLE */
let carrito = [];

function formato(precio){
    return "$" + Number(precio).toLocaleString("es-AR");
}

/* =========================
   PRODUCTOS
========================= */
function renderProductos(){

    const texto = buscador.value.toLowerCase();

    const productosFiltrados = productos.filter(p =>
        p.nombre.toLowerCase().includes(texto) ||
        p.descripcion.toLowerCase().includes(texto)
    );

    container.innerHTML = productosFiltrados.map(p => `

    <div class="col-md-6">
        <div class="card product-card">

            <img src="img/${p.imagen}" alt="${p.nombre}">

            <div class="card-body">

                <h5>${p.nombre}</h5>
                <p>${p.descripcion}</p>

                <span class="badge badge-stock mb-2">
                    Stock: ${p.stock}
                </span>

                <div class="price mb-3">
                    ${formato(p.precio)}
                </div>

                <button class="btn btn-warning"
                        onclick="agregar(${p.id})">
                    Agregar al carrito
                </button>

            </div>

        </div>
    </div>

    `).join('');
}

/* =========================
   AGREGAR AL CARRITO
========================= */
function agregar(id){

    const producto = productos.find(p => p.id == id);

    if(!producto) return;

    if(carrito[id]){

        carrito[id].cantidad++;

    }else{

        carrito[id] = {
            id: producto.id,
            nombre: producto.nombre,
            precio: Number(producto.precio), // 🔥 IMPORTANTE
            cantidad: 1
        };
    }

    actualizarCarrito();
}
/* =========================
   ACTUALIZAR CARRITO
========================= */
function actualizarCarrito(){

    const cartItems = document.getElementById("cartItems");

    if(carrito.length === 0){

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

            <strong>${item.nombre}</strong>

            <div class="d-flex align-items-center gap-2 mb-2">

                <button class="btn btn-sm btn-secondary"
                        onclick="disminuir(${item.id})">
                    ➖
                </button>

                <strong>${item.cantidad}</strong>

                <button class="btn btn-sm btn-success"
                        onclick="aumentar(${item.id})">
                    ➕
                </button>

            </div>

            <p>Subtotal: ${formato(subtotal)}</p>

            <button class="btn btn-sm btn-danger"
                    onclick="eliminar(${item.id})">
                Eliminar
            </button>

        </div>
        `;
    });

    cartItems.innerHTML = html;

    document.getElementById("cantidad").textContent = cantidadTotal;
    document.getElementById("total").textContent = formato(total);
}

/* =========================
   CONTROLES
========================= */
function eliminar(id){
    carrito = carrito.filter(p => p.id != id);
    actualizarCarrito();
}

function aumentar(id){
    const item = carrito.find(p => p.id == id);
    item.cantidad++;
    actualizarCarrito();
}

function disminuir(id){
    const item = carrito.find(p => p.id == id);

    item.cantidad--;

    if(item.cantidad <= 0){
        carrito = carrito.filter(p => p.id != id);
    }

    actualizarCarrito();
}

function vaciarCarrito(){
    carrito = [];
    actualizarCarrito();
}

/* =========================
   FINALIZAR COMPRA (CLAVE)
========================= */

function finalizarCompra() {

    const items = Object.values(carrito);

    if (items.length === 0) {
        alert("Carrito vacío");
        return;
    }

    fetch("procesar_venta.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ items: items })
    })
    .then(r => r.text())
    .then(d => {
        console.log(d);
        alert("Compra realizada");

        carrito = {};
        actualizarCarrito();
    })
    .catch(err => {
        console.log(err);
        alert("Error en la compra");
    });
}

buscador.addEventListener("keyup", renderProductos);

renderProductos();
actualizarCarrito();

</script>

</body>
</html>