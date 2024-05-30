<!DOCTYPE html>
<html>
<head>
    <title>Agregar Alumno</title>
</head>
<body>
    <h1>Agregar Nuevo Alumno</h1>
    <form action="agregar_alumno.php" method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>
        <br>
        <label for="parcial1">Parcial 1:</label>
        <input type="number" step="0.01" id="parcial1" name="parcial1" required>
        <br>
        <label for="parcial2">Parcial 2:</label>
        <input type="number" step="0.01" id="parcial2" name="parcial2" required>
        <br>
        <label for="parcial3">Parcial 3:</label>
        <input type="number" step="0.01" id="parcial3" name="parcial3" required>
        <br>
        <input type="submit" value="Agregar">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        include 'bd.php';

        $nombre = $_POST['nombre'];
        $parcial1 = $_POST['parcial1'];
        $parcial2 = $_POST['parcial2'];
        $parcial3 = $_POST['parcial3'];

        $sql = "INSERT INTO alumnos (nombre) VALUES ('$nombre')";
        if ($conn->query($sql) === TRUE) {
            $alumno_id = $conn->insert_id;
            $sql_calificaciones = "INSERT INTO calificaciones (alumno_id, parcial1, parcial2, parcial3) 
                                   VALUES ($alumno_id, $parcial1, $parcial2, $parcial3)";
            if ($conn->query($sql_calificaciones) === TRUE) {
                echo "Nuevo alumno agregado con éxito";
            } else {
                echo "Error: " . $sql_calificaciones . "<br>" . $conn->error;
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
        header("Location: index.php"); 
        exit;
    }
    ?>
</body>
</html>