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

// Preparar las listas para filtrar
$storageUnits = [];
$storBins = [];
$storageTypes = [];

foreach ($data as $item) {
    if (!empty($item['storageUnit'])) {
        $storageUnits[] = "'" . mysqli_real_escape_string($conexion, $item['storageUnit']) . "'";
    }

    if (!empty($item['storBin'])) {
        $storBins[] = "'" . mysqli_real_escape_string($conexion, $item['storBin']) . "'";
    }

    if (!empty($item['storageType'])) {
        $storageTypes[] = "'" . mysqli_real_escape_string($conexion, $item['storageType']) . "'";
    }
}

$materialesEspeciales = [];

// Si hay Storage Units para filtrar
if (!empty($storageUnits)) {
    // Consultar materiales que están en la base de datos pero no en el archivo
    $consulta = "
        SELECT 
            su.Id_StorageUnit as storageUnit,
            su.Numero_Parte as materialNo,
            su.Storage_Bin as storBin,
            su.Storage_Type as storageType,
            su.Cantidad as cantidad,
            CASE
                WHEN bi.TercerConteo IS NOT NULL AND bi.TercerConteo > 0 THEN bi.TercerConteo
                WHEN bi.SegundoConteo IS NOT NULL AND bi.SegundoConteo > 0 THEN bi.SegundoConteo
                WHEN bi.PrimerConteo IS NOT NULL AND bi.PrimerConteo > 0 THEN bi.PrimerConteo
                ELSE 0
            END AS conteoFinal
        FROM Storage_Unit su
        LEFT JOIN Bitacora_Inventario bi ON su.FolioMarbete = bi.FolioMarbete
        WHERE su.Storage_Bin IN (" . implode(',', $storBins) . ")
        AND su.Storage_Type IN (" . implode(',', $storageTypes) . ")
        AND su.Id_StorageUnit NOT IN (" . implode(',', $storageUnits) . ")
        AND su.Estatus = 1
    ";

    $resultado = mysqli_query($conexion, $consulta);

    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $materialesEspeciales[] = [
                'storageUnit' => $fila['storageUnit'],
                'materialNo' => $fila['materialNo'],
                'storBin' => $fila['storBin'],
                'storageType' => $fila['storageType'],
                'conteoFinal' => $fila['conteoFinal'],
                'inventoryNo' => $data[0]['inventoryNo'] ?? '',
                'page' => $data[0]['page'] ?? '',
                'uom' => 'PC', // Por defecto, se podría mejorar
                'estado' => 'No incluido en el reporte'
            ];
        }
    }
} else if (!empty($storBins) && !empty($storageTypes)) {
    // Si no hay Storage Units, filtrar solo por Storage Bin y Storage Type
    $consulta = "
        SELECT 
            su.Id_StorageUnit as storageUnit,
            su.Numero_Parte as materialNo,
            su.Storage_Bin as storBin,
            su.Storage_Type as storageType,
            su.Cantidad as cantidad,
            CASE
                WHEN bi.TercerConteo IS NOT NULL AND bi.TercerConteo > 0 THEN bi.TercerConteo
                WHEN bi.SegundoConteo IS NOT NULL AND bi.SegundoConteo > 0 THEN bi.SegundoConteo
                WHEN bi.PrimerConteo IS NOT NULL AND bi.PrimerConteo > 0 THEN bi.PrimerConteo
                ELSE 0
            END AS conteoFinal
        FROM Storage_Unit su
        LEFT JOIN Bitacora_Inventario bi ON su.FolioMarbete = bi.FolioMarbete
        WHERE su.Storage_Bin IN (" . implode(',', $storBins) . ")
        AND su.Storage_Type IN (" . implode(',', $storageTypes) . ")
        AND su.Estatus = 1
    ";

    $resultado = mysqli_query($conexion, $consulta);

    if ($resultado) {
        // Verificar cuáles están en el archivo y cuáles no
        $storBinsEnArchivo = [];
        foreach ($data as $item) {
            if (!empty($item['storBin'])) {
                $storBinsEnArchivo[] = $item['storBin'];
            }
        }

        while ($fila = mysqli_fetch_assoc($resultado)) {
            $enArchivo = in_array($fila['storBin'], $storBinsEnArchivo);

            $materialesEspeciales[] = [
                'storageUnit' => $fila['storageUnit'],
                'materialNo' => $fila['materialNo'],
                'storBin' => $fila['storBin'],
                'storageType' => $fila['storageType'],
                'conteoFinal' => $fila['conteoFinal'],
                'inventoryNo' => $data[0]['inventoryNo'] ?? '',
                'page' => $data[0]['page'] ?? '',
                'uom' => 'PC',
                'estado' => $enArchivo ? 'Encontrado sin Storage Unit' : 'No incluido en el reporte'
            ];
        }
    }
}

// Cerrar la conexión
mysqli_close($conexion);

// Enviar resultados al frontend
echo json_encode($materialesEspeciales);
?>