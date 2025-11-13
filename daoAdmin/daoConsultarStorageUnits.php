<?php
include_once('connection.php');

// Configurar encabezados para respuesta JSON
header('Content-Type: application/json');

// Leer los datos enviados desde el frontend
$data = json_decode(file_get_contents('php://input'), true);

if (empty($data) || empty($data['storageUnits'])) {
    echo json_encode(['error' => 'No se recibieron datos de Storage Units']);
    exit();
}

// Conectar a la base de datos
$con = new LocalConector();
$conexion = $con->conectar();

if (!$conexion) {
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit();
}

// Preparar lista de Storage Units
$storageUnits = array_map(function($item) use ($conexion) {
    return "'" . mysqli_real_escape_string($conexion, $item) . "'";
}, $data['storageUnits']);

// Consultar los datos de los Storage Units
$consulta = "
    SELECT 
        su.Id_StorageUnit as storageUnit,
        su.Numero_Parte as materialNo,
        su.Storage_Bin as storBin,
        su.Storage_Type as storageType,
        CASE
            WHEN bi.TercerConteo IS NOT NULL AND bi.TercerConteo > 0 THEN bi.TercerConteo
            WHEN bi.SegundoConteo IS NOT NULL AND bi.SegundoConteo > 0 THEN bi.SegundoConteo
            WHEN bi.PrimerConteo IS NOT NULL AND bi.PrimerConteo > 0 THEN bi.PrimerConteo
            ELSE su.Cantidad
        END AS conteoFinal
    FROM Storage_Unit su
    LEFT JOIN Bitacora_Inventario bi ON su.FolioMarbete = bi.FolioMarbete
    WHERE su.Id_StorageUnit IN (" . implode(',', $storageUnits) . ")
    AND su.Estatus = 1
";

$resultado = mysqli_query($conexion, $consulta);

if (!$resultado) {
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conexion)]);
    exit();
}

$storageUnitsData = [];

while ($fila = mysqli_fetch_assoc($resultado)) {
    $storageUnitsData[] = [
        'storageUnit' => $fila['storageUnit'],
        'materialNo' => $fila['materialNo'],
        'storBin' => $fila['storBin'],
        'storageType' => $fila['storageType'],
        'conteoFinal' => $fila['conteoFinal']
    ];
}

// Cerrar la conexión
mysqli_close($conexion);

// Enviar resultados al frontend
echo json_encode($storageUnitsData);
?>