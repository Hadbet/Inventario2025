<?php
include_once('db/db_Inventario.php');

try {
    $nombre = $_POST['nombre'];
    $cantidad = $_POST['cantidad'];
    $folioMarbete = $_POST['folioMarbete'];

    $parts = explode('.', $folioMarbete);
    $marbete = intval($parts[0]);
    $conteo = isset($parts[1]) ? $parts[1] : null;

    $con = new LocalConector();
    $conex = $con->conectar();

    // ✅ Para verificación, actualizamos marbetes con Estatus = 2
    // Los marcamos como verificados cambiando Estatus = 1
    if ($conteo == 2) {
        $stmt = $conex->prepare("UPDATE `Bitacora_Inventario` 
                                SET `UsuarioVerificacion`=?, 
                                    `PrimerConteo`=?, 
                                    `Estatus`='1', 
                                    `SegFolio`='1' 
                                WHERE `FolioMarbete`=? 
                                AND `Estatus` = 2");

        $stmt->bind_param("sss", $nombre, $cantidad, $marbete);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode([
                "success" => true,
                "message" => "Verificación completada exitosamente",
                "marbete" => $marbete,
                "cantidad" => $cantidad
            ]);
        } else {
            // Verificar si el marbete existe y su estatus actual
            $checkStmt = $conex->prepare("SELECT `Estatus`, `PrimerConteo` FROM `Bitacora_Inventario` WHERE `FolioMarbete`=?");
            $checkStmt->bind_param("s", $marbete);
            $checkStmt->execute();
            $result = $checkStmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo json_encode([
                    "success" => false,
                    "message" => "El marbete tiene Estatus = " . $row['Estatus'] . ". Solo se pueden verificar marbetes con Estatus = 2",
                    "estatusActual" => $row['Estatus']
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "No se encontró el marbete " . $marbete
                ]);
            }

            $checkStmt->close();
        }

        $stmt->close();
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Este endpoint solo acepta conteo = 2"
        ]);
    }

    $conex->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}
?>