<?php
// Configuración de la caducidad de la sesión
ini_set('session.gc_maxlifetime', 3600); // 1 hora
session_set_cookie_params(3600); // 1 hora

session_start();

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Función para eliminar un viaje del carrito
function eliminarDelCarrito($idViaje) {
    if (($key = array_search($idViaje, $_SESSION['carrito'])) !== false) {
        unset($_SESSION['carrito'][$key]);
    }
}

// Verificar si se ha enviado una solicitud para eliminar un viaje
if (isset($_GET['eliminar'])) {
    eliminarDelCarrito($_GET['eliminar']);
}

// Mostrar los viajes en el carrito
function mostrarCarrito() {
    global $conn;

    if (empty($_SESSION['carrito'])) {
        echo "El carrito está vacío.";
        return;
    }

    $ids = implode(",", $_SESSION['carrito']);
    $sql = "SELECT * FROM viajes WHERE id IN ($ids)";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Hotel: " . $row["nombreHotel"] . " - Ciudad: " . $row["ciudad"] . " - País: " . $row["pais"] . " - Fecha: " . $row["fechaViaje"] . " - Duración: " . $row["duracionViaje"] . " días <a href='carrito.php?eliminar=" . $row["id"] . "'>Eliminar</a><br>";
        }
    } else {
        echo "El carrito está vacío.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
</head>
<body>
    <h2>Carrito de Compras</h2>
    <?php
    // Conectar a la base de datos (asumiendo MySQL)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "agencia_viajes";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    mostrarCarrito();

    $conn->close();
    ?>
    <a href="index.php">Volver a la página principal</a>
</body>
</html>
