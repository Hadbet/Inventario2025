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
$materialNos = [];

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

    if (!empty($item['materialNo'])) {
        $materialNos[] = "'" . mysqli_real_escape_string($conexion, $item['materialNo']) . "'";
    }
}

$materialesEspeciales = [];

// PARTE 1: Verificar Storage Units que están en la base pero no coinciden con el Storage Bin y Storage Type
if (!empty($storageUnits)) {
    // Buscar Storage Units que existen pero en diferentes ubicaciones
    $consultaStorageUnitsDiferentes = "
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
        WHERE su.Id_StorageUnit IN (" . implode(',', $storageUnits) . ")
        AND (su.Storage_Bin NOT IN (" . implode(',', $storBins) . ") 
             OR su.Storage_Type NOT IN (" . implode(',', $storageTypes) . "))
        AND su.Estatus = 1
    ";

    $resultadoDiferentes = mysqli_query($conexion, $consultaStorageUnitsDiferentes);

    if ($resultadoDiferentes && mysqli_num_rows($resultadoDiferentes) > 0) {
        while ($fila = mysqli_fetch_assoc($resultadoDiferentes)) {
            // Encontrar el item original del archivo
            $itemOriginal = null;
            foreach ($data as $item) {
                if (isset($item['storageUnit']) && $item['storageUnit'] === $fila['storageUnit']) {
                    $itemOriginal = $item;
                    break;
                }
            }

            $materialesEspeciales[] = [
                'storageUnit' => $fila['storageUnit'],
                'materialNo' => $fila['materialNo'],
                'storBin' => $fila['storBin'], // Ubicación real en la base de datos
                'storageType' => $fila['storageType'], // Tipo real en la base de datos
                'conteoFinal' => $fila['conteoFinal'],
                'inventoryNo' => isset($itemOriginal['inventoryNo']) ? $itemOriginal['inventoryNo'] : $data[0]['inventoryNo'] ?? '',
                'page' => isset($itemOriginal['page']) ? $itemOriginal['page'] : $data[0]['page'] ?? '',
                'uom' => isset($itemOriginal['uom']) ? $itemOriginal['uom'] : 'PC',
                'estado' => 'Encontrado en ubicación diferente'
            ];
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
                'storageUnit' => $fila['storageUnit'],
                'materialNo' => $fila['materialNo'],
                'storBin' => $fila['storBin'],
                'storageType' => $fila['storageType'],
                'conteoFinal' => $fila['conteoFinal'],
                'inventoryNo' => $data[0]['inventoryNo'] ?? '',
                'page' => $data[0]['page'] ?? '',
                'uom' => 'PC',
                'estado' => 'No incluido en el reporte'
            ];
        }
    }
}

// PARTE 3: Verificar todos los Storage Units de la base para encontrar duplicados
if (!empty($storageUnits)) {
    // Consultar si hay Storage Units duplicados en la base de datos
    $consultaDuplicados = "
        SELECT 
            su.Id_StorageUnit as storageUnit,
            su.Numero_Parte as materialNo,
            su.Storage_Bin as storBin,
            su.Storage_Type as storageType,
            COUNT(*) as cantidad_registros,
            CASE
                WHEN bi.TercerConteo IS NOT NULL AND bi.TercerConteo > 0 THEN bi.TercerConteo
                WHEN bi.SegundoConteo IS NOT NULL AND bi.SegundoConteo > 0 THEN bi.SegundoConteo
                WHEN bi.PrimerConteo IS NOT NULL AND bi.PrimerConteo > 0 THEN bi.PrimerConteo
                ELSE 0
            END AS conteoFinal
        FROM Storage_Unit su
        LEFT JOIN Bitacora_Inventario bi ON su.FolioMarbete = bi.FolioMarbete
        WHERE su.Estatus = 1
        GROUP BY su.Id_StorageUnit
        HAVING COUNT(*) > 1
    ";

    $resultadoDuplicados = mysqli_query($conexion, $consultaDuplicados);

    if ($resultadoDuplicados && mysqli_num_rows($resultadoDuplicados) > 0) {
        while ($fila = mysqli_fetch_assoc($resultadoDuplicados)) {
            if (in_array("'" . $fila['storageUnit'] . "'", $storageUnits)) {
                $materialesEspeciales[] = [
                    'storageUnit' => $fila['storageUnit'],
                    'materialNo' => $fila['materialNo'],
                    'storBin' => $fila['storBin'],
                    'storageType' => $fila['storageType'],
                    'conteoFinal' => $fila['conteoFinal'],
                    'inventoryNo' => $data[0]['inventoryNo'] ?? '',
                    'page' => $data[0]['page'] ?? '',
                    'uom' => 'PC',
                    'estado' => 'Storage Unit duplicado en base de datos'
                ];
            }
        }
    }
}

// Cerrar la conexión
mysqli_close($conexion);

// Enviar resultados al frontend
echo json_encode($materialesEspeciales);
?>