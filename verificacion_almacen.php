<?php
session_start();
$rol =$_SESSION['rol'];
$area =$_SESSION['area'];
$areaNombre =$_SESSION['AreaNombre'];
$bin =$_SESSION['StBin'];
$nomina =$_SESSION['nomina'];
$nombre =$_SESSION['nombre'];

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
}?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>GRAMMER INVENTARIO</title>

    <?php include 'estaticos/stylesEstandar.php'; ?>

    <!-- JavaScript -->
    <script src="lib/sweetalert2.all.min.js"></script>

    <link rel="stylesheet" href="css/generales.css">

    <style>
        .card-marbete {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .card-marbete:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .badge-faltantes {
            font-size: 1.2em;
            padding: 0.5em 1em;
        }
    </style>

</head>
<body class="vertical  light  ">
<div class="wrapper">

    <?php
    require_once('estaticos/navegador.php');
    ?>

    <main role="main" class="main-content">
        <center><img src="images/tituloInventario.png" style="width: 50%"></center>

        <!-- ✅ SECCIÓN: Cards de marbetes con diferencias -->
        <div class="container-fluid" id="divMarbetesDiferencias">
            <div class="row justify-content-center">
                <div class="col-12">
                    <h2 class="page-title text-center mb-4">Marbetes con Diferencias - Requieren Verificación</h2>
                </div>
            </div>
            <div class="row" id="cardsMarbetes">
                <!-- Las cards se cargarán dinámicamente aquí -->
            </div>
        </div>

        <!-- ✅ SECCIÓN: Interfaz de verificación (similar a form_registro_conteos_coordinador) -->
        <div class="container-fluid" id="pasoDos" style="display: none">
            <div class="row justify-content-center">
                <div class="col-12">
                    <button class="btn btn-secondary mb-3" onclick="volverACards()">
                        <i class="fe fe-arrow-left mr-2"></i>Volver a Marbetes
                    </button>

                    <h2 class="page-title">Verificación de Marbete: <span id="txtMarbeteActual"></span></h2>
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <strong id="Ubicacion" class="card-title h4"></strong>
                        </div>
                        <div class="card-body">

                            <strong class="card-title h4">Storage Unit Escaneados</strong><br><br>
                            <label for="" class="card-title h5">Total contado : <strong id="lblTotalContado" class="card-title h4">0</strong></label>

                            <table id="data-table" class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Storage Unit</th>
                                    <th>Numero Parte</th>
                                    <th>Cantidad</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                            <strong class="card-title h4">Storage Unit Faltantes</strong><br><br>
                            <label for="" class="card-title h5">Total faltante : <strong id="lblTotalFaltante" class="card-title h4">0</strong></label>

                            <table id="data-table-faltantes" class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Storage Unit</th>
                                    <th>Numero Parte</th>
                                    <th>Cantidad</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                        </div>
                        <div class="card-footer">
                            <button type="submit"
                                    class="btn mb-2 btn-success float-right text-white" onclick="finalizarVerificacion()">
                                Finalizar Verificación<span
                                        class="fe fe-chevron-right fe-16 ml-2"></span></button>
                        </div>
                    </div> <!-- / .card -->
                </div> <!-- .col-12 -->
            </div> <!-- .row -->
        </div> <!-- .container-fluid -->

    </main> <!-- main -->
</div> <!-- .wrapper -->

<?php include 'estaticos/scriptEstandar.php'; ?>

<script src="js/apps.js"></script>
<script src="assets/scanapp.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-56159088-1"></script>

<script>

    var marbeteActual;
    var storageBin = '';
    var numeroParte = '';
    var storageType = '';
    var totalContado = 0;
    var totalFaltante = 0;
    var storageUnitsContados = {};

    // ✅ CARGAR MARBETES CON DIFERENCIAS AL INICIO
    cargarMarbetesDiferencias();

    function cargarMarbetesDiferencias() {
        $.getJSON('https://grammermx.com/Logistica/Inventario2025/dao/consultaMarbetesDiferencias.php?area=<?php echo $area;?>', function (data) {
            if (data.success && data.data.length > 0) {
                var cardsContainer = document.getElementById("cardsMarbetes");
                cardsContainer.innerHTML = '';

                for (var i = 0; i < data.data.length; i++) {
                    var marbeteData = data.data[i];
                    var porcentajeCompleto = ((marbeteData.StorageEscaneados / marbeteData.TotalStorageUnits) * 100).toFixed(0);

                    var cardHTML = `
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card shadow card-marbete" onclick="cargarMarbeteVerificacion('${marbeteData.FolioMarbete}')">
                                <div class="card-header bg-warning">
                                    <strong class="card-title text-white">Marbete: ${marbeteData.FolioMarbete}</strong>
                                    <span class="badge badge-danger float-right badge-faltantes">${marbeteData.StorageFaltantes} Faltantes</span>
                                </div>
                                <div class="card-body">
                                    <p><strong>Número Parte:</strong> ${marbeteData.NumeroParte}</p>
                                    <p><strong>Storage Bin:</strong> ${marbeteData.StorageBin}</p>
                                    <p><strong>Primer Conteo:</strong> ${marbeteData.PrimerConteo}</p>
                                    <p><strong>Responsable:</strong> ${marbeteData.Usuario}</p>
                                    <div class="progress mt-3">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: ${porcentajeCompleto}%"
                                             aria-valuenow="${porcentajeCompleto}" aria-valuemin="0" aria-valuemax="100">
                                            ${porcentajeCompleto}% Completo
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-center">
                                    <button class="btn btn-primary btn-block" onclick="event.stopPropagation(); cargarMarbeteVerificacion('${marbeteData.FolioMarbete}')">
                                        <i class="fe fe-check-circle mr-2"></i>Verificar Ahora
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    cardsContainer.innerHTML += cardHTML;
                }
            } else {
                document.getElementById("divMarbetesDiferencias").innerHTML = `
                    <div class="alert alert-success text-center" role="alert">
                        <h4 class="alert-heading">¡Excelente!</h4>
                        <p>No hay marbetes con diferencias pendientes de verificación.</p>
                    </div>
                `;
            }
        });
    }

    function cargarMarbeteVerificacion(folioMarbete) {
        marbeteActual = folioMarbete;

        // Ocultar cards y mostrar interfaz de verificación
        document.getElementById("divMarbetesDiferencias").style.display = 'none';
        document.getElementById("pasoDos").style.display = 'block';
        document.getElementById("txtMarbeteActual").innerText = folioMarbete;

        // Limpiar tablas
        document.getElementById("data-table").getElementsByTagName('tbody')[0].innerHTML = '';
        document.getElementById("data-table-faltantes").getElementsByTagName('tbody')[0].innerHTML = '';

        totalContado = 0;
        totalFaltante = 0;
        storageUnitsContados = {};

        // Cargar datos del marbete
        $.getJSON('https://grammermx.com/Logistica/Inventario2025/dao/consultaMarbeteFaltantesSunAlmacen.php?marbete=' + folioMarbete, function (data) {
            if (data.success && data.data.length > 0) {
                // Obtener info del primer registro
                storageBin = data.data[0].StorageBin || '';
                numeroParte = data.data[0].NumeroParte || '';

                document.getElementById("Ubicacion").innerHTML = "Ubicación : " + storageBin + " - NP: " + numeroParte;

                // Separar contados y faltantes
                var tableContados = document.getElementById("data-table").getElementsByTagName('tbody')[0];
                var tableFaltantes = document.getElementById("data-table-faltantes").getElementsByTagName('tbody')[0];

                data.data.forEach(function(item) {
                    var cantidad = parseFloat(item.CantidadStorage);

                    // Si EstatusStorage = 1 Y tiene el FolioMarbete correcto → Ya contado
                    if (item.EstatusStorage == 1 && item.FolioMarbete == folioMarbete) {
                        var rowContado = tableContados.insertRow(-1);
                        rowContado.insertCell(0).innerHTML = item.StorageUnit;
                        rowContado.insertCell(1).innerHTML = item.NumeroParte;
                        rowContado.insertCell(2).innerHTML = cantidad;
                        rowContado.cells[2].contentEditable = "true";

                        // ✅ Este es el total correcto que ya está en la BD
                        totalContado += cantidad;

                        // Agregar a la lista de contados
                        storageUnitsContados[item.StorageUnit] = {
                            numeroParte: item.NumeroParte,
                            cantidad: cantidad
                        };
                    }
                    // Si NO está contado → Faltante
                    else if (item.EstatusStorage == 0 || !item.FolioMarbete || item.FolioMarbete != folioMarbete) {
                        var rowFaltante = tableFaltantes.insertRow(-1);
                        rowFaltante.insertCell(0).innerHTML = item.StorageUnit;
                        rowFaltante.insertCell(1).innerHTML = item.NumeroParte;
                        rowFaltante.insertCell(2).innerHTML = cantidad;
                        rowFaltante.insertCell(3).innerHTML =
                            '<button onclick="capturarFaltante(\'' + item.StorageUnit + '\', \'' +
                            item.NumeroParte + '\', ' + cantidad + ', event)" class="btn btn-sm btn-success">' +
                            '<i class="fe fe-check mr-1"></i>Capturar</button>';

                        totalFaltante += cantidad;
                    }
                });

                // ✅ El totalContado es el que se guardará en PrimerConteo
                document.getElementById("lblTotalContado").innerText = totalContado.toFixed(2);
                document.getElementById("lblTotalFaltante").innerText = totalFaltante.toFixed(2);
            }
        });
    }

    function capturarFaltante(storageUnit, numParte, cantidad, event) {
        // ✅ Solo actualizar en BD y mover visualmente, NO sumar al total

        // Mover a la tabla de contados (solo visual)
        var tableContados = document.getElementById("data-table").getElementsByTagName('tbody')[0];
        var row = tableContados.insertRow(-1);
        row.insertCell(0).innerHTML = storageUnit;
        row.insertCell(1).innerHTML = numParte;
        row.insertCell(2).innerHTML = cantidad;
        row.cells[2].contentEditable = "true";

        // ✅ Actualizar solo el contador de faltantes (restar)
        totalFaltante -= parseFloat(cantidad);
        document.getElementById("lblTotalFaltante").innerText = totalFaltante.toFixed(2);

        // Eliminar de la tabla de faltantes
        event.target.closest('tr').remove();

        // Actualizar en base de datos inmediatamente
        var formData = new FormData();
        formData.append('sun', storageUnit);
        formData.append('marbete', marbeteActual);
        formData.append('estatus', '1');

        fetch('https://grammermx.com/Logistica/Inventario2025/dao/actualizarAgregarSun.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: "Storage Unit Capturado",
                        text: "Unit: " + storageUnit,
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    console.error("Error al actualizar:", data.message);
                }
            });
    }

    function finalizarVerificacion() {
        // ✅ Usar el totalContado original (el que ya estaba en la BD)
        var cantidadFinal = totalContado;

        // Verificar si quedan faltantes
        var tableFaltantes = document.getElementById("data-table-faltantes");
        var filasFaltantes = tableFaltantes.querySelectorAll('tbody tr');
        var hayFaltantes = filasFaltantes.length > 0;

        if (hayFaltantes) {
            Swal.fire({
                title: "¿Finalizar con faltantes?",
                text: "Aún hay " + filasFaltantes.length + " storage units sin capturar. ¿Deseas continuar?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, finalizar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    enviarVerificacionFinal(cantidadFinal);
                }
            });
        } else {
            enviarVerificacionFinal(cantidadFinal);
        }
    }

    function enviarVerificacionFinal(cantidad) {
        var formData = new FormData();
        formData.append('nombre', '<?php echo $nomina;?>-<?php echo $nombre;?>');
        formData.append('folioMarbete', marbeteActual + '.2');
        formData.append('cantidad', cantidad); // ✅ Este es el total original de la BD

        fetch('https://grammermx.com/Logistica/Inventario2025/dao/actualizarMarbeteProduccion.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let timerInterval;
                    Swal.fire({
                        title: "¡Verificación Completada!",
                        html: "Marbete verificado exitosamente. Regresando... <b></b>",
                        timer: 2000,
                        timerProgressBar: true,
                        icon: "success",
                        didOpen: () => {
                            Swal.showLoading();
                            const timer = Swal.getPopup().querySelector("b");
                            timerInterval = setInterval(() => {
                                timer.textContent = Swal.getTimerLeft();
                            }, 100);
                        },
                        willClose: () => {
                            clearInterval(timerInterval);
                        }
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.timer) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        title: "Error",
                        text: "Hubo un problema al finalizar la verificación",
                        icon: "error"
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: "Error de conexión",
                    text: "No se pudo conectar con el servidor",
                    icon: "error"
                });
            });
    }

    function volverACards() {
        document.getElementById("divMarbetesDiferencias").style.display = 'block';
        document.getElementById("pasoDos").style.display = 'none';
        cargarMarbetesDiferencias(); // Recargar para actualizar contadores
    }

</script>
</body>
</html>