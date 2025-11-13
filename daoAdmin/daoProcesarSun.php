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
    $tipoLinea = isset($item['tipoLinea']) ? $item['tipoLinea'] : '';

    if ($tipoLinea === 'conStorageUnit') {
        // Caso 1: Línea con Storage Unit
        $storageUnit = mysqli_real_escape_string($conexion, $item['storageUnit']);
        $storBin = mysqli_real_escape_string($conexion, $item['storBin']);
        $materialNo = isset($item['materialNo']) ? mysqli_real_escape_string($conexion, $item['materialNo']) : '';
        $storageType = isset($item['storageType']) ? mysqli_real_escape_string($conexion, $item['storageType']) : '';
        $uom = isset($item['uom']) ? mysqli_real_escape_string($conexion, $item['uom']) : 'PC';

        // Consultar primero en Storage_Unit
        $consulta = "
            SELECT 
                su.Id_StorageUnit,
                su.Numero_Parte,
                su.Storage_Bin,
                su.Storage_Type,
                su.FolioMarbete,
                bi.PrimerConteo,
                bi.SegundoConteo,
                bi.TercerConteo,
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

            $resultados[] = [
                'storageUnit' => $storageUnit,
                'storBin' => $storBin,
                'materialNo' => $fila['Numero_Parte'],
                'conteoFinal' => $fila['ConteoFinal'],
                'storageType' => $fila['Storage_Type'],
                'inventoryNo' => $item['inventoryNo'] ?? '',
                'page' => $item['page'] ?? '',
                'uom' => $uom,
                'estado' => ($fila['Storage_Bin'] === $storBin && $fila['Storage_Type'] === $storageType)
                    ? 'Encontrado'
                    : 'Ubicación diferente'
            ];
        } else {
            // Si no se encuentra por Storage Unit, intentar por Storage Bin y Material No
            if (!empty($materialNo)) {
                $consulta = "
                    SELECT 
                        su.Id_StorageUnit,
                        su.Numero_Parte,
                        su.Storage_Bin,
                        su.Storage_Type,
                        su.FolioMarbete,
                        bi.PrimerConteo,
                        bi.SegundoConteo,
                        bi.TercerConteo,
                        CASE
                            WHEN bi.TercerConteo IS NOT NULL AND bi.TercerConteo > 0 THEN bi.TercerConteo
                            WHEN bi.SegundoConteo IS NOT NULL AND bi.SegundoConteo > 0 THEN bi.SegundoConteo
                            WHEN bi.PrimerConteo IS NOT NULL AND bi.PrimerConteo > 0 THEN bi.PrimerConteo
                            ELSE 0
                        END AS ConteoFinal
                    FROM Storage_Unit su
                    LEFT JOIN Bitacora_Inventario bi ON su.FolioMarbete = bi.FolioMarbete
                    WHERE su.Storage_Bin = '$storBin'
                    AND su.Numero_Parte = '$materialNo'
                    AND su.Estatus = 1
                ";

                $resultado = mysqli_query($conexion, $consulta);

                if ($resultado && mysqli_num_rows($resultado) > 0) {
                    $fila = mysqli_fetch_assoc($resultado);

                    $resultados[] = [
                        'storageUnit' => $storageUnit,
                        'storBin' => $storBin,
                        'materialNo' => $materialNo,
                        'conteoFinal' => $fila['ConteoFinal'],
                        'storageType' => $fila['Storage_Type'],
                        'inventoryNo' => $item['inventoryNo'] ?? '',
                        'page' => $item['page'] ?? '',
                        'uom' => $uom,
                        'estado' => 'Encontrado pero Storage Unit diferente'
                    ];
                } else {
                    // Si no se encuentra, devolver con conteo 0
                    $resultados[] = [
                        'storageUnit' => $storageUnit,
                        'storBin' => $storBin,
                        'materialNo' => $materialNo,
                        'conteoFinal' => '0',
                        'storageType' => $storageType,
                        'inventoryNo' => $item['inventoryNo'] ?? '',
                        'page' => $item['page'] ?? '',
                        'uom' => $uom,
                        'estado' => 'No encontrado'
                    ];
                }
            } else {
                // Si no tiene Material No, devolver con conteo 0
                $resultados[] = [
                    'storageUnit' => $storageUnit,
                    'storBin' => $storBin,
                    'materialNo' => '',
                    'conteoFinal' => '0',
                    'storageType' => $storageType,
                    'inventoryNo' => $item['inventoryNo'] ?? '',
                    'page' => $item['page'] ?? '',
                    'uom' => $uom,
                    'estado' => 'No encontrado'
                ];
            }
        }
    } else if ($tipoLinea === 'sinStorageUnit') {
        // Caso 2: Línea sin Storage Unit
        $storBin = mysqli_real_escape_string($conexion, $item['storBin']);
        $storageType = isset($item['storageType']) ? mysqli_real_escape_string($conexion, $item['storageType']) : '';

        // Consultar en Storage_Unit por Storage Bin
        $consulta = "
            SELECT 
                su.Id_StorageUnit,
                su.Numero_Parte,
                su.Storage_Bin,
                su.Storage_Type,
                su.FolioMarbete,
                bi.PrimerConteo,
                bi.SegundoConteo,
                bi.TercerConteo,
                CASE
                    WHEN bi.TercerConteo IS NOT NULL AND bi.TercerConteo > 0 THEN bi.TercerConteo
                    WHEN bi.SegundoConteo IS NOT NULL AND bi.SegundoConteo > 0 THEN bi.SegundoConteo
                    WHEN bi.PrimerConteo IS NOT NULL AND bi.PrimerConteo > 0 THEN bi.PrimerConteo
                    ELSE 0
                END AS ConteoFinal
            FROM Storage_Unit su
            LEFT JOIN Bitacora_Inventario bi ON su.FolioMarbete = bi.FolioMarbete
            WHERE su.Storage_Bin = '$storBin'
            AND su.Storage_Type = '$storageType'
            AND su.Estatus = 1
        ";

        $resultado = mysqli_query($conexion, $consulta);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $fila = mysqli_fetch_assoc($resultado);

            $resultados[] = [
                'storageUnit' => $fila['Id_StorageUnit'],
                'storBin' => $storBin,
                'materialNo' => $fila['Numero_Parte'],
                'conteoFinal' => $fila['ConteoFinal'],
                'storageType' => $storageType,
                'inventoryNo' => $item['inventoryNo'] ?? '',
                'page' => $item['page'] ?? '',
                'uom' => $item['uom'] ?? 'PC',
                'estado' => 'Encontrado por Storage Bin'
            ];
        } else {
            // Si no se encuentra, devolver con conteo 0
            $resultados[] = [
                'storageUnit' => '',
                'storBin' => $storBin,
                'materialNo' => '',
                'conteoFinal' => '0',
                'storageType' => $storageType,
                'inventoryNo' => $item['inventoryNo'] ?? '',
                'page' => $item['page'] ?? '',
                'uom' => $item['uom'] ?? 'PC',
                'estado' => 'No encontrado'
            ];
        }
    }
}

// Cerrar la conexión
mysqli_close($conexion);

// Enviar resultados al frontend
echo json_encode($resultados);
?>