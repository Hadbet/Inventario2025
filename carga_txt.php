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
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">
    <title>GRAMMER INVENTARIO</title>
    <?php include 'estaticos/stylesEstandar.php'; ?>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .drop-zone {
            border: 2px dashed #ccc;
            border-radius: 5px;
            padding: 25px;
            text-align: center;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }
        .drop-zone:hover, .drop-zone.active {
            background-color: #e3f2fd;
            border-color: #007bff;
        }
        .file-list {
            max-height: 200px;
            overflow-y: auto;
            margin-top: 15px;
            padding: 10px;
            background-color: #f1f1f1;
            border-radius: 5px;
        }
        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 10px;
            margin-bottom: 5px;
            background-color: white;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .file-item button {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            font-size: 18px;
            line-height: 1;
        }
        .section-header {
            padding: 15px;
            background-color: #343a40;
            color: white;
            border-radius: 5px 5px 0 0;
        }
        .section-body {
            padding: 20px;
            background-color: white;
            border-radius: 0 0 5px 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
    </style>
</head>
<body class="vertical light">
<div class="wrapper">
    <?php
    require_once('estaticos/navegador.php');
    ?>
    <main role="main" class="main-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">
                    <h2 class="mb-4">Carga de Archivos de Inventario</h2>

                    <!-- Sección para cargar TXT PVB -->
                    <div class="card mb-4">
                        <div class="card-header section-header">
                            <h5 class="mb-0">Cargar TXT PVB</h5>
                        </div>
                        <div class="card-body section-body">
                            <div class="drop-zone" id="dropZonePvb">
                                <p class="mb-2">Arrastra aquí los archivos TXT o</p>
                                <button type="button" class="btn btn-primary" id="btnTxtPvb">Seleccionar Archivos</button>
                                <input type="file" id="fileInputPvb" multiple accept=".txt" style="display: none;">
                            </div>

                            <div id="fileListPvb" class="file-list" style="display: none;">
                                <h6 class="mb-2">Archivos seleccionados:</h6>
                                <div id="fileItemsPvb"></div>
                            </div>

                            <div class="export-options mt-4">
                                <h6>Opciones de exportación:</h6>
                                <div class="btn-group mt-2">
                                    <button type="button" class="btn btn-outline-primary" id="exportTxtPvb">
                                        <i class="fe fe-file-text"></i> TXT
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="exportPdfPvb">
                                        <i class="fe fe-file"></i> PDF
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="exportCsvPvb">
                                        <i class="fe fe-file-text"></i> CSV
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="printPvb">
                                        <i class="fe fe-printer"></i> Imprimir
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección para cargar TXT SUN -->
                    <div class="card">
                        <div class="card-header section-header">
                            <h5 class="mb-0">Cargar TXT SUN</h5>
                        </div>
                        <div class="card-body section-body">
                            <div class="drop-zone" id="dropZoneSun">
                                <p class="mb-2">Arrastra aquí los archivos TXT o</p>
                                <button type="button" class="btn btn-primary" id="btnTxtSun">Seleccionar Archivos</button>
                                <input type="file" id="fileInputSun" multiple accept=".txt" style="display: none;">
                            </div>

                            <div id="fileListSun" class="file-list" style="display: none;">
                                <h6 class="mb-2">Archivos seleccionados:</h6>
                                <div id="fileItemsSun"></div>
                            </div>

                            <div class="export-options mt-4">
                                <h6>Opciones de exportación:</h6>
                                <div class="btn-group mt-2">
                                    <button type="button" class="btn btn-outline-primary" id="exportTxtSun">
                                        <i class="fe fe-file-text"></i> TXT
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="exportPdfSun">
                                        <i class="fe fe-file"></i> PDF
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="exportCsvSun">
                                        <i class="fe fe-file-text"></i> CSV
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="printSun">
                                        <i class="fe fe-printer"></i> Imprimir
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- .row -->
        </div> <!-- .container-fluid -->
    </main> <!-- main -->
</div> <!-- .wrapper -->

<?php include 'estaticos/scriptEstandar.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
<script src="js/apps.js"></script>
<script src="https://unpkg.com/tippy.js@6"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.2/FileSaver.min.js"></script>


<!-- -Archivos de jQuery-->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script src="js/cargaTxt.js"></script>

<!-- BOOSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>


<!-- DataTable -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</body>
</html>