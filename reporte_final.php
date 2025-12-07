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
    <style>
    </style>
</head>
<body class="vertical  light  ">
<div class="wrapper">
    <?php
    require_once('estaticos/navegador.php');
    ?>
    <main role="main" class="main-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12">

                    <h2 class="mb-2 page-title">Reporte Final Diferencias</h2>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow mb-4">
                                <div class="card-header">
                                    <strong class="card-title">Filtros detallados</strong>
                                </div>
                                <div class="card-body">
                                    <form class="form-inline">

                                        <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Diferencia en pesos mayor a:</label>
                                        <div class="input-group mb-2 mr-sm-2">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">$</div>
                                            </div>
                                            <input type="text" class="form-control" id="inlineFormInputGroupUsername2" >
                                        </div>

                                        <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Diferencias en cantidad mayor a:</label>
                                        <input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2">

                                        <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Número Parte</label>
                                        <input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2">

                                        <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Folio</label>
                                        <input type="text" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2">

                                        <button type="submit" class="btn btn-primary mb-2">Buscar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card shadow bg-warning text-white border-0">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-3 text-center">
                          <span class="circle circle-sm bg-secundary-light">
                            <i class="fe fe-16 fe-trending-down text-white mb-0"></i>
                          </span>
                                        </div>
                                        <div class="col pr-0">
                                            <!--<p class="small mb-0 text-white">Partes con negativos</p>-->
                                            <p class="small mb-0 text-white">Cantidad SAP</p>
                                            <span class="h3 mb-0 text-white" id="partesNegativo"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card shadow bg-success text-white border-0">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-3 text-center">
                          <span class="circle circle-sm bg-success-light">
                            <i class="fe fe-16 fe-trending-up text-white mb-0"></i>
                          </span>
                                        </div>
                                        <div class="col pr-0">
                                            <!--<p class="small mb-0 text-white">Partes con positivos</p>-->
                                            <p class="small mb-0 text-white">Contador</p>
                                            <span class="h3 mb-0 text-white" id="partesPositivo"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card shadow bg-warning text-white border-0">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-3 text-center">
                          <span class="circle circle-sm bg-warning-light">
                            <i class="fe fe-16 fe-alert-circle text-white mb-0"></i>
                          </span>


                                        </div>
                                        <div class="col pr-0">
                                            <!--<p class="small mb-0 text-white" >Costo Negativo</p>-->
                                            <p class="small mb-0 text-white" >Valor SAP</p>
                                            <span class="h3 mb-0 text-white" id="costoNegativo"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3 mb-4">
                            <div class="card shadow bg-success text-white border-0">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-3 text-center">
                          <span class="circle circle-sm bg-success-light">
                            <i class="fe fe-16 fe-dollar-sign text-white mb-0"></i>
                          </span>
                                        </div>
                                        <div class="col pr-0">
                                            <!--<p class="small mb-0 text-white">Costo Positivas</p>-->
                                            <p class="small mb-0 text-white" >Valor Contador</p>
                                            <span class="h3 mb-0 text-white" id="costoPositivo"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row my-4">
                        <!-- Small table -->
                        <div class="col-md-12">
                            <div class="card shadow">
                                <div class="card-body">
                                    <button id="export-button" class="btn btn-success text-white">Exportar a Excel</button>
                                    <button id="copy-button" class="btn btn-info">Copiar al portapapeles</button>
                                    <br><br>
                                    <!-- table -->
                                    <table class="table datatables table-fixed" id="data-table">
                                        <thead>
                                        <tr>
                                            <th>P</th>
                                            <th>L</th>
                                            <th>M</th>
                                            <th>GrammerNo</th>
                                            <th>Descripción</th>
                                            <th>UM</th>
                                            <th>Costo/Und</th>
                                            <th>StLocation</th>
                                            <th>StBin</th>
                                            <th>Folio</th>
                                            <th>Sap</th>
                                            <th>Conteo</th>
                                            <th>Dif</th>
                                            <th>Costo</th>
                                            <th>Comentario</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- Los datos se insertarán aquí desde el script JavaScript -->
                                        </tbody>
                                    </table>

                                    <!-- Button trigger modal -->
                                    <button style="display: none" type="button" class="btn mb-2 btn-outline-success"
                                            data-toggle="modal" data-target="#verticalModal" id="btnModal"> Launch demo
                                        modal
                                    </button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="verticalModal" tabindex="-1" role="dialog"
                                         aria-labelledby="verticalModalTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="verticalModalTitle">Modificación de
                                                        usuarios</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="col-form-label">Id:</label>
                                                        <input type="text" class="form-control" id="txtIdM" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="recipient-name"
                                                               class="col-form-label">Usuario:</label>
                                                        <input type="text" class="form-control" id="txtUsuarioM">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="recipient-name"
                                                               class="col-form-label">Password:</label>
                                                        <input type="password" class="form-control" id="txtPasswordM">
                                                    </div>
                                                    <hr>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn mb-2 btn-success text-white"
                                                            onclick="actualizarDatos()">Actualizar
                                                    </button>
                                                    <button type="button" class="btn mb-2 btn-secondary"
                                                            data-dismiss="modal" id="btnCloseM">Close
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div> <!-- simple table -->
                    </div> <!-- end section -->
                </div> <!-- .col-12 -->
            </div> <!-- .row -->
        </div> <!-- .container-fluid -->

    </main> <!-- main -->
