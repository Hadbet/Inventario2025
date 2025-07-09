<?php

include_once('db/db_Inventario.php');


ContadorApu();

function ContadorApu()
{
    $con = new LocalConector();
    $conex = $con->conectar();

    $datos = mysqli_query($conex, "SELECT 
                                        b.Id_Bitacora,
                                        b.NumeroParte,
                                        b.Fecha,
                                        b.Usuario,
                                        p.Descripcion,
                                        p.UM,
                                        p.ProfitCtr,
                                        p.Costo,
                                        p.Por,
                                        (p.Costo / p.Por) AS CostoUnitario,
                                        CASE 
                                            WHEN b.TercerConteo IS NOT NULL AND b.TercerConteo != 0 THEN b.TercerConteo
                                            WHEN b.SegundoConteo IS NOT NULL AND b.SegundoConteo != 0 THEN b.SegundoConteo
                                            ELSE b.PrimerConteo
                                        END AS Cantidad,
                                        CASE 
                                            WHEN b.TercerConteo IS NOT NULL AND b.TercerConteo != 0 THEN b.TercerConteo * (p.Costo / p.Por)
                                            WHEN b.SegundoConteo IS NOT NULL AND b.SegundoConteo != 0 THEN b.SegundoConteo * (p.Costo / p.Por)
                                            ELSE b.PrimerConteo * (p.Costo / p.Por)
                                        END AS Total
                                    
                                    FROM 
                                        Bitacora_Inventario b
                                    JOIN 
                                        Parte p ON b.NumeroParte = p.GrammerNo
                                    WHERE 
                                        b.Estatus = 1;");
    $resultado = mysqli_fetch_all($datos, MYSQLI_ASSOC);
    echo json_encode(array("data" => $resultado));
}


?>