<?php
// Configuración de la caducidad de la sesión
ini_set('session.gc_maxlifetime', 3600); // 1 hora
session_set_cookie_params(3600); // 1 hora

session_start();

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Función para agregar un viaje al carrito
function agregarAlCarrito($idViaje) {
    if (!in_array($idViaje, $_SESSION['carrito'])) {
        $_SESSION['carrito'][] = $idViaje;
        // Regenerar el ID de sesión para seguridad
        session_regenerate_id(true);
    }
}

// Verificar si se ha enviado una solicitud para agregar un viaje
if (isset($_GET['agregar'])) {
    agregarAlCarrito($_GET['agregar']);
}

// Conectar a la base de datos (asumiendo MySQL)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agencia_viajes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Procesar formulario de registro
function filtro($datos) {
    $datos = trim($datos);
    $datos = stripslashes($datos);
    $datos = htmlspecialchars($datos);
    return $datos;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar_viaje'])) {
    // Recuperar y filtrar datos del formulario
    $nombreHotel = filtro($_POST['nombreHotel']);
    $ciudad = filtro($_POST['ciudad']);
    $pais = filtro($_POST['pais']);
    $fechaViaje = filtro($_POST['fechaViaje']);
    $duracionViaje = filtro($_POST['duracionViaje']);
    $categoria = filtro($_POST['categoria']);
    $rangoPrecios = filtro($_POST['rangoPrecios']);
    $opcionesComida = filtro($_POST['opcionesComida']);

    // Validar campos obligatorios
    if (empty($nombreHotel) || empty($ciudad) || empty($pais) || empty($fechaViaje) || empty($duracionViaje)) {
        echo "Por favor, complete todos los campos obligatorios.";
        exit();
    }

    // Usar consultas preparadas para evitar inyección SQL
    $sql = "INSERT INTO viajes (nombreHotel, ciudad, pais, fechaViaje, duracionViaje, categoria, rangoPrecios, opcionesComida) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssisss", $nombreHotel, $ciudad, $pais, $fechaViaje, $duracionViaje, $categoria, $rangoPrecios, $opcionesComida);

    if ($stmt->execute()) {
        echo "Registro de viaje guardado exitosamente.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

// Función para generar notificaciones emergentes
function generarNotificaciones() {
    // Datos simulados de ofertas especiales
    $ofertas = [
        "¡20% de descuento en vuelos a París!",
        "¡Reserva 2 noches y obtén la tercera gratis en hoteles seleccionados!",
        "¡10% de descuento en paquetes turísticos a Japón!"
    ];

    // Selecciona una oferta aleatoria
    $oferta = $ofertas[array_rand($ofertas)];
    echo "<script>alert('$oferta');</script>";
}

// Llamada a la función para generar la notificación al cargar la página
generarNotificaciones();

// Procesar formulario de búsqueda de vuelos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buscar_vuelos'])) {
    $origen = filtro($_POST['origen']);
    $destino = filtro($_POST['destino']);
    $fecha = filtro($_POST['fecha']);

    // Realizar la búsqueda de vuelos (simulación)
    $vuelos = [
        ["origen" => "Santiago", "destino" => "Buenos Aires", "fecha" => "2024-07-01", "precio" => "100"],
        ["origen" => "Santiago", "destino" => "Lima", "fecha" => "2024-07-02", "precio" => "150"]
    ];

    echo "<h2>Resultados de Búsqueda de Vuelos</h2>";
    foreach ($vuelos as $vuelo) {
        if ($vuelo["origen"] == $origen && $vuelo["destino"] == $destino && $vuelo["fecha"] == $fecha) {
            echo "Vuelo de " . $vuelo["origen"] . " a " . $vuelo["destino"] . " el " . $vuelo["fecha"] . " - Precio: $" . $vuelo["precio"] . "<br>";
        }
    }
}

