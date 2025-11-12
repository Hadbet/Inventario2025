<?php
include_once('connection.php');

// Configurar encabezados para respuesta JSON
header('Content-Type: application/json');

// Leer los datos enviados desde el frontend
$data = json_decode(file_get_contents('php://input'), true);

if (empty($data)) {
    echo json_encode(['error' => 'No se recibieron datos']);
    exit();
}

// Conectar a la base de datos
$con = new LocalConector();
$conexion = $con->conectar();

if (!$conexion) {
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit();
}

$resultados = [];

foreach ($data as $item) {
    $storBin = mysqli_real_escape_string($conexion, $item['storBin']);
    $materialNo = mysqli_real_escape_string($conexion, $item['materialNo']);
    $storageType = isset($item['storageType']) ? mysqli_real_escape_string($conexion, $item['storageType']) : '';

    // Si no se proporcionó el storage type, buscarlo en la tabla Bin
    if (empty($storageType)) {
        $consulta = "SELECT StType FROM Bin WHERE StBin = '$storBin' LIMIT 1";
        $resultado = mysqli_query($conexion, $consulta);

        if ($resultado && $fila = mysqli_fetch_assoc($resultado)) {
            $storageType = $fila['StType'];
        }
    }

    // Consultar la cantidad final según las reglas:
    // 1. Usar TercerConteo si existe y es mayor a 0
    // 2. Si no, usar SegundoConteo si existe y es mayor a 0
    // 3. Si no, usar PrimerConteo si existe y es mayor a 0
    // 4. Si no hay ninguno, poner 0
    $consulta = "
        SELECT 
            NumeroParte,
            StorageBin,
            PrimerConteo,
            SegundoConteo,
            TercerConteo,
            StorageType,
            (SELECT StType FROM Bin WHERE StBin = Bitacora_Inventario.StorageBin LIMIT 1) AS StType,
            CASE
                WHEN TercerConteo IS NOT NULL AND TercerConteo > 0 THEN TercerConteo
                WHEN SegundoConteo IS NOT NULL AND SegundoConteo > 0 THEN SegundoConteo
                WHEN PrimerConteo IS NOT NULL AND PrimerConteo > 0 THEN PrimerConteo
                ELSE 0
            END AS ConteoFinal
        FROM Bitacora_Inventario
        WHERE StorageBin = '$storBin' 
          AND NumeroParte = '$materialNo'
          AND Estatus = 1
    ";

    $resultado = mysqli_query($conexion, $consulta);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $fila = mysqli_fetch_assoc($resultado);

        $resultados[] = [
            'storBin' => $storBin,
            'materialNo' => $materialNo,
            'conteoFinal' => $fila['ConteoFinal'],
            'storageType' => $storageType,
            'descripcion' => ''  // La descripción se puede agregar si está disponible en la base de datos
        ];
    } else {
        // Si no se encuentra en Bitacora_Inventario, devolver con conteo 0
        $resultados[] = [
            'storBin' => $storBin,
            'materialNo' => $materialNo,
            'conteoFinal' => '0',
            'storageType' => $storageType,
            'descripcion' => ''
        ];
    }
}

// Cerrar la conexión
mysqli_close($conexion);

// Enviar resultados al frontend
echo json_encode($resultados);
?>