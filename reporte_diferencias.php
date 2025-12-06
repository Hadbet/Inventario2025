<?php
session_start();
$rol = $_SESSION['rol'];
$area = $_SESSION['area'];
$areaNombre = $_SESSION['AreaNombre'];
$bin = $_SESSION['StBin'];
$nomina = $_SESSION['nomina'];
$nombre = $_SESSION['nombre'];

if (strlen($nomina) == 1) {
    $nomina = "0000000" . $nomina;
}
if (strlen($nomina) == 2) {
    $nomina = "000000" . $nomina;
}
if (strlen($nomina) == 3) {
    $nomina = "00000" . $nomina;
}
if (strlen($nomina) == 4) {
    $nomina = "0000" . $nomina;
}
if (strlen($nomina) == 5) {
    $nomina = "000" . $nomina;
}
if (strlen($nomina) == 6) {
    $nomina = "00" . $nomina;
}
if (strlen($nomina) == 7) {
    $nomina = "0" . $nomina;
} ?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>GRAMMER INVENTARIO</title>
    <?php include 'estaticos/stylesEstandar.php'; ?>
    <script src="lib/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="css/generales.css">
</head>

<body class="vertical  light  ">
<div class="wrapper">

    <?php
    require_once('estaticos/navegador.php');
    ?>

    <main role="main" class="main-content">
        <center><img src="images/tituloInventario.png" style="width: 50%"></center>

        <div class="row align-items-center my-4">
            <div class="col">
                <h2 class="h3 mb-0 page-title">Valores Inventario Sap</h2>
            </div>
        </div>

        <div class="row">

            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <span class="h2 mb-0" id="lblDineroSap"></span>
                                <p class="small text-muted mb-0">de Valor de Inventario Sap</p>
                                <span class="badge badge-pill badge-success"></span>
                            </div>
                            <div class="col-auto">
                                <span class="fe fe-32 fe-shopping-bag text-muted mb-0"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <span class="h2 mb-0" id="lblCantidadSap"></span>
                                <p class="small text-muted mb-0">de cantidad de Inventario Sap</p>
                                <span class="badge badge-pill badge-success"></span>
                            </div>
                            <div class="col-auto">
                                <span class="fe fe-32 fe-shopping-bag text-muted mb-0"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row align-items-center my-4">
            <div class="col">
                <h2 class="h3 mb-0 page-title">Valores Inventario Real</h2>
            </div>
        </div>

        <div class="row">

            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <span class="h2 mb-0" id="lblDineroReal"></span>
                                <p class="small text-muted mb-0">de Valor de Inventario Real</p>
                                <span class="badge badge-pill badge-success"></span>
                            </div>
                            <div class="col-auto">
                                <span class="fe fe-32 fe-shopping-bag text-muted mb-0"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <span class="h2 mb-0" id="lblCantidadReal"></span>
                                <p class="small text-muted mb-0">de cantidad de Inventario Real</p>
                                <span class="badge badge-pill badge-success"></span>
                            </div>
                            <div class="col-auto">
                                <span class="fe fe-32 fe-shopping-bag text-muted mb-0"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row align-items-center my-4">
            <div class="col">
                <h2 class="h3 mb-0 page-title">Diferencias de Inventario</h2>
            </div>
        </div>

        <div class="row">

            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <span class="h2 mb-0" id="lblDinero"></span>
                                <p class="small text-muted mb-0">de diferencia de costo</p>
                                <span class="badge badge-pill badge-success"></span>
                            </div>
                            <div class="col-auto">
                                <span class="fe fe-32 fe-shopping-bag text-muted mb-0"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <span class="h2 mb-0" id="lblCantidad"></span>
                                <p class="small text-muted mb-0">de diferencia de coteo</p>
                                <span class="badge badge-pill badge-success"></span>
                            </div>
                            <div class="col-auto">
                                <span class="fe fe-32 fe-shopping-bag text-muted mb-0"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <div class="container-fluid">

            <div class="row align-items-center my-4">
                <div class="col">
                    <h2 class="h3 mb-0 page-title">Reporte de diferencias</h2>
                </div>
            </div>

            <table class="table datatables" id="dataTable-1">
                <thead>
                <tr>
                    <th>Marbete</th>
                    <th>Número Parte</th>
                    <th>Storage Bin</th>
                    <th>Captura</th>
                    <th>Inventario Sap</th>
                    <th>Diferencia</th>
                    <th>Área</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>


        </div> <!-- .container-fluid -->
    </main> <!-- main -->
</div> <!-- .wrapper -->

<?php include 'estaticos/scriptEstandar.php'; ?>

<script src="js/apps.js"></script>
<script src="assets/scanapp.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-56159088-1"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>

