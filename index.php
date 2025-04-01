<?php
$servername = "mysql_db"; // Nombre del contenedor de la base de datos
$username = "root";
$password = "1234";
$dbname = "base_redes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Insertar usuario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $sql = "INSERT INTO usuarios (nombre, email) VALUES ('$nombre', '$email')";
    if ($conn->query($sql) === TRUE) {
        echo "Nuevo registro creado exitosamente.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Eliminar usuario
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conn->query("DELETE FROM usuarios WHERE id=$id");
    header("Location: index.php"); // Recargar la página
}

// Obtener usuario para edición
$editar = false;
$usuario = ["id" => "", "nombre" => "", "email" => ""];
if (isset($_GET['editar'])) {
    $id = $_GET['editar'];
    $result = $conn->query("SELECT * FROM usuarios WHERE id=$id");
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        $editar = true;
    }
}

// Actualizar usuario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $conn->query("UPDATE usuarios SET nombre='$nombre', email='$email' WHERE id=$id");
    header("Location: index.php");
}

// Obtener lista de usuarios
$sql = "SELECT * FROM usuarios";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        table { width: 50%; margin: auto; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; }
        input { padding: 5px; margin: 5px; }
        button { padding: 5px; margin: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Gestión de Usuarios</h1>

    <h2><?php echo $editar ? "Editar Usuario" : "Registrar Usuario"; ?></h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo $usuario['nombre']; ?>" required><br>
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo $usuario['email']; ?>" required><br>
        <input type="submit" name="<?php echo $editar ? "actualizar" : "registrar"; ?>" value="<?php echo $editar ? "Actualizar" : "Registrar"; ?>">
    </form>

    <h2>Lista de Usuarios</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row["id"]; ?></td>
                    <td><?php echo $row["nombre"]; ?></td>
                    <td><?php echo $row["email"]; ?></td>
                    <td>
                        <a href="?editar=<?php echo $row['id']; ?>">Editar</a>
                        <a href="?eliminar=<?php echo $row['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
<?php $conn->close(); ?>
