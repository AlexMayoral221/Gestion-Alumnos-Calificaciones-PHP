<?php
$mensaje_status = '';
$tipo_status = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'bd.php'; 

    $nombre = $_POST['nombre'];
    $parcial1 = $_POST['parcial1'];
    $parcial2 = $_POST['parcial2'];
    $parcial3 = $_POST['parcial3'];

    $conn->begin_transaction();
    $exito_transaccion = true;

    try {
        $stmt_alumno = $conn->prepare("INSERT INTO alumnos (nombre) VALUES (?)");
        $stmt_alumno->bind_param("s", $nombre);
        
        if ($stmt_alumno->execute()) {
            $alumno_id = $conn->insert_id;
            $stmt_alumno->close();
            
            $stmt_calificaciones = $conn->prepare("INSERT INTO calificaciones (alumno_id, parcial1, parcial2, parcial3) 
                                                   VALUES (?, ?, ?, ?)");
            $stmt_calificaciones->bind_param("iddd", $alumno_id, $parcial1, $parcial2, $parcial3);
            
            if (!$stmt_calificaciones->execute()) {
                $exito_transaccion = false;
                error_log("Error en calificaciones: " . $stmt_calificaciones->error);
            }
            $stmt_calificaciones->close();

        } else {
            $exito_transaccion = false;
            error_log("Error en alumno: " . $stmt_alumno->error);
        }

        if ($exito_transaccion) {
            $conn->commit();
            $mensaje_status = "¡Alumno <b>{$nombre}</b> y sus calificaciones se agregaron con éxito!";
            $tipo_status = 'success';
        } else {
            $conn->rollback();
            $mensaje_status = "Error al guardar el alumno. Por favor, revisa los logs.";
            $tipo_status = 'error';
        }
    } catch (Exception $e) {
        $conn->rollback();
        $mensaje_status = "Ha ocurrido un error inesperado: " . $e->getMessage();
        $tipo_status = 'error';
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Alumno</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f7f7;
        }
    </style>
</head>
<body class="p-4 sm:p-8">
    <div class="max-w-xl mx-auto bg-white p-8 md:p-12 shadow-2xl rounded-xl border border-gray-100">
        
        <header class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-800 border-b pb-4">
                Agregar Nuevo Alumno
            </h1>
        </header>

        <?php if ($mensaje_status): ?>
        <div role="alert" class="p-4 rounded-lg mb-6 text-sm font-medium 
            <?php echo $tipo_status == 'success' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300'; ?>">
            <?php echo $mensaje_status; ?>
        </div>
        <?php endif; ?>

        <form action="agregar_alumno.php" method="post" class="space-y-6">
            
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition duration-150 sm:text-sm"
                       placeholder="Ej: Juan Pérez">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div>
                    <label for="parcial1" class="block text-sm font-medium text-gray-700 mb-1">Parcial 1:</label>
                    <input type="number" step="0.01" min="0" max="10" id="parcial1" name="parcial1" required
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition duration-150 sm:text-sm"
                           placeholder="Ej: 8.5">
                </div>
                
                <div>
                    <label for="parcial2" class="block text-sm font-medium text-gray-700 mb-1">Parcial:</label>
                    <input type="number" step="0.01" min="0" max="10" id="parcial2" name="parcial2" required
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition duration-150 sm:text-sm"
                           placeholder="Ej: 7.0">
                </div>
                
                <div>
                    <label for="parcial3" class="block text-sm font-medium text-gray-700 mb-1">Parcial 3:</label>
                    <input type="number" step="0.01" min="0" max="10" id="parcial3" name="parcial3" required
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition duration-150 sm:text-sm"
                           placeholder="Ej: 9.2">
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center pt-4 border-t mt-6">
                <a href="index.php" 
                   class="mb-4 sm:mb-0 text-sm font-semibold text-gray-500 hover:text-gray-700 transition duration-150">
                    ← Volver a la Lista de Alumnos
                </a>

                <input type="submit" value="Guardar Alumno y Calificaciones"
                       class="cursor-pointer px-6 py-3 bg-blue-600 text-white font-bold rounded-lg shadow-lg hover:bg-blue-700 transition duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-blue-300 text-base">
            </div>
            
        </form>

    </div>
</body>
</html>