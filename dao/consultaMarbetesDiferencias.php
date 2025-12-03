<?php
include_once('db/db_Inventario.php');

$area = $_GET['area'];

consultarDiferencias($area);

function consultarDiferencias($area)
{
    $con = new LocalConector();
    $conex = $con->conectar();

    // Consulta para obtener marbetes con diferencias (Estatus = 2)
    $query = "SELECT 
                bi.FolioMarbete,
                bi.NumeroParte,
                bi.StorageBin,
                bi.PrimerConteo,
                bi.Usuario,
                bi.Comentario,
                bi.AreaNombre,
                COUNT(su.Id_StorageUnit) as TotalStorageUnits,
                SUM(CASE WHEN su.Estatus = 1 THEN 1 ELSE 0 END) as StorageEscaneados,
                SUM(CASE WHEN su.Estatus = 0 THEN 1 ELSE 0 END) as StorageFaltantes
              FROM `Bitacora_Inventario` bi
              LEFT JOIN `Storage_Unit` su ON bi.FolioMarbete = su.FolioMarbete
              WHERE bi.Area = '$area' AND bi.Estatus = 2
              GROUP BY bi.FolioMarbete
              ORDER BY bi.FolioMarbete ASC";

    $datos = mysqli_query($conex, $query);

    if (mysqli_num_rows($datos) > 0) {
        $resultado = mysqli_fetch_all($datos, MYSQLI_ASSOC);
        echo json_encode(array("success" => true, "data" => $resultado));
    } else {
        echo json_encode(array("success" => false, "message" => "No hay marbetes con diferencias"));
    }

    mysqli_close($conex);
}
?>