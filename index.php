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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Alumnos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f7f7;
        }
        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    <div class="max-w-6xl mx-auto bg-white p-6 md:p-10 shadow-xl rounded-xl">
        
        <header class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center border-b pb-4">
            <h1 class="text-3xl font-extrabold text-gray-800 mb-4 sm:mb-0">
                Lista de Alumnos <span class="text-sm font-medium text-blue-600">(Sistema de Calificaciones)</span>
            </h1>
            <a href="agregar_alumno.php" 
               class="px-5 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-blue-300 text-sm">
                + Agregar Nuevo Alumno
            </a>
        </header>

        <div class="table-container mb-10">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg shadow-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/4">
                            Nombre
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            P1
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            P2
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            P3
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-800 uppercase tracking-wider">
                            Promedio
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <?php
                    $rowIndex = 0;
                    while($row = $result->fetch_assoc()) {
                        $rowIndex++;
                        $promedio = round($row['promedio'] ?? 0, 2);
                        $passed = $promedio >= 6;
                        $status = $promedio > 0 ? ($passed ? 'Pasó' : 'No pasó') : 'Sin notas';
                        
                        $total++;
                        if ($promedio >= 6) {
                            $pasaron++;
                        } else if ($promedio > 0) {
                            $no_pasaron++;
                        } 

                        $rowClass = $rowIndex % 2 == 0 ? 'bg-white' : 'bg-gray-50';

                        $statusClass = $promedio > 0 ? 
                                       ($passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') :
                                       'bg-gray-100 text-gray-600';

                        echo "<tr class='{$rowClass} hover:bg-blue-50 transition duration-150'>
                                <td class='px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900'>". htmlspecialchars($row['nombre']) ."</td>
                                <td class='px-4 py-3 whitespace-nowrap text-sm text-center text-gray-500'>". htmlspecialchars($row['parcial1'] ?? '-') ."</td>
                                <td class='px-4 py-3 whitespace-nowrap text-sm text-center text-gray-500'>". htmlspecialchars($row['parcial2'] ?? '-') ."</td>
                                <td class='px-4 py-3 whitespace-nowrap text-sm text-center text-gray-500'>". htmlspecialchars($row['parcial3'] ?? '-') ."</td>
                                <td class='px-4 py-3 whitespace-nowrap text-sm text-center font-bold text-gray-700'>{$promedio}</td>
                                <td class='px-4 py-3 whitespace-nowrap text-center'>
                                    <span class='inline-flex items-center px-3 py-0.5 rounded-full text-xs {$statusClass} font-medium'>
                                        {$status}
                                    </span>
                                </td>
                                <td class='px-4 py-3 whitespace-nowrap text-center text-sm font-medium space-x-2'>
                                    <a href='editar_alumno.php?id={$row['id']}' 
                                       class='text-blue-600 hover:text-blue-900 transition duration-150 p-2 rounded hover:bg-blue-50'>
                                        Editar
                                    </a>
                                    <a href='borrar_alumno.php?id={$row['id']}' 
                                       class='text-red-600 hover:text-red-900 transition duration-150 p-2 rounded hover:bg-red-50'
                                       onclick=\"return confirm('¿Estás seguro de que quieres borrar a ". htmlspecialchars($row['nombre']) ."?');\">
                                        Borrar
                                    </a>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <h2 class="text-xl font-bold text-gray-700 mb-4 mt-8 border-t pt-4">Resumen de Calificaciones</h2>
        <div class="max-w-sm">
            <table class="w-full divide-y divide-gray-200 border border-gray-300 rounded-lg shadow-md">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Total Alumnos
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-green-700 uppercase tracking-wider">
                            Aprobados 
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-red-700 uppercase tracking-wider">
                            Reprobados
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-lg font-bold text-blue-600">
                            <?php echo $total; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-lg font-bold text-green-600">
                            <?php echo $pasaron; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-lg font-bold text-red-600">
                            <?php echo $no_pasaron; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>
<?php
$conn->close();
?>