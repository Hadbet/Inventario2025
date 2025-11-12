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
}

?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistema de inventario para Grammer">
    <meta name="author" content="Grammer">
    <link rel="icon" href="favicon.ico">
    <title>GRAMMER INVENTARIO</title>
    <?php include 'estaticos/stylesEstandar.php'; ?>
    <link rel="stylesheet" href="css/styles.css">
    <!-- Estilos adicionales para tooltips -->
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/themes/light.css" />
</head>
<body class="vertical light">
<div class="wrapper">
    <?php
    require_once('estaticos/navegador.php');
    ?>
    <main role="main" class="main-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <!-- Sección de Título -->
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="page-title">Importar marbetes o Rellenar txt</h2>
                    </div>
                </div>

                <!-- Sección de Bitácora -->
                <div class="col-12">
                    <div class="row my-4">
                        <div class="col-md-12">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h2 class="text-center mb-4">Tabla Bitácora / Carga txt sin storage unit</h2>

                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <button class="btn btn-success text-right" id="btnExcelBitacora">
                                            <i class="fas fa-file-excel me-2"></i>Cargar Excel Bitácora
                                        </button>
                                        <input type="file" id="fileInputBitacora" accept=".xlsx, .xls" style="display: none;"/>

                                        <button class="btn btn-secondary text-right" id="tooltipBitacora">
                                            <i class="far fa-question-circle me-2"></i>Ejemplo excel
                                        </button>

                                        <button class="btn btn-primary text-right" id="btnTxtBitacora">
                                            <i class="fas fa-file-alt me-2"></i>Actualizar txt
                                        </button>
                                        <input type="file" id="fileInputTxt" accept=".txt" style="display: none;" multiple/>
                                    </div>

                                    <!-- Tabla Bitácora -->
                                    <div class="table-responsive">
                                        <table class="table datatables" id="tablaBitacora">
                                            <thead class="bg-light">
                                            <tr>
                                                <th>Id_Bitacora</th>
                                                <th>NúmeroParte</th>
                                                <th>FolioMarbete</th>
                                                <th>Fecha</th>
                                                <th>Usuario</th>
                                                <th>Estatus</th>
                                                <th>PrimerConteo</th>
                                                <th>SegundoConteo</th>
                                                <th>TercerConteo</th>
                                                <th>Comentario</th>
                                                <th>StorageBin</th>
                                                <th>StorageType</th>
                                                <th>Área</th>
                                            </tr>
                                            </thead>
                                            <tbody id="bodyBitacora"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Storage -->
                <div class="col-12">
                    <div class="row my-4">
                        <div class="col-md-12">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h2 class="text-center mb-4">Tabla Storage / Carga txt</h2>

                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <button class="btn btn-success text-right" id="btnExcelStorage">
                                            <i class="fas fa-file-excel me-2"></i>Cargar Excel Storage
                                        </button>
                                        <input type="file" id="fileInputStorage" accept=".xlsx, .xls" style="display: none;"/>

                                        <button class="btn btn-secondary text-right" id="tooltipStorage">
                                            <i class="far fa-question-circle me-2"></i>Ejemplo excel
                                        </button>

                                        <button class="btn btn-primary text-right" id="btnTxtStorage">
                                            <i class="fas fa-file-alt me-2"></i>Actualizar txt
                                        </button>
                                        <input type="file" id="fileInputTxtS" accept=".txt" style="display: none;" multiple/>
                                    </div>

                                    <!-- Tabla Storage -->
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered mt-3" id="tablaStorage">
                                            <thead class="bg-light">
                                            <tr>
                                                <th>id_StorageUnit</th>
                                                <th>Numero_Parte</th>
                                                <th>Cantidad</th>
                                                <th>Storage_Bin</th>
                                                <th>Storage_Type</th>
                                            </tr>
                                            </thead>
                                            <tbody id="bodyStorage"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Reporte Inventario -->
                <div class="col-12">
                    <div class="row my-4">
                        <div class="col-md-12">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h2 class="text-center mb-4">Carga y descarga de reporte Inventario</h2>

                                    <div class="d-flex justify-content-center mb-3">
                                        <button class="btn btn-success text-right" id="btnExcelExcelQty">
                                            <i class="fas fa-file-excel me-2"></i>Cargar Excel Inventario
                                        </button>
                                        <input type="file" id="fileInputExcelQty" accept=".xlsx, .xls" style="display: none;"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Scripts estándar -->
<?php include 'estaticos/scriptEstandar.php'; ?>

<!-- Scripts para procesamiento de Excel y archivos -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.2/FileSaver.min.js"></script>

<!-- jQuery y Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>

<!-- SweetAlert2 y Charts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<!-- Tooltips -->
<script src="https://unpkg.com/tippy.js@6"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>

<!-- Scripts personalizados -->
<script src="js/apps.js"></script>
<script src="js/excel.js"></script>
<script src="js/archivoTexto.js"></script>

<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-56159088-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());
    gtag('config', 'UA-56159088-1');
</script>

<!-- Inicialización de tooltips -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inicialización de tooltips para ejemplos
        document.getElementById("tooltipBitacora").addEventListener("click", function () {
            mostrarImagenTooltip(
                "tooltipBitacora",
                "https://grammermx.com/excelInventario/imgs/bitacora.png",
                320,
                140
            );
        });

        document.getElementById("tooltipStorage").addEventListener("click", function () {
            mostrarImagenTooltip(
                "tooltipStorage",
                "https://grammermx.com/excelInventario/imgs/storage.png",
                320,
                100
            );
        });

        // Inicialización de DataTables
        if ($.fn.DataTable.isDataTable('#tablaBitacora')) {
            $('#tablaBitacora').DataTable().destroy();
        }
        $('#tablaBitacora').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            responsive: true,
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        });

        if ($.fn.DataTable.isDataTable('#tablaStorage')) {
            $('#tablaStorage').DataTable().destroy();
        }
        $('#tablaStorage').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            responsive: true,
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        });
    });
</script>

</body>
</html>