<?php
// ========================================
// consultaReporteFinalTres.php CORREGIDO
// ========================================

include_once('db/db_Inventario.php');

ContadorApu();

function ContadorApu()
{
    $con = new LocalConector();
    $conex = $con->conectar();

    $datos = mysqli_query($conex, "SELECT 
        BInv.NumeroParte AS 'GrammerNo', 
        P.Descripcion, 
        P.UM, 
        CAST(P.Costo AS DECIMAL(15,4)) / CAST(P.Por AS DECIMAL(15,4)) AS 'Costo_Unitario',
        BInv.StorageBin, 
        BInv.FolioMarbete, 
        SUM(
            COALESCE( 
                CASE WHEN BInv.TercerConteo != 0 THEN BInv.TercerConteo END, 
                CASE WHEN BInv.SegundoConteo != 0 THEN BInv.SegundoConteo END, 
                BInv.PrimerConteo 
            )
        ) AS 'Total_Conteo', 
        CASE 
            WHEN BInv.TercerConteo != 0 THEN 'Con tercer conteo' 
            WHEN BInv.SegundoConteo != 0 THEN 'Con segundo conteo' 
            ELSE 'Con primer conteo' 
        END AS 'Comentario'
    FROM 
        Bitacora_Inventario BInv
    JOIN 
        Parte P ON BInv.NumeroParte = P.GrammerNo
    WHERE 
        BInv.Estatus = 1 
    GROUP BY 
        BInv.NumeroParte, 
        BInv.StorageBin,
        BInv.FolioMarbete,
        P.Descripcion,
        P.UM,
        P.Costo,
        P.Por,
        BInv.TercerConteo,
        BInv.SegundoConteo");

    $resultado = mysqli_fetch_all($datos, MYSQLI_ASSOC);
    echo json_encode(array("data" => $resultado));
}
?>