<?php

include_once('db/db_Inventario.php');


$parte = $_GET['parte'];
$area = $_GET['area'];

ContadorApu($parte,$area);

function ContadorApu($parte,$area)
{
    $con = new LocalConector();
    $conex = $con->conectar();

    $datos = mysqli_query($conex, "SELECT 
                                            i.`STLocation`, 
                                            i.`STBin`, 
                                            i.`STType`, 
                                            i.`GrammerNo`, 
                                            i.`Cantidad`, 
                                            i.`AreaCve`,
                                            p.`Descripcion`, 
                                            p.`UM`, 
                                            p.`ProfitCtr`, 
                                            p.`Costo`, 
                                            p.`Por`
                                        FROM 
                                            `InventarioSap` i
                                        INNER JOIN 
                                            `Parte` p ON i.`GrammerNo` = p.`GrammerNo`
                                        WHERE 
                                            i.`GrammerNo` = '$parte' 
                                            AND i.`AreaCve` = '$area';");

    $resultado = mysqli_fetch_all($datos, MYSQLI_ASSOC);
    echo json_encode(array("data" => $resultado));
}


?>