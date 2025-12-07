<?php

include_once('db/db_Inventario.php');

ContadorApu();

function ContadorApu()
{
    $con = new LocalConector();
    $conex = $con->conectar();

    // ✅ CAMBIO CRÍTICO: LEFT JOIN desde Bitacora_Inventario para incluir items que NO están en SAP
    $datos = mysqli_query($conex, "SELECT 
    BInv.NumeroParte AS 'GrammerNo',
    BInv.StorageBin AS 'STBin',
    COALESCE(ISap.Cantidad, 0) AS 'Total_InventarioSap',
    BInv.NumeroParte, 
    BInv.StorageBin, 
    SUM(
        COALESCE( 
            CASE WHEN BInv.TercerConteo != 0 THEN BInv.TercerConteo END, 
            CASE WHEN BInv.SegundoConteo != 0 THEN BInv.SegundoConteo END, 
            BInv.PrimerConteo 
        )
    ) AS 'Total_Bitacora_Inventario', 
    BInv.FolioMarbete,
    Part.Descripcion,
    Part.UM,
    CASE 
        WHEN Part.Por IS NULL OR Part.Por = 0 OR Part.Costo IS NULL OR Part.Costo = 0 THEN 0
        ELSE (Part.Por / Part.Costo)
    END AS 'CostoUnitario',
    CASE 
        WHEN BInv.TercerConteo != 0 THEN 'Con tercer conteo'
        WHEN BInv.SegundoConteo != 0 THEN 'Con segundo conteo'
        WHEN BInv.PrimerConteo != 0 THEN 'Con primer conteo'
        ELSE ''
    END AS 'Comentario'
FROM 
    Bitacora_Inventario BInv
LEFT JOIN 
    InventarioSap ISap ON BInv.NumeroParte = ISap.GrammerNo 
        AND BInv.StorageBin = ISap.STBin 
        AND BInv.StorageType = ISap.STType
LEFT JOIN
    Parte Part ON BInv.NumeroParte = Part.GrammerNo
WHERE
    BInv.Estatus = 1
GROUP BY 
    BInv.NumeroParte,
    BInv.StorageBin,
    BInv.FolioMarbete,
    Part.Descripcion,
    Part.UM,
    Part.Por,
    Part.Costo,
    ISap.Cantidad");

    $resultado = mysqli_fetch_all($datos, MYSQLI_ASSOC);
    echo json_encode(array("data" => $resultado));
}

?>