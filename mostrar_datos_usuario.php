<?php
 require_once "./SERVER.PHP";
// Configuración de la base de datos
$servername = SERVER;
$username = USER;
$password = PASS;
$database = DB;

session_start(); // Inicia la sesión

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $database);

session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: login.php");
    exit();
}

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Obtener el nombre del usuario de la sesión
    $usuario = $_SESSION['nombre'];
    $sql_user = "SELECT * FROM usuarios WHERE nombre = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("s", $usuario);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user !== false && $result_user->num_rows > 0) {
        $row_user = $result_user->fetch_assoc();
        // Eliminamos la contraseña por seguridad
        unset($row_user['contrasena']);

        $response["success"] = true;
        $response["message"] = "Datos del usuario obtenidos con éxito.";
        $response["usuario"] = $row_user;
    } else {
        $response["message"] = "No se encontró al usuario en la base de datos.";
    }

    // Cerrar la conexión y liberar recursos
    $stmt_user->close();
}

// Devolver la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);

// Cerrar la conexión a la base de datos
$conn->close();
?>
