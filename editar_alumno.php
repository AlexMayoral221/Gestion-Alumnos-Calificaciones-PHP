<?php
// Incluye la conexión a la base de datos
include 'bd.php'; 

$alumno = null;
$calificaciones = [
    'parcial1' => '',
    'parcial2' => '',
    'parcial3' => ''
];
$mensaje_status = '';
$tipo_status = '';

$alumno_id = $_GET['id'] ?? null;

// --- 1. Lógica para PROCESAR la edición (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['alumno_id'])) {
    $alumno_id = $_POST['alumno_id'];
    $nombre = trim($_POST['nombre']);
    $parcial1 = floatval($_POST['parcial1']);
    $parcial2 = floatval($_POST['parcial2']);
    $parcial3 = floatval($_POST['parcial3']);
    
    // --- NUEVA VALIDACIÓN DE DATOS ---
    $errores = [];

    if (empty($nombre)) {
        $errores[] = "El nombre del alumno no puede estar vacío.";
    }
    
    // Validar rango de notas (0.00 a 10.00)
    if ($parcial1 < 0.00 || $parcial1 > 10.00) {
        $errores[] = "Parcial 1 debe estar entre 0.00 y 10.00.";
    }
    if ($parcial2 < 0.00 || $parcial2 > 10.00) {
        $errores[] = "Parcial 2 debe estar entre 0.00 y 10.00.";
    }
    if ($parcial3 < 0.00 || $parcial3 > 10.00) {
        $errores[] = "Parcial 3 debe estar entre 0.00 y 10.00.";
    }

    if (!empty($errores)) {
        // Si hay errores, no procesar la base de datos y mostrar el mensaje
        $mensaje_status = "¡Error de Validación! Corrige los siguientes problemas:<ul><li>" . implode("</li><li>", $errores) . "</li></ul>";
        $tipo_status = 'error';
        // Recargar las variables para que el formulario mantenga los valores no válidos
        $alumno = ['id' => $alumno_id, 'nombre' => $nombre];
        $calificaciones['parcial1'] = $parcial1;
        $calificaciones['parcial2'] = $parcial2;
        $calificaciones['parcial3'] = $parcial3;
    } else {
        // INICIO DE TRANSACCIÓN: Se ejecuta solo si no hay errores de validación.
        $conn->begin_transaction();
        $exito_transaccion = true;

        try {
            // A. Actualizar nombre del alumno (Sentencia Preparada)
            $stmt_alumno = $conn->prepare("UPDATE alumnos SET nombre = ? WHERE id = ?");
            $stmt_alumno->bind_param("si", $nombre, $alumno_id);
            if (!$stmt_alumno->execute()) {
                $exito_transaccion = false;
                error_log("Error en actualización de alumno: " . $stmt_alumno->error);
            }
            $stmt_alumno->close();

            // B. Actualizar calificaciones con un UPDATE simple.
            $sql_calificaciones_update = "UPDATE calificaciones SET parcial1 = ?, parcial2 = ?, parcial3 = ?
                                          WHERE alumno_id = ?";
            $stmt_calificaciones = $conn->prepare($sql_calificaciones_update);
            $stmt_calificaciones->bind_param("dddi", $parcial1, $parcial2, $parcial3, $alumno_id);
            
            if (!$stmt_calificaciones->execute()) {
                $exito_transaccion = false;
                error_log("Error en actualización de calificaciones: " . $stmt_calificaciones->error);
            }
            $stmt_calificaciones->close();

            if ($exito_transaccion) {
                $conn->commit();
                $mensaje_status = "¡Los datos del alumno **" . htmlspecialchars($nombre) . "** se actualizaron con éxito!";
                $tipo_status = 'success';
            } else {
                $conn->rollback();
                $mensaje_status = "Error al actualizar los datos del alumno. Por favor, revisa los logs.";
                $tipo_status = 'error';
            }
        } catch (Exception $e) {
            $conn->rollback();
            $mensaje_status = "Ha ocurrido un error inesperado: " . $e->getMessage();
            $tipo_status = 'error';
        }
    }
    
    // Si la validación falló, la conexión se cierra más tarde en la sección de carga.
    // Si la validación fue exitosa, la conexión se mantiene abierta para la carga de datos.
}


