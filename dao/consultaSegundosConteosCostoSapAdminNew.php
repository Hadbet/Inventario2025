<?php

include_once('db/db_Inventario.php');


ContadorApu();

function ContadorApu()
{
    $con = new LocalConector();
    $conex = $con->conectar();

    $datos = mysqli_query($conex, "SELECT 
                                            i.STLocation,
                                            i.STBin,
                                            i.STType,
                                            i.GrammerNo,
                                            i.Cantidad,
                                            p.Descripcion,
                                            p.UM,
                                            p.ProfitCtr,
                                            p.Costo,
                                            p.Por,
                                            (p.Costo / p.Por) AS CostoUnitario,
                                            i.Cantidad * (p.Costo / p.Por) AS Total
                                        
                                        FROM 
                                            InventarioSap i
                                        JOIN 
                                            Parte p ON i.GrammerNo = p.GrammerNo;");
    $resultado = mysqli_fetch_all($datos, MYSQLI_ASSOC);
    echo json_encode(array("data" => $resultado));
}


?>