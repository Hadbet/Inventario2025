<?php
include_once('db/db_Inventario.php');

$marbete = $_GET['marbete'];

consultarFaltantes($marbete);

function consultarFaltantes($marbete)
{
    $con = new LocalConector();
    $conex = $con->conectar();

    // ✅ Primero obtenemos el StorageBin y StorageType del marbete
    $queryMarbete = "SELECT `StorageBin`, `StorageType`, `NumeroParte` 
                     FROM `Bitacora_Inventario` 
                     WHERE `FolioMarbete` = '$marbete'";

    $datosMarbete = mysqli_query($conex, $queryMarbete);

    if (!$datosMarbete || mysqli_num_rows($datosMarbete) == 0) {
        echo json_encode(array("success" => false, "message" => "Marbete no encontrado", "data" => array()));
        mysqli_close($conex);
        return;
    }

    $marbeteInfo = mysqli_fetch_assoc($datosMarbete);
    $storageBin = $marbeteInfo['StorageBin'];
    $storageType = $marbeteInfo['StorageType'];
    $numeroParte = $marbeteInfo['NumeroParte'];

    // ✅ Ahora buscamos los Storage Units usando Storage_Bin y Storage_Type (con guión bajo)
    // Devolvemos TODOS los storage units de esta ubicación
    $query = "SELECT 
                su.Id_StorageUnit as StorageUnit,
                su.Numero_Parte as NumeroParte,
                su.Cantidad as CantidadStorage,
                su.Estatus as EstatusStorage,
                su.FolioMarbete,
                '$marbete' as FolioMarbeteBuscado
              FROM `Storage_Unit` su
              WHERE su.Storage_Bin = '$storageBin'
              AND su.Storage_Type = '$storageType'
              AND su.Numero_Parte = '$numeroParte'
              ORDER BY su.Id_StorageUnit ASC";

    $datos = mysqli_query($conex, $query);

    if (!$datos) {
        echo json_encode(array("success" => false, "message" => "Error en consulta: " . mysqli_error($conex), "data" => array()));
        mysqli_close($conex);
        return;
    }

    if (mysqli_num_rows($datos) > 0) {
        $resultado = mysqli_fetch_all($datos, MYSQLI_ASSOC);
        echo json_encode(array("success" => true, "data" => $resultado));
    } else {
        echo json_encode(array("success" => true, "message" => "No hay storage units para este marbete", "data" => array()));
    }

    mysqli_close($conex);
}
?>