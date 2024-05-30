<?php
include 'bd.php';

$sql = "SELECT alumnos.id, alumnos.nombre, 
        calificaciones.parcial1, calificaciones.parcial2, calificaciones.parcial3, 
        (calificaciones.parcial1 + calificaciones.parcial2 + calificaciones.parcial3) / 3 AS promedio 
        FROM alumnos 
        LEFT JOIN calificaciones ON alumnos.id = calificaciones.alumno_id";
$result = $conn->query($sql);

$pasaron = 0;
$no_pasaron = 0;
$total = 0;

?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Alumnos</title>
</head>
<body>
    <h1>Lista de Alumnos</h1>
    <a href="agregar_alumno.php">Agregar Alumno</a>
    <table border="1">
        <tr>
            <th>Nombre</th>
            <th>Parcial 1</th>
            <th>Parcial 2</th>
            <th>Parcial 3</th>
            <th>Promedio</th>
            <th></th>
            <th></th>
        </tr>
        <?php
        while($row = $result->fetch_assoc()) {
            $promedio = round($row['promedio'], 2);
            $status = $promedio >= 6 ? 'Pasó' : 'No pasó';
            $total++;
            if ($promedio >= 6) {
                $pasaron++;
            } else {
                $no_pasaron++;
            }
            echo "<tr>
                    <td>{$row['nombre']}</td>
                    <td>{$row['parcial1']}</td>
                    <td>{$row['parcial2']}</td>
                    <td>{$row['parcial3']}</td>
                    <td>{$promedio}</td>
                    <td>{$status}</td>
                    <td>
                        <a href='borrar_alumno.php?id={$row['id']}'>Borrar</a>
                    </td>
                  </tr>";
        }
        ?>
    </table><br>

    <table border="1">
        <tr>
            <th>Total</th>
            <th>Pasaron</th>
            <th>No Pasaron</th>
        </tr>
        <tr>
            <td><?php echo $total; ?></td>
            <td><?php echo $pasaron; ?></td>
            <td><?php echo $no_pasaron; ?></td>
        </tr>
    </table>
</body>
</html>
<?php
$conn->close();
?>