<script>

    verificacionDiferencia();

    function verificacionDiferencia() {
        var totalDinero = 0.0;
        var totalCantidad = 0.0;

        $.getJSON('https://grammermx.com/Logistica/Inventario2025/dao/consultaSegundosConteosCostoAdminNew.php', function (data) {

            if (data && data.data && data.data.length > 0) {
                for (var i = 0; i < data.data.length; i++) {
                    // ✅ Validar que los valores sean números válidos
                    var valorDinero = parseFloat(data.data[i].Total);
                    var valorCantidad = parseFloat(data.data[i].Cantidad);

                    if (!isNaN(valorDinero)) {
                        totalDinero += valorDinero;
                    }
                    if (!isNaN(valorCantidad)) {
                        totalCantidad += valorCantidad;
                    }
                }

                console.log("Total Dinero Real:", totalDinero);
                console.log("Total Cantidad Real:", totalCantidad);

                verificarDiferenciaSap(totalDinero, totalCantidad);
            } else {
                Swal.fire({
                    title: "Tu conteo esta bien",
                    text: "No necesitas ir a segundos conteos",
                    icon: "success"
                });
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Error al cargar datos reales:", textStatus, errorThrown);
        });
    }

    function verificarDiferenciaSap(totalDinero, totalCantidad) {
        $.getJSON('https://grammermx.com/Logistica/Inventario2025/dao/consultaSegundosConteosCostoSapAdminNew.php', function (data) {
            if (data && data.data && data.data.length > 0) {

                // ✅ Calcular totales con validación
                const totalDineroSap = data.data.reduce((sum, item) => {
                    const valor = parseFloat(item.Total);
                    return sum + (isNaN(valor) ? 0 : valor);
                }, 0);

                const totalCantidadSap = data.data.reduce((sum, item) => {
                    const valor = parseFloat(item.Cantidad);
                    return sum + (isNaN(valor) ? 0 : valor);
                }, 0);

                console.log("Total Dinero SAP:", totalDineroSap);
                console.log("Total Cantidad SAP:", totalCantidadSap);

                // ✅ Validar que los valores no sean NaN antes de mostrar
                const formatter = new Intl.NumberFormat('es-MX', {
                    style: 'currency',
                    currency: 'MXN'
                });

                // Mostrar valores reales
                document.getElementById("lblDineroReal").innerText = isNaN(totalDinero) ? '$0.00' : formatter.format(totalDinero);
                document.getElementById("lblCantidadReal").innerText = isNaN(totalCantidad) ? '0.00' : totalCantidad.toFixed(2);

                // Mostrar valores SAP
                document.getElementById("lblDineroSap").innerText = isNaN(totalDineroSap) ? '$0.00' : formatter.format(totalDineroSap);
                document.getElementById("lblCantidadSap").innerText = isNaN(totalCantidadSap) ? '0.00' : totalCantidadSap.toFixed(2);

                // Calcular y mostrar diferencias
                var diferenciaDinero = totalDineroSap - totalDinero;
                var diferenciaCantidad = totalCantidadSap - totalCantidad;

                console.log("Diferencia Dinero:", diferenciaDinero);
                console.log("Diferencia Cantidad:", diferenciaCantidad);

                document.getElementById("lblDinero").innerText = isNaN(diferenciaDinero) ? '$0.00' : formatter.format(diferenciaDinero);
                document.getElementById("lblCantidad").innerText = isNaN(diferenciaCantidad) ? '0.00' : diferenciaCantidad.toFixed(2);

                crearTabla();
            } else {
                Swal.fire({
                    title: "Tu conteo está bien",
                    text: "No necesitas ir a segundos conteos",
                    icon: "success"
                });
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Error al cargar datos SAP:", textStatus, errorThrown);
        });
    }

    function crearTabla() {
        $.ajax({
            url: 'https://grammermx.com/Logistica/Inventario2025/dao/consultaSegundosConteosCostoAdminAux.php',
            dataType: 'json',
            success: function (data) {

                var table = $('#dataTable-1').DataTable({
                    data: data.data,
                    columns: [
                        {data: 'FolioMarbete'},
                        {data: 'NumeroParte'},
                        {data: 'StorageBin'},
                        {
                            data: 'CantidadContada',
                            render: function (data, type, row) {
                                var valor = parseFloat(data);
                                return isNaN(valor) ? '0.00' : valor.toFixed(2);
                            }
                        },
                        {
                            data: 'CantidadInventarioSap',
                            render: function (data, type, row) {
                                var valor = parseFloat(data);
                                return isNaN(valor) ? '0.00' : valor.toFixed(2);
                            }
                        },
                        {
                            data: 'Diferencia',
                            render: function (data, type, row) {
                                var valor = parseFloat(data);
                                return isNaN(valor) ? '0.00' : valor.toFixed(2);
                            }
                        },
                        {data: 'AreaNombre'}
                    ],
                    autoWidth: true,
                    "lengthMenu": [
                        [16, 32, 64, -1],
                        [16, 32, 64, "All"]
                    ],
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'copy',
                            className: 'btn btn-sm copyButton'
                        },
                        {
                            extend: 'csv',
                            className: 'btn btn-sm csvButton'
                        },
                        {
                            extend: 'excel',
                            className: 'btn btn-sm excelButton'
                        },
                        {
                            extend: 'pdf',
                            className: 'btn btn-sm pdfButton'
                        },
                        {
                            extend: 'print',
                            className: 'btn btn-sm printButton'
                        }
                    ],
                    initComplete: function () {
                        this.api().columns().every(function () {
                            var column = this;
                            var input = document.createElement("input");
                            input.className = 'form-control form-control-sm';
                            $(input).appendTo($(column.footer()).empty())
                                .on('keyup change clear', function () {
                                    if (column.search() !== this.value) {
                                        column.search(this.value).draw();
                                    }
                                });
                        });
                    }
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar tabla:", textStatus, errorThrown);
            }
        });
    }

</script>
</body>
</html>