// Procesar formulario de reserva de hoteles
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reservar_hotel'])) {
    $hotel = filtro($_POST['hotel']);
    $fechaEntrada = filtro($_POST['fechaEntrada']);
    $fechaSalida = filtro($_POST['fechaSalida']);
    $huespedes = filtro($_POST['huespedes']);

    // Simulación de reserva de hotel
    echo "<h2>Reserva de Hotel</h2>";
    echo "Hotel: " . $hotel . " - Fecha de Entrada: " . $fechaEntrada . " - Fecha de Salida: " . $fechaSalida . " - Huéspedes: " . $huespedes . "<br>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agencia de Viajes - Ofertas Especiales</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Viajes Disponibles</h2>
    <?php
    $sql = "SELECT * FROM viajes";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Hotel: " . $row["nombreHotel"] . " - Ciudad: " . $row["ciudad"] . " - País: " . $row["pais"] . " - Fecha: " . $row["fechaViaje"] . " - Duración: " . $row["duracionViaje"] . " días <a href='index.php?agregar=" . $row["id"] . "'>Agregar al carrito</a><br>";
        }
    } else {
        echo "No hay viajes disponibles.";
    }
    ?>

    <a href="carrito.php">Ver Carrito</a>

    <hr>

    <div class="form-container">
        <h2>Registro de Destinos y Fechas de Viaje</h2>
        <form action="index.php" method="post">
            <label for="nombreHotel">Nombre del Hotel:</label>
            <input type="text" id="nombreHotel" name="nombreHotel" required aria-label="Nombre del Hotel" aria-required="true">

            <label for="ciudad">Ciudad:</label>
            <input type="text" id="ciudad" name="ciudad" required aria-label="Ciudad" aria-required="true">

            <label for="pais">País:</label>
            <input type="text" id="pais" name="pais" required aria-label="País" aria-required="true">

            <label for="fechaViaje">Fecha de Viaje:</label>
            <input type="date" id="fechaViaje" name="fechaViaje" required aria-label="Fecha de Viaje" aria-required="true">

            <label for="duracionViaje">Duración del Viaje (días):</label>
            <input type="number" id="duracionViaje" name="duracionViaje" min="1" required aria-label="Duración del Viaje" aria-required="true">

            <label for="categoria">Categoría:</label>
            <select id="categoria" name="categoria" aria-label="Categoría">
                <option value="">Seleccione una categoría</option>
                <option value="Hotel">Hotel</option>
                <option value="Resort">Resort</option>
                <option value="Cabaña">Cabaña</option>
                <option value="Aventura">Aventura</option>
                <option value="Playa">Playa</option>
                <option value="Cultural">Cultural</option>
            </select>

            <label for="rangoPrecios">Rango de Precios:</label>
            <input type="text" id="rangoPrecios" name="rangoPrecios" aria-label="Rango de Precios">

            <label for="opcionesComida">Opciones de Comida:</label>
            <select id="opcionesComida" name="opcionesComida" aria-label="Opciones de Comida">
                <option value="">Seleccione una opción de comida</option>
                <option value="Todo incluido">Todo incluido</option>
                <option value="Media pensión">Media pensión</option>
                <option value="Solo alojamiento">Solo alojamiento</option>
            </select>

            <input type="submit" name="registrar_viaje" value="Registrar Viaje" accesskey="r">
        </form>
    </div>

    <hr>

    <div class="form-container">
        <h2>Búsqueda de Vuelos</h2>
        <form action="index.php" method="post">
            <label for="origen">Origen:</label>
            <input type="text" id="origen" name="origen" required aria-label="Origen" aria-required="true">

            <label for="destino">Destino:</label>
            <input type="text" id="destino" name="destino" required aria-label="Destino" aria-required="true">

            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" required aria-label="Fecha" aria-required="true">

            <input type="submit" name="buscar_vuelos" value="Buscar Vuelos" accesskey="b">
        </form>
    </div>

    <hr>

    <div class="form-container">
        <h2>Reserva de Hoteles</h2>
        <form action="index.php" method="post">
            <label for="hotel">Hotel:</label>
            <input type="text" id="hotel" name="hotel" required aria-label="Hotel" aria-required="true">

            <label for="fechaEntrada">Fecha de Entrada:</label>
            <input type="date" id="fechaEntrada" name="fechaEntrada" required aria-label="Fecha de Entrada" aria-required="true">

            <label for="fechaSalida">Fecha de Salida:</label>
            <input type="date" id="fechaSalida" name="fechaSalida" required aria-label="Fecha de Salida" aria-required="true">

            <label for="huespedes">Número de Huéspedes:</label>
            <input type="number" id="huespedes" name="huespedes" min="1" required aria-label="Número de Huéspedes" aria-required="true">

            <input type="submit" name="reservar_hotel" value="Reservar Hotel" accesskey="r">
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>