</div> <!-- .wrapper -->

<?php include 'estaticos/scriptEstandar.php'; ?>
<script src="https://unpkg.com/tableexport.jquery.plugin/tableExport.min.js"></script>
<script src="https://unpkg.com/tableexport.jquery.plugin/libs/FileSaver/FileSaver.min.js"></script>
<script src="https://unpkg.com/tableexport.jquery.plugin/libs/js-xlsx/xlsx.core.min.js"></script>
<script>

    estatusConteo();
    function estatusConteo() {
        $.getJSON('https://grammermx.com/Logistica/Inventario2025/dao/consultaReporteFinalDetalles.php', function (data) {
            for (var i = 0; i < data.data.length; i++) {

                document.getElementById("costoNegativo").innerText = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(data.data[i].Costo_Total_Negativo);
                document.getElementById("costoPositivo").innerText = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(data.data[i].Costo_Total_Positivo);
                document.getElementById("partesNegativo").innerText=data.data[i].Cantidad_Total_Negativa;
                document.getElementById("partesPositivo").innerText=data.data[i].Cantidad_Total_Positiva;

            }
        });
    }

    $('#data-table').find('td').each(function(){
        var text = $(this).text();
        if (!isNaN(text)) {
            $(this).text(text.toString());
        }
    });
/*
    $('#copy-button').click(function() {
        var range = document.createRange();
        range.selectNode(document.getElementById('data-table'));
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
        document.execCommand('copy');
        window.getSelection().removeAllRanges();
    });

    $('#export-button').click(function() {
        $('#data-table').tableExport({
            type:'xlsx',
            fileName: 'reporte_final_inventario',
            displayTableName: true,
            exportHiddenCells: false,
            headers: true,
            footers: true,
            formats: ['xlsx'],
            filename: 'reporte_final_inventario',
            sheetname: 'reporte_final_inventario',
            bootstrap: false,
            exportButtons: true,
            position: 'bottom',
            ignoreRows: null,
            ignoreCols: null,
            trimWhitespace: false,
            RTL: false,
            sheetnames: false,
            onMsoNumberFormat: function(cell, row, col) {
                if (col === 3) return '\\@';
            }
        });
    });
*/
    function inicioTabla() {
        $.getJSON('https://grammermx.com/Logistica/Inventario2025/dao/consultaReporteFinal.php', function (data) {
            var table = document.getElementById("data-table");
            var totalSap = 0;
            var totalConteo = 0;
            for (var i = 0; i < data.data.length; i++) {
                totalSap += parseFloat(data.data[i].Total_InventarioSap);
                totalConteo += parseFloat(data.data[i].Total_Bitacora_Inventario);
                var row = table.insertRow(-1); // Crea una nueva fila al final de la tabla
                var cell1 = row.insertCell(0); // Crea una nueva celda en la fila
                var cell2 = row.insertCell(1); // Crea otra nueva celda en la fila
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                var cell5 = row.insertCell(4);
                var cell6 = row.insertCell(5);
                var cell7 = row.insertCell(6);
                var cell8 = row.insertCell(7);
                var cell9 = row.insertCell(8);
                var cell10 = row.insertCell(9);
                var cell11 = row.insertCell(10);
                var cell12 = row.insertCell(11);
                var cell13 = row.insertCell(12);
                var cell14 = row.insertCell(13);
                var cell15 = row.insertCell(14);
                cell1.innerHTML = i === 0 ? "*" : ""; // P
                cell2.innerHTML = i === 0 ? "" : "*"; // L
                cell3.innerHTML = i === 0 ? "" : ""; // M
                cell4.innerHTML = data.data[i].GrammerNo; // GrammerNo
                cell5.innerHTML = data.data[i].Descripcion; // Descripcion
                cell6.innerHTML = data.data[i].UM; // UM
                cell7.innerHTML = data.data[i].CostoUnitario; // Costo/Und
                cell8.innerHTML = ""; // StLocation
                cell9.innerHTML = data.data[i].STBin; // StBin
                cell10.innerHTML = data.data[i].FolioMarbete; // Folio
                cell11.innerHTML = data.data[i].Total_InventarioSap; // Sap
                cell12.innerHTML = data.data[i].Total_Bitacora_Inventario; // Conteo
                cell13.innerHTML = ""; // Dif
                cell14.innerHTML = ""; // Costo
                cell15.innerHTML = data.data[i].Comentario; // Comentario
            }
        });
    }
