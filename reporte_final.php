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
    // ✅ CÓDIGO JAVASCRIPT COMPLETO CON CREACIÓN AUTOMÁTICA DE REGISTROS P

    let allData = [];

    async function fetchData(url) {
        return $.getJSON(url).fail((jqxhr, textStatus, error) => {
            console.error(`Error fetching ${url}: ${textStatus}, ${error}`);
            return Promise.reject(error);
        });
    }

    // ✅ VERSIÓN CON DEBUG PARA VER QUÉ DATOS LLEGAN

    function processData(response, type) {
        if (!response || !Array.isArray(response.data)) {
            console.warn(`Respuesta inesperada para el tipo '${type}':`, response);
            return [];
        }

        // ✅ DEBUG: Ver el primer registro de cada tipo
        if (response.data.length > 0) {
            console.log(`📋 Datos tipo ${type}:`, response.data[0]);
        }

        return response.data.map(item => {
            // ✅ DEBUG: Verificar Costo_Unitario
            if (type === 'tres' && !item.Costo_Unitario) {
                console.error(`❌ Item tipo TRES sin Costo_Unitario:`, item);
            }

            return {
                P: type === 'uno' ? '*' : '',
                L: type === 'dos' ? '*' : '',
                M: type === 'tres' ? '*' : '',
                GrammerNo: item.GrammerNo,
                Descripcion: item.Descripcion,
                UM: item.UM,
                Costo_Unitario: item.Costo_Unitario, // ✅ No hacer parseFloat aquí
                StLocation: '',
                StBin: type === 'uno' ? '' : item.STBin || item.StorageBin,
                Folio: type === 'tres' ? item.FolioMarbete : '',
                Sap: type === 'tres' ? '' : item.Total_InventarioSap,
                Conteo: type === 'tres' ? item.Total_Conteo : item.Total_Bitacora_Inventario,
                Dif: type === 'tres' ? '' : item.Diferencia,
                Costo: parseFloat(item.Costo_Unitario || 0) * parseFloat(item.Diferencia || 0),
                Comentario: type === 'tres' ? item.Comentario : ''
            };
        });
    }

    function crearRegistrosP() {
        const grupos = {};

        allData.forEach(item => {
            const key = `${item.GrammerNo}_${item.StBin}`;
            if (!grupos[key]) {
                grupos[key] = [];
            }
            grupos[key].push(item);
        });

        const nuevosRegistrosP = [];

        Object.entries(grupos).forEach(([key, items]) => {
            const tieneP = items.some(item => item.P === '*');
            const tieneL = items.some(item => item.L === '*');
            const tieneM = items.some(item => item.M === '*');

            if (tieneM && !tieneP && !tieneL) {
                const itemM = items.find(item => item.M === '*');

                if (itemM) {
                    // ✅ DEBUG: Ver qué tiene el itemM
                    console.log(`🔍 Item M encontrado:`, {
                        GrammerNo: itemM.GrammerNo,
                        StBin: itemM.StBin,
                        Costo_Unitario_RAW: itemM.Costo_Unitario,
                        Costo_Unitario_TYPE: typeof itemM.Costo_Unitario,
                        Conteo: itemM.Conteo
                    });

                    const conteo = parseFloat(itemM.Conteo || 0);
                    const costoUnitario = parseFloat(itemM.Costo_Unitario || 0);
                    const sap = 0;
                    const diferencia = conteo - sap;
                    const costo = diferencia * costoUnitario;

                    console.log(`💰 Cálculos:`, {
                        conteo,
                        costoUnitario,
                        diferencia,
                        costo
                    });

                    const nuevoP = {
                        P: '*',
                        L: '',
                        M: '',
                        GrammerNo: itemM.GrammerNo,
                        Descripcion: itemM.Descripcion,
                        UM: itemM.UM,
                        Costo_Unitario: costoUnitario,
                        StLocation: '',
                        StBin: itemM.StBin,
                        Folio: '',
                        Sap: sap,
                        Conteo: conteo,
                        Dif: diferencia,
                        Costo: costo,
                        Comentario: ''
                    };

                    nuevosRegistrosP.push(nuevoP);
                }
            }
        });

        allData = [...nuevosRegistrosP, ...allData];

        console.log(`📊 Total registros P creados: ${nuevosRegistrosP.length}`);
    }

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

            allData = [
                ...processData(results[0], 'uno'),
                ...processData(results[1], 'dos'),
                ...processData(results[2], 'tres')
            ];

            // ✅ PASO CRÍTICO: Crear registros P para items que solo tienen M
            crearRegistrosP();

            allData.sort((a, b) => {
                const grammerNoCompare = a.GrammerNo.localeCompare(b.GrammerNo);
                if (grammerNoCompare !== 0) {
                    return grammerNoCompare;
                }
                return a.StBin.localeCompare(b.StBin);
            });

            const tableBody = $('#data-table tbody');
            const rowsHtml = allData.map(item => `
            <tr>
                <td>${item.P || ''}</td>
                <td>${item.L || ''}</td>
                <td>${item.M || ''}</td>
                <td>${item.GrammerNo || ''}</td>
                <td>${item.Descripcion || ''}</td>
                <td>${item.UM || ''}</td>
                <td>${(parseFloat(item.Costo_Unitario || 0)).toFixed(4)}</td>
                <td>${item.StLocation || ''}</td>
                <td>${item.StBin || ''}</td>
                <td>${item.Folio || ''}</td>
                <td>${(parseFloat(item.Sap || 0)).toFixed(2)}</td>
                <td>${(parseFloat(item.Conteo || 0)).toFixed(2)}</td>
                <td>${(parseFloat(item.Dif || 0)).toFixed(2)}</td>
                <td>${(parseFloat(item.Costo || 0)).toFixed(4)}</td>
                <td>${item.Comentario || ''}</td>
            </tr>
        `).join('');

            tableBody.html(rowsHtml);

            console.timeEnd("Tiempo total de carga");

        } catch (error) {
            console.error("Error al cargar los datos:", error);
            $('#data-table tbody').html('<tr><td colspan="15">Error al cargar los datos. Por favor, intente de nuevo.</td></tr>');
        }
    }

    /**
     * ✅ FUNCIÓN CLAVE: Crear registros P para items que solo tienen M (no existen en SAP)
     */
    function crearRegistrosP() {
        // Agrupar por GrammerNo + StBin
        const grupos = {};

        allData.forEach(item => {
            const key = `${item.GrammerNo}_${item.StBin}`;
            if (!grupos[key]) {
                grupos[key] = [];
            }
            grupos[key].push(item);
        });

        // Array para almacenar los nuevos registros P
        const nuevosRegistrosP = [];

        // Procesar cada grupo
        Object.entries(grupos).forEach(([key, items]) => {
            const tieneP = items.some(item => item.P === '*');
            const tieneL = items.some(item => item.L === '*');
            const tieneM = items.some(item => item.M === '*');

            // ✅ Si solo tiene M (sin P ni L), crear registro P
            if (tieneM && !tieneP && !tieneL) {
                // Tomar datos del primer M
                const itemM = items.find(item => item.M === '*');

                if (itemM) {
                    const conteo = parseFloat(itemM.Conteo || 0);
                    const costoUnitario = parseFloat(itemM.Costo_Unitario || 0);
                    const sap = 0; // ✅ No existe en SAP = 0
                    const diferencia = conteo - sap; // Positivo porque conteo > 0
                    const costo = Math.abs(diferencia * costoUnitario);

                    // Crear registro P nuevo
                    const nuevoP = {
                        P: '*',
                        L: '',
                        M: '',
                        GrammerNo: itemM.GrammerNo,
                        Descripcion: itemM.Descripcion,
                        UM: itemM.UM,
                        Costo_Unitario: costoUnitario,
                        StLocation: '',
                        StBin: itemM.StBin,
                        Folio: '',
                        Sap: sap, // ✅ 0 porque no existe en SAP
                        Conteo: conteo,
                        Dif: diferencia, // ✅ Diferencia positiva
                        Costo: costo, // ✅ Costo calculado
                        Comentario: ''
                    };

                    nuevosRegistrosP.push(nuevoP);

                    console.log(`✅ Creado registro P para ${itemM.GrammerNo} (${itemM.StBin}): SAP=0, Conteo=${conteo}, Dif=${diferencia}, Costo=${costo}`);
                }
            }
        });

        // ✅ Agregar los nuevos registros P al inicio del array
        allData = [...nuevosRegistrosP, ...allData];

        console.log(`📊 Total registros P creados: ${nuevosRegistrosP.length}`);
    }

    // Iniciar la carga de datos
    loadData();

    // Exportar a Excel
    $('#export-button').click(function() {
        if (typeof allData === 'undefined' || allData.length === 0) {
            alert("No hay datos para exportar.");
            return;
        }

        console.time("Tiempo de exportación");

        const worksheet = XLSX.utils.json_to_sheet(allData);
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

        const headers = Object.keys(allData[0]);
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