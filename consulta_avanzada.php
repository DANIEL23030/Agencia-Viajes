<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Avanzada de Reservas</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Hoteles con más de dos reservas</h2>
    <?php
    // Conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "AGENCIA";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Consulta avanzada
    $sql = "
        SELECT h.nombre, COUNT(r.id_reserva) as num_reservas
        FROM HOTEL h
        JOIN RESERVA r ON h.id_hotel = r.id_hotel
        GROUP BY h.nombre
        HAVING num_reservas > 2
    ";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Hotel: " . $row["nombre"] . " - Reservas: " . $row["num_reservas"] . "<br>";
        }
    } else {
        echo "No se encontraron hoteles con más de dos reservas.";
    }

    $conn->close();
    ?>
</body>
</html>
