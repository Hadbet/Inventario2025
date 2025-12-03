<?php
include_once('db/db_Inventario.php');

$area = $_GET['area'];

consultarDiferencias($area);

function consultarDiferencias($area)
{
    $con = new LocalConector();
    $conex = $con->conectar();

    // ✅ Consulta corregida con nombres de columnas exactos de la base de datos
    $query = "SELECT 
                bi.FolioMarbete,
                bi.NumeroParte,
                bi.StorageBin,
                bi.StorageType,
                bi.PrimerConteo,
                bi.Usuario,
                bi.Comentario,
                bi.Area,
                -- Total de Storage Units en este StorageBin y StorageType
                (SELECT COUNT(*) 
                 FROM Storage_Unit su 
                 WHERE su.Storage_Bin = bi.StorageBin 
                 AND su.Storage_Type = bi.StorageType
                 AND su.Numero_Parte = bi.NumeroParte) as TotalStorageUnits,
                -- Storage Units ya escaneados (tienen FolioMarbete asignado y Estatus = 1)
                (SELECT COUNT(*) 
                 FROM Storage_Unit su 
                 WHERE su.Storage_Bin = bi.StorageBin 
                 AND su.Storage_Type = bi.StorageType
                 AND su.Numero_Parte = bi.NumeroParte
                 AND su.FolioMarbete = bi.FolioMarbete
                 AND su.Estatus = 1) as StorageEscaneados,
                -- Storage Units faltantes (NO tienen este FolioMarbete o Estatus = 0)
                (SELECT COUNT(*) 
                 FROM Storage_Unit su 
                 WHERE su.Storage_Bin = bi.StorageBin 
                 AND su.Storage_Type = bi.StorageType
                 AND su.Numero_Parte = bi.NumeroParte
                 AND (su.FolioMarbete IS NULL 
                      OR su.FolioMarbete = '' 
                      OR su.Estatus = 0
                      OR (su.FolioMarbete != bi.FolioMarbete AND su.Estatus = 0))) as StorageFaltantes,
                -- Suma de cantidades faltantes
                (SELECT IFNULL(SUM(su.Cantidad), 0)
                 FROM Storage_Unit su 
                 WHERE su.Storage_Bin = bi.StorageBin 
                 AND su.Storage_Type = bi.StorageType
                 AND su.Numero_Parte = bi.NumeroParte
                 AND (su.FolioMarbete IS NULL 
                      OR su.FolioMarbete = '' 
                      OR su.Estatus = 0
                      OR (su.FolioMarbete != bi.FolioMarbete AND su.Estatus = 0))) as CantidadFaltante
              FROM `Bitacora_Inventario` bi
              WHERE bi.Area = '$area' AND bi.Estatus = 2
              ORDER BY bi.FolioMarbete ASC";

    $datos = mysqli_query($conex, $query);

    if (!$datos) {
        echo json_encode(array("success" => false, "message" => "Error en consulta: " . mysqli_error($conex)));
        return;
    }

    if (mysqli_num_rows($datos) > 0) {
        $resultado = mysqli_fetch_all($datos, MYSQLI_ASSOC);
        echo json_encode(array("success" => true, "data" => $resultado));
    } else {
        echo json_encode(array("success" => false, "message" => "No hay marbetes con diferencias"));
    }

    mysqli_close($conex);
}
?>