</script>
<script src="js/apps.js"></script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-56159088-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());
    gtag('config', 'UA-56159088-1');

    let allData = [];

    // 1. Simplificamos fetchData. $.getJSON ya devuelve un objeto "thenable" (similar a una Promesa)
    // que funciona perfectamente con async/await. No es necesario el constructor new Promise().
    async function fetchData(url) {
        return $.getJSON(url).fail((jqxhr, textStatus, error) => {
            // Añadimos un mejor log de errores para la depuración
            console.error(`Error fetching ${url}: ${textStatus}, ${error}`);
            // Rechazamos la promesa para que Promise.all falle si una petición falla
            return Promise.reject(error);
        });
    }

    // 2. Renombramos la función y eliminamos "async", ya que no hace nada asíncrono.
    // Usamos .map() en lugar de un bucle 'for', es más conciso y moderno para transformar arrays.
    function processData(response, type) {
        // Si la respuesta no tiene la estructura esperada, devolvemos un array vacío
        if (!response || !Array.isArray(response.data)) {
            console.warn(`Respuesta inesperada para el tipo '${type}':`, response);
            return [];
        }

        return response.data.map(item => ({
            P: type === 'uno' ? '*' : '',
            L: type === 'dos' ? '*' : '',
            M: type === 'tres' ? '*' : '',
            GrammerNo: item.GrammerNo,
            Descripcion: item.Descripcion,
            UM: item.UM,
            Costo_Unitario: item.Costo_Unitario,
            StLocation: '',
            StBin: type === 'uno' ? '' : item.STBin || item.StorageBin,
            Folio: type === 'tres' ? item.FolioMarbete : '',
            Sap: type === 'tres' ? '' : item.Total_InventarioSap,
            Conteo: type === 'tres' ? item.Total_Conteo : item.Total_Bitacora_Inventario,
            Dif: type === 'tres' ? '' : item.Diferencia,
            Costo: parseFloat(item.Costo_Unitario || 0) * parseFloat(item.Diferencia || 0),
            Comentario: type === 'tres' ? item.Comentario : ''
        }));
    }

    // ✅ LÓGICA MEJORADA: Completar M cuando no tenga P o L

    async function loadData() {
        try {
            console.time("Tiempo total de carga");

            const urls = {
                uno: 'https://grammermx.com/Logistica/Inventario2025/dao/consultaReporteFinalUno.php',
                dos: 'https://grammermx.com/Logistica/Inventario2025/dao/consultaReporteFinalDos.php',
                tres: 'https://grammermx.com/Logistica/Inventario2025/dao/consultaReporteFinalTres.php'
            };

            const results = await Promise.all([
                fetchData(urls.uno),
                fetchData(urls.dos),
                fetchData(urls.tres)
            ]);

            allData = [];

            // ========================================
            // 1️⃣ TIPO UNO: Agrupado por GrammerNo
            // ========================================
            if (results[0]?.data) {
                results[0].data.forEach(item => {
                    const sap = parseFloat(item.Total_InventarioSap || 0);
                    const conteo = parseFloat(item.Total_Bitacora_Inventario || 0);
                    const diferencia = conteo - sap;
                    const costoUnitario = parseFloat(item.Costo_Unitario || 0);

                    allData.push({
                        P: sap > 0 && conteo === 0 ? '*' : '',
                        L: sap === 0 && conteo > 0 ? '*' : '',
                        M: sap > 0 && conteo > 0 && diferencia !== 0 ? '*' : '',
                        GrammerNo: item.GrammerNo || '',
                        Descripcion: item.Descripcion || '',
                        UM: item.UM || '',
                        Costo_Unitario: costoUnitario,
                        StLocation: '',
                        StBin: '',
                        Folio: '',
                        Sap: sap,
                        Conteo: conteo,
                        Dif: diferencia,
                        Costo: Math.abs(diferencia * costoUnitario),
                        Comentario: '',
                        Tipo: 'uno'
                    });
                });
            }

            // ========================================
            // 2️⃣ TIPO DOS: Agrupado por GrammerNo + Storage Bin
            // ========================================
            if (results[1]?.data) {
                results[1].data.forEach(item => {
                    const sap = parseFloat(item.Total_InventarioSap || 0);
                    const conteo = parseFloat(item.Total_Bitacora_Inventario || 0);
                    const diferencia = conteo - sap;
                    const costoUnitario = parseFloat(item.Costo_Unitario || 0);

                    allData.push({
                        P: sap > 0 && conteo === 0 ? '*' : '',
                        L: sap === 0 && conteo > 0 ? '*' : '',
                        M: sap > 0 && conteo > 0 && diferencia !== 0 ? '*' : '',
                        GrammerNo: item.GrammerNo || '',
                        Descripcion: item.Descripcion || '',
                        UM: item.UM || '',
                        Costo_Unitario: costoUnitario,
                        StLocation: '',
                        StBin: item.STBin || item.StorageBin || '',
                        Folio: '',
                        Sap: sap,
                        Conteo: conteo,
                        Dif: diferencia,
                        Costo: Math.abs(diferencia * costoUnitario),
                        Comentario: '',
                        Tipo: 'dos'
                    });
                });
            }

            // ========================================
            // 3️⃣ TIPO TRES: Detalle de marbetes
            // ========================================
            if (results[2]?.data) {
                results[2].data.forEach(item => {
                    const conteo = parseFloat(item.Total_Conteo || 0);
                    const costoUnitario = parseFloat(item.Costo_Unitario || 0);

                    allData.push({
                        P: '',
                        L: '',
                        M: '*',
                        GrammerNo: item.GrammerNo || '',
                        Descripcion: item.Descripcion || '',
                        UM: item.UM || '',
                        Costo_Unitario: costoUnitario,
                        StLocation: '',
                        StBin: item.StorageBin || '',
                        Folio: item.FolioMarbete || '',
                        Sap: '',
                        Conteo: conteo,
                        Dif: '',
                        Costo: '',
                        Comentario: item.Comentario || '',
                        Tipo: 'tres'
                    });
                });
            }

            // ========================================
            // ✅ PASO CRÍTICO: Completar datos en M huérfanos
            // ========================================
            completarMHuerfanos();

            // Ordenar
            allData.sort((a, b) => {
                const grammerCompare = a.GrammerNo.localeCompare(b.GrammerNo);
                if (grammerCompare !== 0) return grammerCompare;
                return (a.StBin || '').localeCompare(b.StBin || '');
            });

            renderTable();
            console.timeEnd("Tiempo total de carga");

        } catch (error) {
            console.error("Error al cargar los datos:", error);
            $('#data-table tbody').html('<tr><td colspan="15">Error al cargar los datos.</td></tr>');
        }
    }

    /**
     * ✅ FUNCIÓN CLAVE: Completar SAP, Dif y Costo en registros M que no tienen P o L
     */
    function completarMHuerfanos() {
        // Agrupar por GrammerNo + StBin para identificar familias
        const grupos = {};

        allData.forEach((item, index) => {
            const key = `${item.GrammerNo}_${item.StBin}`;
            if (!grupos[key]) {
                grupos[key] = [];
            }
            grupos[key].push({ item, index });
        });

        // Procesar cada grupo
        Object.values(grupos).forEach(grupo => {
            // Verificar si hay P o L en el grupo
            const tieneP = grupo.some(g => g.item.P === '*');
            const tieneL = grupo.some(g => g.item.L === '*');
            const tieneM = grupo.some(g => g.item.M === '*');

            // ✅ Si solo tiene M (sin P ni L), completar datos
            if (tieneM && !tieneP && !tieneL) {
                grupo.forEach(g => {
                    if (g.item.M === '*' && g.item.Tipo === 'tres') {
                        // Este M está huérfano, necesita datos completos

                        // Buscar datos de SAP en tipo uno o dos
                        const datosBase = allData.find(d =>
                            d.GrammerNo === g.item.GrammerNo &&
                            d.StBin === g.item.StBin &&
                            (d.Tipo === 'uno' || d.Tipo === 'dos')
                        );

                        if (datosBase) {
                            // ✅ COMPLETAR DATOS FALTANTES
                            const conteo = parseFloat(g.item.Conteo || 0);
                            const sap = parseFloat(datosBase.Sap || 0);
                            const diferencia = conteo - sap;
                            const costoUnitario = parseFloat(g.item.Costo_Unitario || 0);

                            allData[g.index].Sap = sap;
                            allData[g.index].Dif = diferencia;
                            allData[g.index].Costo = Math.abs(diferencia * costoUnitario);
                        } else {
                            // No hay SAP, entonces SAP = 0
                            const conteo = parseFloat(g.item.Conteo || 0);
                            const costoUnitario = parseFloat(g.item.Costo_Unitario || 0);

                            allData[g.index].Sap = 0;
                            allData[g.index].Dif = conteo;
                            allData[g.index].Costo = Math.abs(conteo * costoUnitario);
                        }
                    }
                });
            }
            // Si tiene P, L y M, dejar como está (comportamiento actual)
        });
    }

    function renderTable() {
        const tableBody = $('#data-table tbody');
        const rowsHtml = allData.map(item => `
        <tr>
            <td>${item.P || ''}</td>
            <td>${item.L || ''}</td>
            <td>${item.M || ''}</td>
            <td>${item.GrammerNo || ''}</td>
            <td>${item.Descripcion || ''}</td>
            <td>${item.UM || ''}</td>
            <td>${typeof item.Costo_Unitario === 'number' ? item.Costo_Unitario.toFixed(4) : ''}</td>
            <td>${item.StLocation || ''}</td>
            <td>${item.StBin || ''}</td>
            <td>${item.Folio || ''}</td>
            <td>${typeof item.Sap === 'number' ? item.Sap.toFixed(2) : (item.Sap === '' ? '' : '0.00')}</td>
            <td>${typeof item.Conteo === 'number' ? item.Conteo.toFixed(2) : ''}</td>
            <td>${typeof item.Dif === 'number' ? item.Dif.toFixed(2) : (item.Dif === '' ? '' : '0.00')}</td>
            <td>${typeof item.Costo === 'number' ? item.Costo.toFixed(4) : (item.Costo === '' ? '' : '0.00')}</td>
            <td>${item.Comentario || ''}</td>
        </tr>
    `).join('');

        tableBody.html(rowsHtml);
    }

    async function fetchData(url) {
        return $.getJSON(url).fail((jqxhr, textStatus, error) => {
            console.error(`Error fetching ${url}: ${textStatus}, ${error}`);
            return Promise.reject(error);
        });
    }

    // Iniciar carga
    loadData();

    // Exportar a Excel
    $('#export-button').click(function() {
        if (typeof allData === 'undefined' || allData.length === 0) {
            alert("No hay datos para exportar.");
            return;
        }

        console.time("Tiempo de exportación");

        // Limpiar campo 'Tipo' antes de exportar
        const dataParaExportar = allData.map(item => {
            const { Tipo, ...rest } = item;
            return rest;
        });

        const worksheet = XLSX.utils.json_to_sheet(dataParaExportar);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Reporte Final");

        XLSX.writeFile(workbook, "reporte_final_inventario.xlsx", {compression: true});

        console.timeEnd("Tiempo de exportación");
    });

    // Copiar al portapapeles
    $('#copy-button').click(function() {
        if (typeof allData === 'undefined' || allData.length === 0) {
            alert("No hay datos para copiar.");
            return;
        }

        const headers = ['P', 'L', 'M', 'GrammerNo', 'Descripcion', 'UM', 'Costo_Unitario',
            'StLocation', 'StBin', 'Folio', 'Sap', 'Conteo', 'Dif', 'Costo', 'Comentario'];

        const csvContent = [
            headers.join(','),
            ...allData.map(row => headers.map(header => {
                let value = row[header];
                if (typeof value === 'string' && value.includes(',')) {
                    return `"${value}"`;
                }
                return value;
            }).join(','))
        ].join('\n');

        navigator.clipboard.writeText(csvContent).then(() => {
            const originalText = $(this).text();
            $(this).text("¡Copiado!");
            setTimeout(() => $(this).text(originalText), 2000);
        }).catch(err => {
            console.error('Error al copiar:', err);
            alert('No se pudo copiar al portapapeles.');
        });
    });
</script>
</body>
</html>