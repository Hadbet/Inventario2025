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

// Preparar la lista de materiales procesados para excluirlos del reporte
$storBins = [];
$materialNos = [];

foreach ($data as $item) {
    $storBins[] = "'" . mysqli_real_escape_string($conexion, $item['storBin']) . "'";
    $materialNos[] = "'" . mysqli_real_escape_string($conexion, $item['materialNo']) . "'";
}

// Si no hay datos para filtrar, devolver un array vacío
if (empty($storBins) || empty($materialNos)) {
    echo json_encode([]);
    exit();
}

// Consultar materiales que están en la misma ubicación pero no en el archivo procesado
$consulta = "
    SELECT 
        bi.NumeroParte as materialNo,
        bi.StorageBin as storBin,
        CASE
            WHEN bi.TercerConteo IS NOT NULL AND bi.TercerConteo > 0 THEN bi.TercerConteo
            WHEN bi.SegundoConteo IS NOT NULL AND bi.SegundoConteo > 0 THEN bi.SegundoConteo
            WHEN bi.PrimerConteo IS NOT NULL AND bi.PrimerConteo > 0 THEN bi.PrimerConteo
            ELSE 0
        END AS conteoFinal,
        (SELECT StType FROM Bin WHERE StBin = bi.StorageBin LIMIT 1) AS storageType,
        '' as descripcion,
        'No incluido en el reporte' as estado
    FROM Bitacora_Inventario bi
    WHERE bi.Estatus = 1
    AND bi.StorageBin IN (" . implode(',', $storBins) . ")
    AND bi.NumeroParte NOT IN (" . implode(',', $materialNos) . ")
";

$resultado = mysqli_query($conexion, $consulta);

if (!$resultado) {
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conexion)]);
    exit();
}

$materialesFaltantes = [];

while ($fila = mysqli_fetch_assoc($resultado)) {
    $materialesFaltantes[] = [
        'storBin' => $fila['storBin'],
        'materialNo' => $fila['materialNo'],
        'conteoFinal' => $fila['conteoFinal'],
        'storageType' => $fila['storageType'],
        'descripcion' => $fila['descripcion'],
        'estado' => $fila['estado']
    ];
}

// Cerrar la conexión
mysqli_close($conexion);

// Enviar resultados al frontend
echo json_encode($materialesFaltantes);
?>