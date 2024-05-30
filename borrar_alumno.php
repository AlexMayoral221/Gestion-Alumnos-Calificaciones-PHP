<?php
include 'bd.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql_calificaciones = "DELETE FROM calificaciones WHERE alumno_id = $id";
    if ($conn->query($sql_calificaciones) === TRUE) {
        $sql_alumno = "DELETE FROM alumnos WHERE id = $id";
        if ($conn->query($sql_alumno) === TRUE) {
            echo "Alumno y sus calificaciones borrados con éxito";
        } else {
            echo "Error al borrar el alumno: " . $conn->error;
        }
    } else {
        echo "Error al borrar las calificaciones: " . $conn->error;
    }
}

$conn->close();
header("Location: index.php");
exit;
?>
