<?php
include_once('db/db_Inventario.php');

try {
    $storageUnits = json_decode($_POST['storageUnits'], true);
    $folioMarbete = $_POST['folioMarbete'];
    $conteo = $_POST['conteo'];

    $con = new LocalConector();
    $conex = $con->conectar();

    // ✅ Calcular el total de cantidades agregadas
    $totalCantidadAgregada = 0;
    foreach ($storageUnits as $storageUnit => $details) {
        $totalCantidadAgregada += floatval($details['cantidad']);
    }

    // ✅ 1. Actualizar Storage_Unit (marcar como contados)
    $stmt = $conex->prepare("UPDATE `Storage_Unit` SET `Estatus`='1', `Conteo`=?, `FolioMarbete`=?, `Cantidad`=? WHERE `Id_StorageUnit` = ?");

    $successCount = 0;
    $failedUnits = array();

    foreach ($storageUnits as $storageUnit => $details) {
        $cantidad = $details['cantidad'];
        $stmt->bind_param("ssss", $conteo, $folioMarbete, $cantidad, $storageUnit);

        if ($stmt->execute()) {
            $successCount++;
        } else {
            $failedUnits[] = $storageUnit;
        }
    }

    $stmt->close();

    // ✅ 2. Actualizar Bitacora_Inventario (sumar al PrimerConteo)
    // Primero obtenemos el PrimerConteo actual
    $queryBitacora = "SELECT `PrimerConteo` FROM `Bitacora_Inventario` WHERE `FolioMarbete` = ?";
    $stmtBitacora = $conex->prepare($queryBitacora);
    $stmtBitacora->bind_param("s", $folioMarbete);
    $stmtBitacora->execute();
    $resultBitacora = $stmtBitacora->get_result();
    $rowBitacora = $resultBitacora->fetch_assoc();
    $primerConteoActual = floatval($rowBitacora['PrimerConteo']);
    $stmtBitacora->close();

    // Calculamos el nuevo PrimerConteo
    $nuevoPrimerConteo = $primerConteoActual + $totalCantidadAgregada;

    // Actualizamos la Bitacora_Inventario
    $updateBitacora = "UPDATE `Bitacora_Inventario` SET `PrimerConteo` = ? WHERE `FolioMarbete` = ?";
    $stmtUpdateBitacora = $conex->prepare($updateBitacora);
    $stmtUpdateBitacora->bind_param("ds", $nuevoPrimerConteo, $folioMarbete);
    $bitacoraActualizada = $stmtUpdateBitacora->execute();
    $stmtUpdateBitacora->close();

    $conex->close();

    echo json_encode([
        "success" => true,
        "message" => "Se actualizaron " . $successCount . " storage units",
        "failedUnits" => $failedUnits,
        "bitacoraActualizada" => $bitacoraActualizada,
        "primerConteoAnterior" => $primerConteoActual,
        "cantidadAgregada" => $totalCantidadAgregada,
        "nuevoPrimerConteo" => $nuevoPrimerConteo
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>