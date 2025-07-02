<?php
include_once('db/db_Inventario.php');

try {
    $sun = $_POST['sun'];
    $marbete = $_POST['marbete'];
    $estatus = $_POST['estatus'];

    $con = new LocalConector();
    $conex=$con->conectar();

    $stmt = $conex->prepare("UPDATE `Storage_Unit` SET `Estatus`=?,`FolioMarbete`=?,`Conteo`='3' WHERE `Id_StorageUnit` = ?");
    $stmt->bind_param("sss", $estatus, $marbete,$sun);

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Se actualizo su registro"]);
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