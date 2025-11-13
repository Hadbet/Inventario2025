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

// PARTE 1: Buscar Storage Units que existen pero en ubicación diferente
if (!empty($storageUnits)) {
    foreach ($storageUnits as $storageUnitQuoted) {
        $storageUnit = trim($storageUnitQuoted, "'");

        $consulta = "
            SELECT 
                su.Id_StorageUnit,
                su.Numero_Parte,
                su.Storage_Bin,
                su.Storage_Type,
                CASE
                    WHEN bi.TercerConteo IS NOT NULL AND bi.TercerConteo > 0 THEN bi.TercerConteo
                    WHEN bi.SegundoConteo IS NOT NULL AND bi.SegundoConteo > 0 THEN bi.SegundoConteo
                    WHEN bi.PrimerConteo IS NOT NULL AND bi.PrimerConteo > 0 THEN bi.PrimerConteo
                    ELSE 0
                END AS ConteoFinal
            FROM Storage_Unit su
            LEFT JOIN Bitacora_Inventario bi ON su.FolioMarbete = bi.FolioMarbete
            WHERE su.Id_StorageUnit = '$storageUnit'
            AND su.Estatus = 1
        ";

        $resultado = mysqli_query($conexion, $consulta);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $fila = mysqli_fetch_assoc($resultado);

            // Encontrar el item original del archivo
            $itemOriginal = null;
            foreach ($data as $item) {
                if (isset($item['storageUnit']) && $item['storageUnit'] === $storageUnit) {
                    $itemOriginal = $item;
                    break;
                }
            }

            if ($itemOriginal) {
                // Verificar si está en una ubicación diferente
                $ubicacionDiferente = false;

                if ($fila['Storage_Bin'] !== $itemOriginal['storBin']) {
                    $ubicacionDiferente = true;
                }

                if (isset($itemOriginal['storageType']) && $fila['Storage_Type'] !== $itemOriginal['storageType']) {
                    $ubicacionDiferente = true;
                }

                if ($ubicacionDiferente) {
                    $materialesEspeciales[] = [
                        'inventoryNo' => $itemOriginal['inventoryNo'] ?? '',
                        'page' => $itemOriginal['page'] ?? '',
                        'storageType' => $fila['Storage_Type'],
                        'storBin' => $fila['Storage_Bin'],
                        'storageUnit' => $fila['Id_StorageUnit'],
                        'materialNo' => $fila['Numero_Parte'],
                        'conteoFinal' => $fila['ConteoFinal'],
                        'uom' => $itemOriginal['uom'] ?? 'PC',
                        'estado' => 'Ubicación real diferente al TXT (TXT: ' . $itemOriginal['storBin'] . ', ' . ($itemOriginal['storageType'] ?? '') . ')'
                    ];
                }
            }
        }
    }
}

// PARTE 2: Buscar materiales adicionales en las ubicaciones del archivo que no están incluidos
if (!empty($storBins) && !empty($storageTypes)) {
    $condicionStorageUnits = !empty($storageUnits)
        ? "AND su.Id_StorageUnit NOT IN (" . implode(',', $storageUnits) . ")"
        : "";

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
                ELSE 0
            END AS conteoFinal
        FROM Storage_Unit su
        LEFT JOIN Bitacora_Inventario bi ON su.FolioMarbete = bi.FolioMarbete
        WHERE su.Storage_Bin IN (" . implode(',', $storBins) . ")
        AND su.Storage_Type IN (" . implode(',', $storageTypes) . ")
        $condicionStorageUnits
        AND su.Estatus = 1
    ";

    $resultado = mysqli_query($conexion, $consulta);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $materialesEspeciales[] = [
                'inventoryNo' => $data[0]['inventoryNo'] ?? '',
                'page' => $data[0]['page'] ?? '',
                'storageType' => $fila['storageType'],
                'storBin' => $fila['storBin'],
                'storageUnit' => $fila['storageUnit'],
                'materialNo' => $fila['materialNo'],
                'conteoFinal' => $fila['conteoFinal'],
                'uom' => 'PC',
                'estado' => 'No incluido en el reporte'
            ];
        }
    }
}

// Cerrar la conexión
mysqli_close($conexion);

// Enviar resultados al frontend
echo json_encode($materialesEspeciales);
?>