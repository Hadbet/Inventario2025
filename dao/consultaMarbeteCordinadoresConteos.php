<?php

include_once('db/db_Inventario.php');


$marbete = $_GET['marbete'];

ContadorApu($marbete);

function ContadorApu($marbete)
{
    $con = new LocalConector();
    $conex = $con->conectar();

    $datos = mysqli_query($conex, "SELECT 
    B.Id_Bitacora, 
    B.NumeroParte, 
    B.FolioMarbete, 
    B.Fecha, 
    B.Usuario, 
    B.UsuarioVerificacion, 
    B.Estatus, 
    B.PrimerConteo, 
    B.SegundoConteo, 
    B.TercerConteo, 
    B.SegFolio, 
    B.UserSeg, 
    B.Comentario, 
    B.StorageBin, 
    B.StorageType, 
    B.Area, 
    COALESCE(S.Id_StorageUnit, 'NA') AS StorageUnit, 
    COALESCE(S.Cantidad, 'NA') AS CantidadStorage, 
    COALESCE(S.Estatus, 0) AS EstatusStorage 
FROM 
    Bitacora_Inventario B 
LEFT JOIN 
    Storage_Unit S ON (B.StorageBin = S.Storage_Bin AND B.NumeroParte = S.Numero_Parte) 
WHERE 
    B.FolioMarbete = '$marbete'
    AND B.Estatus IN (0, 1, 5);");

    $resultado = mysqli_fetch_all($datos, MYSQLI_ASSOC);
    echo json_encode(array("data" => $resultado));
}


?>