// --- 2. Lógica para CARGAR los datos iniciales (GET o después de POST) ---
if ($alumno_id && empty($errores)) { // Solo cargar si no hay errores de validación activos
    // Consulta para obtener el alumno y sus calificaciones
    $sql_fetch = "SELECT a.id, a.nombre, c.parcial1, c.parcial2, c.parcial3
                  FROM alumnos a
                  LEFT JOIN calificaciones c ON a.id = c.alumno_id
                  WHERE a.id = ?";
                  
    $stmt_fetch = $conn->prepare($sql_fetch);
    $stmt_fetch->bind_param("i", $alumno_id);
    $stmt_fetch->execute();
    $result_fetch = $stmt_fetch->get_result();
    
    if ($result_fetch->num_rows > 0) {
        $row = $result_fetch->fetch_assoc();
        $alumno = $row;
        // Si no hay calificaciones (LEFT JOIN) usa 0.00 como valor predeterminado
        $calificaciones['parcial1'] = $row['parcial1'] ?? 0.00;
        $calificaciones['parcial2'] = $row['parcial2'] ?? 0.00;
        $calificaciones['parcial3'] = $row['parcial3'] ?? 0.00;
    } else {
        $mensaje_status = "Error: Alumno no encontrado.";
        $tipo_status = 'error';
        $alumno_id = null; // Invalida el ID si no se encuentra
    }
    $stmt_fetch->close();
}

// Cierra la conexión si existe
if (isset($conn)) {
    $conn->close();
}

// Si el ID no existe o no se encontró y no hay mensaje de error (caso inicial), redirigir
if (!$alumno_id && !$mensaje_status) {
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Alumno: <?php echo htmlspecialchars($alumno['nombre'] ?? ''); ?></title>
    <!-- Carga del CDN de Tailwind CSS para estilos -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f7f7;
        }
        /* Estilo para los elementos de lista dentro del mensaje de error */
        .error-list ul {
            list-style-type: disc;
            margin-left: 1.5rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    <div class="max-w-xl mx-auto bg-white p-8 md:p-12 shadow-2xl rounded-xl border border-gray-100">
        
        <!-- Encabezado -->
        <header class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-800 border-b pb-4">
                Editar Alumno: <?php echo htmlspecialchars($alumno['nombre'] ?? 'Cargando...'); ?>
            </h1>
        </header>

        <!-- Mensaje de Estado (Éxito/Error) -->
        <?php if ($mensaje_status): ?>
        <div role="alert" class="p-4 rounded-lg mb-6 text-sm font-medium error-list
            <?php echo $tipo_status == 'success' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300'; ?>">
            <?php echo $mensaje_status; ?>
        </div>
        <?php endif; ?>

        <!-- Formulario de Edición -->
        <?php if ($alumno): ?>
        <form action="editar_alumno.php?id=<?php echo $alumno['id']; ?>" method="post" class="space-y-6">
            <input type="hidden" name="alumno_id" value="<?php echo htmlspecialchars($alumno['id']); ?>">
            
            <!-- Campo Nombre -->
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" required
                       value="<?php echo htmlspecialchars($alumno['nombre']); ?>"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition duration-150 sm:text-sm"
                       placeholder="Ej: Juan Pérez">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Campo Parcial 1 -->
                <div>
                    <label for="parcial1" class="block text-sm font-medium text-gray-700 mb-1">Parcial 1 (0.00 - 10.00):</label>
                    <input type="number" step="0.01" min="0" max="10" id="parcial1" name="parcial1" required
                           value="<?php echo htmlspecialchars($calificaciones['parcial1']); ?>"
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition duration-150 sm:text-sm">
                </div>
                
                <!-- Campo Parcial 2 -->
                <div>
                    <label for="parcial2" class="block text-sm font-medium text-gray-700 mb-1">Parcial 2 (0.00 - 10.00):</label>
                    <input type="number" step="0.01" min="0" max="10" id="parcial2" name="parcial2" required
                           value="<?php echo htmlspecialchars($calificaciones['parcial2']); ?>"
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition duration-150 sm:text-sm">
                </div>
                
                <!-- Campo Parcial 3 -->
                <div>
                    <label for="parcial3" class="block text-sm font-medium text-gray-700 mb-1">Parcial 3 (0.00 - 10.00):</label>
                    <input type="number" step="0.01" min="0" max="10" id="parcial3" name="parcial3" required
                           value="<?php echo htmlspecialchars($calificaciones['parcial3']); ?>"
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition duration-150 sm:text-sm">
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex flex-col sm:flex-row justify-between items-center pt-4 border-t mt-6">
                <a href="index.php" 
                   class="mb-4 sm:mb-0 text-sm font-semibold text-gray-500 hover:text-gray-700 transition duration-150">
                    ← Volver a la Lista de Alumnos
                </a>

                <input type="submit" value="Guardar Cambios"
                       class="cursor-pointer px-6 py-3 bg-green-600 text-white font-bold rounded-lg shadow-lg hover:bg-green-700 transition duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-green-300 text-base">
            </div>
            
        </form>
        <?php endif; ?>

    </div>
</body>
</html>