document.addEventListener('DOMContentLoaded', function() {
    // Configuración del drag & drop para PVB
    const dropZonePvb = document.getElementById('dropZonePvb');
    const fileInputPvb = document.getElementById('fileInputPvb');
    const fileListPvb = document.getElementById('fileListPvb');
    const fileItemsPvb = document.getElementById('fileItemsPvb');

    // Mapa para almacenar los archivos seleccionados para PVB
    const filesPvb = new Map();

    document.getElementById('btnTxtPvb').addEventListener('click', () => {
        fileInputPvb.click();
    });

    // Manejo del arrastre y suelta para PVB
    dropZonePvb.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZonePvb.classList.add('active');
    });

    dropZonePvb.addEventListener('dragleave', () => {
        dropZonePvb.classList.remove('active');
    });

    dropZonePvb.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZonePvb.classList.remove('active');
        handleFilesPvb(e.dataTransfer.files);
    });

    fileInputPvb.addEventListener('change', (e) => {
        handleFilesPvb(e.target.files);
    });

    // Función para manejar los archivos seleccionados para PVB
    function handleFilesPvb(selectedFiles) {
        if (selectedFiles.length > 0) {
            fileListPvb.style.display = 'block';

            for (const file of selectedFiles) {
                if (file.name.toLowerCase().endsWith('.txt')) {
                    const fileId = Date.now() + '-' + Math.random().toString(36).substr(2, 9);
                    filesPvb.set(fileId, file);

                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-item';
                    fileItem.innerHTML = `
                        <span>${file.name}</span>
                        <button type="button" data-file-id="${fileId}">&times;</button>
                    `;
                    fileItemsPvb.appendChild(fileItem);

                    fileItem.querySelector('button').addEventListener('click', function() {
                        const fileId = this.getAttribute('data-file-id');
                        filesPvb.delete(fileId);
                        fileItem.remove();

                        // Ocultar la lista si no hay archivos
                        if (fileItemsPvb.children.length === 0) {
                            fileListPvb.style.display = 'none';
                        }
                    });
                }
            }
        }
    }

    // Configuración de botones de exportación PVB
    document.getElementById('exportTxtPvb').addEventListener('click', () => procesarYExportarPvb('txt'));
    document.getElementById('exportPdfPvb').addEventListener('click', () => procesarYExportarPvb('pdf'));
    document.getElementById('exportCsvPvb').addEventListener('click', () => procesarYExportarPvb('csv'));
    document.getElementById('printPvb').addEventListener('click', () => procesarYExportarPvb('print'));

    // Configuración del drag & drop para SUN
    const dropZoneSun = document.getElementById('dropZoneSun');
    const fileInputSun = document.getElementById('fileInputSun');
    const fileListSun = document.getElementById('fileListSun');
    const fileItemsSun = document.getElementById('fileItemsSun');

    // Mapa para almacenar los archivos seleccionados para SUN
    const filesSun = new Map();

    document.getElementById('btnTxtSun').addEventListener('click', () => {
        fileInputSun.click();
    });

    // Manejo del arrastre y suelta para SUN
    dropZoneSun.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZoneSun.classList.add('active');
    });

    dropZoneSun.addEventListener('dragleave', () => {
        dropZoneSun.classList.remove('active');
    });

    dropZoneSun.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZoneSun.classList.remove('active');
        handleFilesSun(e.dataTransfer.files);
    });

    fileInputSun.addEventListener('change', (e) => {
        handleFilesSun(e.target.files);
    });

    // Función para manejar los archivos seleccionados para SUN
    function handleFilesSun(selectedFiles) {
        if (selectedFiles.length > 0) {
            fileListSun.style.display = 'block';

            for (const file of selectedFiles) {
                if (file.name.toLowerCase().endsWith('.txt')) {
                    const fileId = Date.now() + '-' + Math.random().toString(36).substr(2, 9);
                    filesSun.set(fileId, file);

                    const fileItem = document.createElement('div');
                    fileItem.className = 'file-item';
                    fileItem.innerHTML = `
                        <span>${file.name}</span>
                        <button type="button" data-file-id="${fileId}">&times;</button>
                    `;
                    fileItemsSun.appendChild(fileItem);

                    fileItem.querySelector('button').addEventListener('click', function() {
                        const fileId = this.getAttribute('data-file-id');
                        filesSun.delete(fileId);
                        fileItem.remove();

                        // Ocultar la lista si no hay archivos
                        if (fileItemsSun.children.length === 0) {
                            fileListSun.style.display = 'none';
                        }
                    });
                }
            }
        }
    }

    // Configuración de botones de exportación SUN
    document.getElementById('exportTxtSun').addEventListener('click', () => procesarYExportarSun('txt'));
    document.getElementById('exportPdfSun').addEventListener('click', () => procesarYExportarSun('pdf'));
    document.getElementById('exportCsvSun').addEventListener('click', () => procesarYExportarSun('csv'));
    document.getElementById('printSun').addEventListener('click', () => procesarYExportarSun('print'));

    // Variables para almacenar los resultados procesados
    let resultadosProcesados = [];
    let materialesFaltantes = [];

    // Función para procesar y exportar archivos PVB
    async function procesarYExportarPvb(formato) {
        const files = Array.from(filesPvb.values());

        if (files.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No hay archivos',
                text: 'Por favor, selecciona al menos un archivo TXT para procesar.'
            });
            return;
        }

        // Mostrar indicador de carga
        Swal.fire({
            title: 'Procesando archivos',
            text: 'Espere mientras se procesan los archivos...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            resultadosProcesados = [];
            materialesFaltantes = [];

            for (const file of files) {
                // Procesar el archivo
                const datosArchivo = await parsearArchivoPvb(file);

                if (datosArchivo.length > 0) {
                    // Enviar datos al backend
                    const datosBackend = await enviarDatosAlBackend(datosArchivo);

                    // Actualizar el contenido del archivo
                    const contenidoActualizado = await actualizarContenidoArchivo(file, datosBackend);

                    resultadosProcesados.push({
                        nombreArchivo: file.name,
                        contenido: contenidoActualizado.contenido,
                    });

                    // Actualizar materiales faltantes
                    const faltantes = await obtenerMaterialesFaltantes(datosArchivo);
                    if (faltantes.length > 0) {
                        materialesFaltantes = materialesFaltantes.concat(faltantes);
                    }
                }
            }

            // Cerrar el indicador de carga
            Swal.close();

            // Exportar según el formato seleccionado
            switch (formato) {
                case 'txt':
                    exportarComoTxt(resultadosProcesados);
                    break;
                case 'pdf':
                    exportarComoPdf(resultadosProcesados);
                    break;
                case 'csv':
                    exportarComoCsv(resultadosProcesados);
                    break;
                case 'print':
                    imprimirArchivos(resultadosProcesados);
                    break;
            }

            // Mostrar mensaje si hay materiales faltantes
            if (materialesFaltantes.length > 0) {
                setTimeout(() => {
                    Swal.fire({
                        icon: 'info',
                        title: 'Materiales faltantes',
                        text: `Se encontraron ${materialesFaltantes.length} materiales que no están en el reporte.`,
                        confirmButtonText: 'Exportar Excel',
                        showCancelButton: true,
                        cancelButtonText: 'Cerrar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            exportarMaterialesFaltantes(materialesFaltantes);
                        }
                    });
                }, 1000);
            }
        } catch (error) {
            console.error('Error al procesar los archivos:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al procesar los archivos.'
            });
        }
    }

    // Función para parsear archivo PVB
    async function parsearArchivoPvb(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();

            reader.onload = (event) => {
                try {
                    const contenido = event.target.result;
                    const lineas = contenido.split(/\r?\n/);

                    // Extraer información del encabezado
                    let storageType = '';
                    let inventoryNo = '';
                    let page = '';

                    for (const linea of lineas) {
                        if (linea.includes('Storage type :')) {
                            const match = linea.match(/Storage type\s*:\s*(\d+)/);
                            if (match && match[1]) {
                                storageType = match[1].trim();
                            }
                        }
                        if (linea.includes('Inventory lst.no. :')) {
                            const match = linea.match(/Inventory lst\.no\.\s*:\s*(\d+)/);
                            if (match && match[1]) {
                                inventoryNo = match[1].trim();
                            }
                        }
                        if (linea.includes('Page')) {
                            const match = linea.match(/Page\.+:\s*([^\s]+)/);
                            if (match && match[1]) {
                                page = match[1].trim();
                            }
                        }
                    }

                    // Extraer datos de los materiales
                    const datos = [];

                    for (const linea of lineas) {
                        // Buscar líneas que comienzan con un número (0001) seguido de espacio y texto
                        if (/^0001\s+\w+/.test(linea)) {
                            const partes = linea.split(/\s+/);

                            if (partes.length >= 6) {
                                const storBin = partes[1];
                                const materialNo = partes[5];

                                datos.push({
                                    storBin: storBin,
                                    materialNo: materialNo,
                                    storageType: storageType,
                                    inventoryNo: inventoryNo,
                                    page: page
                                });
                            }
                        }
                    }

                    resolve(datos);
                } catch (error) {
                    console.error('Error al parsear el archivo:', error);
                    reject(error);
                }
            };

            reader.onerror = (error) => {
                console.error('Error al leer el archivo:', error);
                reject(error);
            };

            reader.readAsText(file);
        });
    }

    // Función para enviar datos al backend
    async function enviarDatosAlBackend(datos) {
        try {
            const response = await fetch('https://grammermx.com/Logistica/Inventario2025/daoAdmin/daoProcesarPvb.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datos)
            });

            if (!response.ok) {
                throw new Error(`Error del servidor: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Error al enviar datos al backend:', error);
            throw error;
        }
    }

    // Función para actualizar el contenido del archivo
    async function actualizarContenidoArchivo(file, datosBackend) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();

            reader.onload = (event) => {
                try {
                    const contenidoOriginal = event.target.result;
                    const lineasOriginales = contenidoOriginal.split(/\r?\n/);

                    // Actualizar las líneas con los datos recibidos del backend
                    const lineasActualizadas = lineasOriginales.map((linea) => {
                        // Buscar líneas que comienzan con un número (0001) seguido de espacio y texto
                        if (/^0001\s+\w+/.test(linea)) {
                            const partes = linea.split(/\s+/);

                            if (partes.length >= 6) {
                                const storBin = partes[1];
                                const materialNo = partes[5];

                                // Buscar el material en los datos del backend
                                const materialEncontrado = datosBackend.find(
                                    (item) => item.storBin === storBin && item.materialNo === materialNo
                                );

                                if (materialEncontrado) {
                                    // Determinar el valor final del conteo
                                    let conteoFinal = '0';
                                    if (materialEncontrado.conteoFinal && materialEncontrado.conteoFinal !== '0') {
                                        conteoFinal = materialEncontrado.conteoFinal;
                                    }

                                    // Reemplazar los guiones bajos por la cantidad
                                    return linea.replace(/______________ PC/, `${conteoFinal} PC`);
                                } else {
                                    // Si el material no se encuentra, poner 0
                                    return linea.replace(/______________ PC/, '0 PC');
                                }
                            }
                        }

                        return linea;
                    });

                    resolve({
                        contenido: lineasActualizadas.join('\n')
                    });
                } catch (error) {
                    console.error('Error al actualizar el contenido del archivo:', error);
                    reject(error);
                }
            };

            reader.onerror = (error) => {
                console.error('Error al leer el archivo:', error);
                reject(error);
            };

            reader.readAsText(file);
        });
    }

    // Función para obtener materiales faltantes
    async function obtenerMaterialesFaltantes(datos) {
        try {
            const response = await fetch('https://grammermx.com/Logistica/Inventario2025/daoAdmin/daoReporteFaltantes.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datos)
            });

            if (!response.ok) {
                throw new Error(`Error del servidor: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Error al obtener materiales faltantes:', error);
            return [];
        }
    }

    // Función para exportar como TXT
    function exportarComoTxt(resultados) {
        for (const resultado of resultados) {
            const blob = new Blob([resultado.contenido], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);

            const link = document.createElement('a');
            link.href = url;
            link.download = `actualizado_${resultado.nombreArchivo}`;
            link.click();

            URL.revokeObjectURL(url);
        }
    }

    // Función para exportar como PDF
    function exportarComoPdf(resultados) {
        // Verificar que jsPDF esté disponible
        if (typeof jspdf === 'undefined' && typeof jsPDF === 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar la biblioteca jsPDF necesaria para generar PDFs.'
            });
            return;
        }

        const jsPDFlib = jspdf?.jsPDF || jsPDF;

        for (const resultado of resultados) {
            // Crear un documento PDF
            const pdf = new jsPDFlib({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4'
            });

            // Configurar la fuente
            pdf.setFont('courier', 'normal');
            pdf.setFontSize(8);

            // Dividir el contenido en líneas
            const lineas = resultado.contenido.split('\n');

            // Configurar márgenes
            const margenX = 10;
            let margenY = 10;

            // Agregar contenido al PDF
            for (const linea of lineas) {
                if (margenY > 280) {
                    // Agregar nueva página si se llega al final
                    pdf.addPage();
                    margenY = 10;
                }

                pdf.text(linea, margenX, margenY);
                margenY += 3; // Espacio entre líneas
            }

            // Guardar el PDF
            pdf.save(`actualizado_${resultado.nombreArchivo.replace('.txt', '.pdf')}`);
        }
    }

    // Función para exportar como CSV
    function exportarComoCsv(resultados) {
        for (const resultado of resultados) {
            // Dividir el contenido en líneas
            const lineas = resultado.contenido.split('\n');

            // Extraer información del encabezado
            let almacen = '';
            let tipo = '';
            let fecha = '';

            for (const linea of lineas) {
                if (linea.includes('Wareh.number :')) {
                    const match = linea.match(/Wareh\.number\s*:\s*([^\s]+)/);
                    if (match && match[1]) {
                        almacen = match[1].trim();
                    }
                }
                if (linea.includes('Storage type :')) {
                    const match = linea.match(/Storage type\s*:\s*([^\s]+)/);
                    if (match && match[1]) {
                        tipo = match[1].trim();
                    }
                }
                if (linea.includes('Date')) {
                    const match = linea.match(/Date\.+:\s*([^\s]+)/);
                    if (match && match[1]) {
                        fecha = match[1].trim();
                    }
                }
            }

            // Crear contenido CSV
            let csvContent = 'StorageBin,QuantNo,Plant,SLoc,MaterialNo,Description,Quantity,UoM\n';

            for (const linea of lineas) {
                // Buscar líneas con datos de materiales
                if (/^0001\s+\w+/.test(linea)) {
                    const partes = linea.split(/\s+/);

                    if (partes.length >= 6) {
                        const storBin = partes[1];
                        const quantNo = partes[2];
                        const plnt = partes[3];
                        const sLoc = partes[4];
                        const materialNo = partes[5];

                        // Extraer descripción y cantidad
                        let descripcion = '';
                        let cantidad = '0';
                        let unidad = 'PC';

                        // Extraer la descripción
                        let i = 6;
                        while (i < partes.length && !partes[i].match(/^\d+$|^__/)) {
                            descripcion += partes[i] + ' ';
                            i++;
                        }

                        // Buscar la cantidad
                        const cantidadMatch = linea.match(/(\d+)\s+PC/);
                        if (cantidadMatch && cantidadMatch[1]) {
                            cantidad = cantidadMatch[1];
                        }

                        // Agregar fila al CSV
                        csvContent += `${storBin},${quantNo},${plnt},${sLoc},${materialNo},"${descripcion.trim()}",${cantidad},${unidad}\n`;
                    }
                }
            }

            // Descargar el archivo CSV
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);

            const link = document.createElement('a');
            link.href = url;
            link.download = `actualizado_${resultado.nombreArchivo.replace('.txt', '.csv')}`;
            link.click();

            URL.revokeObjectURL(url);
        }
    }

    // Función para imprimir los archivos
    function imprimirArchivos(resultados) {
        // Crear un iframe oculto para imprimir
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        document.body.appendChild(iframe);

        // Crear el contenido del iframe
        let contenidoHtml = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Impresión de Inventario</title>
                <style>
                    body {
                        font-family: monospace;
                        font-size: 9pt;
                        white-space: pre;
                    }
                    .page-break {
                        page-break-after: always;
                    }
                </style>
            </head>
            <body>
        `;

        // Agregar cada archivo al contenido
        for (let i = 0; i < resultados.length; i++) {
            const resultado = resultados[i];

            contenidoHtml += `<div>${resultado.contenido}</div>`;

            // Agregar salto de página si no es el último archivo
            if (i < resultados.length - 1) {
                contenidoHtml += '<div class="page-break"></div>';
            }
        }

        contenidoHtml += '</body></html>';

        // Escribir el contenido en el iframe
        iframe.contentWindow.document.open();
        iframe.contentWindow.document.write(contenidoHtml);
        iframe.contentWindow.document.close();

        // Esperar a que se cargue el contenido y luego imprimir
        setTimeout(() => {
            iframe.contentWindow.print();

            // Eliminar el iframe después de imprimir
            setTimeout(() => {
                document.body.removeChild(iframe);
            }, 1000);
        }, 500);
    }

    // Función para exportar los materiales faltantes como Excel
    function exportarMaterialesFaltantes(materiales) {
        if (!materiales || materiales.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No hay materiales faltantes',
                text: 'No hay materiales faltantes para exportar.'
            });
            return;
        }

        // Crear un libro de Excel
        const wb = XLSX.utils.book_new();

        // Configurar propiedades del libro
        wb.Props = {
            Title: 'Materiales Faltantes',
            Author: 'Sistema de Inventario',
            CreatedDate: new Date()
        };

        // Crear la hoja de trabajo
        wb.SheetNames.push('Materiales Faltantes');

        // Datos para la hoja de trabajo - Nuevo orden de columnas
        const data = [
            ['Libro', 'Hoja', 'Storage Type', 'Storage Bin', 'Material No', 'Cantidad', 'Estado']
        ];

        // Crear un mapa para guardar la información de libro y hoja por storage bin
        const infoLibroHoja = new Map();

        // Buscar información de libro y hoja en los archivos procesados
        for (const resultado of resultadosProcesados) {
            const lineas = resultado.contenido.split('\n');
            let inventoryNo = '';
            let page = '';
            let currentStorageBin = '';
            let currentStorageType = '';

            // Recorrer cada línea para extraer la información
            for (let i = 0; i < lineas.length; i++) {
                const linea = lineas[i];

                // Buscar el número de libro (Inventory lst.no)
                if (linea.includes('Inventory lst.no. :')) {
                    const match = linea.match(/Inventory lst\.no\.\s*:\s*(\d+)/);
                    if (match && match[1]) {
                        inventoryNo = match[1].trim();
                    }
                }

                // Buscar el número de página (Page)
                if (linea.includes('Page')) {
                    const match = linea.match(/Page\.+:\s*([^\s]+)/);
                    if (match && match[1]) {
                        page = match[1].trim();
                    }
                }

                // Buscar el tipo de almacenamiento (Storage type)
                if (linea.includes('Storage type :')) {
                    const match = linea.match(/Storage type\s*:\s*(\d+)/);
                    if (match && match[1]) {
                        currentStorageType = match[1].trim();
                    }
                }

                // Buscar las líneas de material para obtener el storage bin
                if (/^0001\s+\w+/.test(linea)) {
                    const partes = linea.split(/\s+/);

                    if (partes.length >= 2) {
                        currentStorageBin = partes[1];

                        // Guardar la información para este storage bin
                        infoLibroHoja.set(currentStorageBin, {
                            libro: inventoryNo,
                            hoja: page,
                            storageType: currentStorageType
                        });
                    }
                }
            }
        }

        // Agregar cada material faltante a los datos con la información de libro y hoja
        for (const item of materiales) {
            const storBin = item.storBin || '';
            const info = infoLibroHoja.get(storBin) || { libro: '', hoja: '', storageType: '' };

            data.push([
                info.libro || '',
                info.hoja || '',
                item.storageType || info.storageType || '',
                storBin,
                item.materialNo || '',
                item.conteoFinal || '0',
                item.estado || 'No incluido en el reporte'
            ]);
        }

        // Crear la hoja de trabajo
        const ws = XLSX.utils.aoa_to_sheet(data);
        wb.Sheets['Materiales Faltantes'] = ws;

        // Generar el archivo Excel
        const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'binary' });

        // Función auxiliar para convertir la cadena binaria en un objeto Blob
        function s2ab(s) {
            const buf = new ArrayBuffer(s.length);
            const view = new Uint8Array(buf);
            for (let i = 0; i < s.length; i++) {
                view[i] = s.charCodeAt(i) & 0xFF;
            }
            return buf;
        }

        // Descargar el archivo Excel
        const blob = new Blob([s2ab(wbout)], { type: 'application/octet-stream' });
        const url = URL.createObjectURL(blob);

        const link = document.createElement('a');
        link.href = url;
        link.download = 'materiales_faltantes.xlsx';
        link.click();

        URL.revokeObjectURL(url);
    }

    // FUNCIONES PARA SUN

    // Función para procesar y exportar archivos SUN
    async function procesarYExportarSun(formato) {
        const files = Array.from(filesSun.values());

        if (files.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No hay archivos',
                text: 'Por favor, selecciona al menos un archivo TXT para procesar.'
            });
            return;
        }

        // Mostrar indicador de carga
        Swal.fire({
            title: 'Procesando archivos',
            text: 'Espere mientras se procesan los archivos...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            resultadosProcesados = [];
            materialesFaltantes = [];

            for (const file of files) {
                // Procesar el archivo
                const datosArchivo = await parsearArchivoSun(file);

                if (datosArchivo.length > 0) {
                    // Enviar datos al backend
                    const datosBackend = await enviarDatosAlBackendSun(datosArchivo);

                    // Actualizar el contenido del archivo
                    const contenidoActualizado = await actualizarContenidoArchivoSun(file, datosBackend);

                    resultadosProcesados.push({
                        nombreArchivo: file.name,
                        contenido: contenidoActualizado.contenido,
                    });

                    // Actualizar materiales faltantes o encontrados en otra ubicación
                    const materialesEspeciales = await obtenerMaterialesEspecialesSun(datosArchivo);
                    if (materialesEspeciales.length > 0) {
                        materialesFaltantes = materialesFaltantes.concat(materialesEspeciales);
                    }
                }
            }

            // Cerrar el indicador de carga
            Swal.close();

            // Exportar según el formato seleccionado
            switch (formato) {
                case 'txt':
                    exportarComoTxt(resultadosProcesados);
                    break;
                case 'pdf':
                    exportarComoPdf(resultadosProcesados);
                    break;
                case 'csv':
                    exportarComoCsv(resultadosProcesados);
                    break;
                case 'print':
                    imprimirArchivos(resultadosProcesados);
                    break;
            }

            // Mostrar mensaje si hay materiales especiales (faltantes o encontrados en otra ubicación)
            if (materialesFaltantes.length > 0) {
                setTimeout(() => {
                    Swal.fire({
                        icon: 'info',
                        title: 'Materiales especiales',
                        text: `Se encontraron ${materialesFaltantes.length} materiales que requieren atención.`,
                        confirmButtonText: 'Exportar Excel',
                        showCancelButton: true,
                        cancelButtonText: 'Cerrar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            exportarMaterialesEspecialesSun(materialesFaltantes);
                        }
                    });
                }, 1000);
            }
        } catch (error) {
            console.error('Error al procesar los archivos:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al procesar los archivos.'
            });
        }
    }

    // Función para parsear archivo SUN
    async function parsearArchivoSun(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();

            reader.onload = (event) => {
                try {
                    const contenido = event.target.result;
                    const lineas = contenido.split(/\r?\n/);

                    // Extraer información del encabezado
                    let storageType = '';
                    let inventoryNo = '';
                    let page = '';

                    for (const linea of lineas) {
                        if (linea.includes('Storage type :')) {
                            const match = linea.match(/Storage type\s*:\s*(\d+)/);
                            if (match && match[1]) {
                                storageType = match[1].trim();
                            }
                        }
                        if (linea.includes('Inventory lst.no. :')) {
                            const match = linea.match(/Inventory lst\.no\.\s*:\s*(\d+)/);
                            if (match && match[1]) {
                                inventoryNo = match[1].trim();
                            }
                        }
                        if (linea.includes('Page')) {
                            const match = linea.match(/Page\.+:\s*([^\s]+)/);
                            if (match && match[1]) {
                                page = match[1].trim();
                            }
                        }
                    }

                    // Extraer datos de los materiales
                    const datos = [];

                    for (const linea of lineas) {
                        // Verificar si hay línea con datos de materiales
                        // Caso 1: Línea con Storage Unit
                        if (/^0001\s+\w+/.test(linea) && linea.includes("Storage Unit")) {
                            const partes = linea.split(/\s+/);

                            if (partes.length >= 10) {
                                const storBin = partes[1];
                                const quantNo = partes[2];
                                const plnt = partes[3];
                                const sLoc = partes[4];
                                const materialNo = partes[5];

                                // Buscar el Storage Unit (estará hacia el final de la línea)
                                let storageUnit = '';
                                for (let i = 6; i < partes.length; i++) {
                                    if (partes[i].match(/^\d{10}$/)) { // Un Storage Unit típico tiene 10 dígitos
                                        storageUnit = partes[i];
                                        break;
                                    }
                                }

                                // Determinar la unidad de medida
                                let uom = 'PC';
                                if (linea.includes('M')) {
                                    uom = 'M';
                                }

                                datos.push({
                                    storBin: storBin,
                                    quantNo: quantNo || '',
                                    plnt: plnt || '',
                                    sLoc: sLoc || '',
                                    materialNo: materialNo || '',
                                    storageUnit: storageUnit,
                                    storageType: storageType,
                                    inventoryNo: inventoryNo,
                                    page: page,
                                    uom: uom,
                                    tipoLinea: 'conStorageUnit'
                                });
                            }
                        }
                        // Caso 2: Línea sin Storage Unit (solo Storage Bin)
                        else if (/^00\d{2}\s+\w+/.test(linea)) {
                            const partes = linea.split(/\s+/);

                            if (partes.length >= 2) {
                                const itemNo = partes[0];
                                const storBin = partes[1];

                                datos.push({
                                    itemNo: itemNo,
                                    storBin: storBin,
                                    storageType: storageType,
                                    inventoryNo: inventoryNo,
                                    page: page,
                                    uom: 'PC',
                                    tipoLinea: 'sinStorageUnit'
                                });
                            }
                        }
                    }

                    resolve(datos);
                } catch (error) {
                    console.error('Error al parsear el archivo:', error);
                    reject(error);
                }
            };

            reader.onerror = (error) => {
                console.error('Error al leer el archivo:', error);
                reject(error);
            };

            reader.readAsText(file);
        });
    }

    // Función para enviar datos al backend para SUN
    async function enviarDatosAlBackendSun(datos) {
        try {
            const response = await fetch('https://grammermx.com/Logistica/Inventario2025/daoAdmin/daoProcesarSun.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datos)
            });

            if (!response.ok) {
                throw new Error(`Error del servidor: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Error al enviar datos al backend:', error);
            throw error;
        }
    }

    // Función para actualizar el contenido del archivo SUN
    // Función para actualizar el contenido del archivo SUN
    async function actualizarContenidoArchivoSun(file, datosBackend) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();

            reader.onload = (event) => {
                try {
                    const contenidoOriginal = event.target.result;
                    const lineasOriginales = contenidoOriginal.split(/\r?\n/);

                    // Actualizar las líneas con los datos recibidos del backend
                    const lineasActualizadas = lineasOriginales.map((linea) => {
                        // Caso 1: Línea con Storage Unit
                        if (/^0001\s+\w+/.test(linea) && linea.includes("Storage Unit")) {
                            const partes = linea.split(/\s+/);

                            if (partes.length >= 10) {
                                const storBin = partes[1];
                                const materialNo = partes[5];

                                // Buscar el Storage Unit
                                let storageUnit = '';
                                for (let i = 6; i < partes.length; i++) {
                                    if (partes[i].match(/^\d{10}$/)) { // Storage Unit típico con 10 dígitos
                                        storageUnit = partes[i];
                                        break;
                                    }
                                }

                                // Determinar la unidad de medida
                                let uom = 'PC';
                                if (linea.includes(' M')) {
                                    uom = 'M';
                                } else if (linea.includes(' KG')) {
                                    uom = 'KG';
                                }

                                // Buscar el material en los datos del backend
                                const materialEncontrado = datosBackend.find(
                                    (item) => (item.storageUnit === storageUnit)
                                );

                                if (materialEncontrado) {
                                    // Verificar el estado para determinar si usar conteo o 0
                                    if (materialEncontrado.estado === 'Encontrado' ||
                                        materialEncontrado.estado === 'Encontrado por Storage Bin') {
                                        // Usar el conteo encontrado
                                        let conteoFinal = '0';
                                        if (materialEncontrado.conteoFinal && materialEncontrado.conteoFinal !== '0') {
                                            conteoFinal = materialEncontrado.conteoFinal;
                                        }

                                        // Reemplazar los guiones bajos por la cantidad
                                        return linea.replace(/____________ (PC|M|KG)/, `${conteoFinal} ${uom}`);
                                    } else {
                                        // Si está en otra ubicación o tiene otro problema, poner 0
                                        return linea.replace(/____________ (PC|M|KG)/, `0 ${uom}`);
                                    }
                                } else {
                                    // Si el material no se encuentra, poner 0
                                    return linea.replace(/____________ (PC|M|KG)/, `0 ${uom}`);
                                }
                            }
                        }
                        // Caso 2: Línea sin Storage Unit
                        else if (/^00\d{2}\s+\w+/.test(linea)) {
                            const partes = linea.split(/\s+/);

                            if (partes.length >= 2) {
                                const storBin = partes[1];

                                // Buscar el storage bin en los datos del backend
                                const materialEncontrado = datosBackend.find(
                                    (item) => item.storBin === storBin
                                );

                                if (materialEncontrado && materialEncontrado.estado === 'Encontrado por Storage Bin') {
                                    // Determinar el valor final del conteo
                                    let conteoFinal = '0';
                                    if (materialEncontrado.conteoFinal && materialEncontrado.conteoFinal !== '0') {
                                        conteoFinal = materialEncontrado.conteoFinal;
                                    }

                                    // Reemplazar los guiones bajos por la cantidad
                                    return linea.replace(/____________/, `${conteoFinal}`);
                                } else {
                                    // Si el material no se encuentra o tiene otro estado, poner 0
                                    return linea.replace(/____________/, '0');
                                }
                            }
                        }

                        return linea;
                    });

                    resolve({
                        contenido: lineasActualizadas.join('\n')
                    });
                } catch (error) {
                    console.error('Error al actualizar el contenido del archivo:', error);
                    reject(error);
                }
            };

            reader.onerror = (error) => {
                console.error('Error al leer el archivo:', error);
                reject(error);
            };

            reader.readAsText(file);
        });
    }

    // Función para obtener materiales especiales (faltantes o en otra ubicación)
    async function obtenerMaterialesEspecialesSun(datos) {
        try {
            const response = await fetch('https://grammermx.com/Logistica/Inventario2025/daoAdmin/daoMaterialesEspecialesSun.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datos)
            });

            if (!response.ok) {
                throw new Error(`Error del servidor: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Error al obtener materiales especiales:', error);
            return [];
        }
    }

    // Función para exportar materiales especiales SUN como Excel
    function exportarMaterialesEspecialesSun(materiales) {
        if (!materiales || materiales.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No hay materiales especiales',
                text: 'No hay materiales especiales para exportar.'
            });
            return;
        }

        // Crear un libro de Excel
        const wb = XLSX.utils.book_new();

        // Configurar propiedades del libro
        wb.Props = {
            Title: 'Materiales Especiales SUN',
            Author: 'Sistema de Inventario',
            CreatedDate: new Date()
        };

        // Crear la hoja de trabajo
        wb.SheetNames.push('Materiales Especiales');

        // Datos para la hoja de trabajo - Orden solicitado
        const data = [
            ['Libro', 'Hoja', 'Storage Type', 'Storage Bin', 'Storage Unit', 'Material No', 'Cantidad', 'Estado']
        ];

        // Agregar cada material especial a los datos
        for (const item of materiales) {
            data.push([
                item.inventoryNo || '',
                item.page || '',
                item.storageType || '',
                item.storBin || '',
                item.storageUnit || '',
                item.materialNo || '',
                item.conteoFinal || '0',
                item.estado || ''
            ]);
        }

        // Crear la hoja de trabajo
        const ws = XLSX.utils.aoa_to_sheet(data);
        wb.Sheets['Materiales Especiales'] = ws;

        // Generar el archivo Excel
        const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'binary' });

        // Función auxiliar para convertir la cadena binaria en un objeto Blob
        function s2ab(s) {
            const buf = new ArrayBuffer(s.length);
            const view = new Uint8Array(buf);
            for (let i = 0; i < s.length; i++) {
                view[i] = s.charCodeAt(i) & 0xFF;
            }
            return buf;
        }

        // Descargar el archivo Excel
        const blob = new Blob([s2ab(wbout)], { type: 'application/octet-stream' });
        const url = URL.createObjectURL(blob);

        const link = document.createElement('a');
        link.href = url;
        link.download = 'materiales_especiales_sun.xlsx';
        link.click();

        URL.revokeObjectURL(url);
    }
});