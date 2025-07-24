<?php
include_once('db/db_Inventario.php');

try {
    $nombre = $_POST['nombre'];
    $numeroParte = $_POST['numeroParte'];
    $storageBin = $_POST['storageBin'];
    $cantidad = $_POST['cantidad'];
    $folioMarbete = $_POST['folioMarbete'];
    $conteo = $_POST['conteo'];

    $parts = explode('.', $folioMarbete);

    $marbete = intval($parts[0]);

    $con = new LocalConector();
    $conex=$con->conectar();

    if ($conteo == 1) {
        $stmt = $conex->prepare("UPDATE `Bitacora_Inventario` SET `NumeroParte`=?,  `Usuario` =?,  `UsuarioVerificacion`=?, `Estatus`='1', `PrimerConteo`=? WHERE `FolioMarbete`=? ");
    } elseif ($conteo == 2) {
        $stmt = $conex->prepare("UPDATE `Bitacora_Inventario` SET  `UserSeg`=?, `SegundoConteo`=?, `SegFolio`=1 WHERE `FolioMarbete`=? AND `Estatus` = 1");
    } elseif ($conteo == 3) {
        $stmt = $conex->prepare("UPDATE `Bitacora_Inventario` SET  `TercerConteo`=? WHERE `FolioMarbete`=? AND `Estatus` = 1");
    }

    if ($conteo == 1){
        $stmt->bind_param("sssss",$numeroParte,$nombre,$nombre, $cantidad, $marbete);

    }
    if ($conteo == 2){
        $stmt->bind_param("ss", $nombre,$cantidad, $marbete);

    }
    if ($conteo == 3){
        $stmt->bind_param("ss", $cantidad, $marbete);
    }

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Actualización exitosa"]);
    } else {
        echo json_encode(["success" => false, "message" => "No se pudo actualizar el registro"]);
    }

    $stmt->close();
    $conex->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>