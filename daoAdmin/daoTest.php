<?php
// ========================================
// daoConsultarStorageUnitsCantidades.php
// Busca Storage Units y devuelve cantidades
// ========================================

include_once('connection.php');

// Configurar encabezados
header('Content-Type: application/json');

// Leer datos del frontend
$data = json_decode(file_get_contents('php://input'), true);

if (empty($data) || empty($data['storageUnits'])) {
    echo json_encode(['error' => 'No se recibieron Storage Units']);
    exit();
}

// Conectar a la base de datos
$con = new LocalConector();
$conexion = $con->conectar();

if (!$conexion) {
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit();
}

// Preparar lista de Storage Units (escapar para SQL)
$storageUnits = array_map(function($item) use ($conexion) {
    return "'" . mysqli_real_escape_string($conexion, trim($item)) . "'";
}, $data['storageUnits']);

// Consulta SQL
$consulta = "
    SELECT 
        su.Id_StorageUnit as storageUnit,
        su.Numero_Parte as materialNo,
        su.Storage_Bin as storBin,
        su.Storage_Type as storageType,
        su.Cantidad as cantidad
    FROM Storage_Unit su
    WHERE su.Id_StorageUnit IN (" . implode(',', $storageUnits) . ")
        AND su.Estatus = 1
";

$resultado = mysqli_query($conexion, $consulta);

if (!$resultado) {
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conexion)]);
    exit();
}

// Crear un mapa de Storage Unit => Cantidad
$mapaCantidades = [];
while ($fila = mysqli_fetch_assoc($resultado)) {
    $mapaCantidades[$fila['storageUnit']] = [
        'cantidad' => $fila['cantidad'],
        'materialNo' => $fila['materialNo'],
        'storBin' => $fila['storBin'],
        'storageType' => $fila['storageType']
    ];
}

// Cerrar conexión
mysqli_close($conexion);

// Devolver resultados
echo json_encode($mapaCantidades);
?>