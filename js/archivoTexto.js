/**
 * GRAMMER INVENTARIO - Módulo de Procesamiento de Archivos de Texto
 * Este módulo maneja el procesamiento de archivos de texto de inventario mediante:
 * 1. Extracción de datos de storage bin, material no y storage unit
 * 2. Consulta de datos correspondientes desde la base de datos
 * 3. Actualización del archivo de texto con los valores de conteo
 * 4. Generación de reportes para artículos no encontrados en el archivo
 */

/**********************************************************************************************************************/
/********************************************************TABLA BITACORA***********************************************/
/**********************************************************************************************************************/

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar los eventos para el procesamiento de bitácora
    iniciarProcesoBitacora();
    // Inicializar los eventos para el procesamiento de storage unit
    iniciarProcesoStorage();
});

/**
 * Inicializa los eventos y manejadores para el procesamiento de Bitácora
 */
function iniciarProcesoBitacora() {
    const btnTxtBitacora = document.getElementById('btnTxtBitacora');
    const fileInputTxt = document.getElementById('fileInputTxt');

    if (!btnTxtBitacora || !fileInputTxt) {
        console.error("No se encontraron los elementos requeridos para el procesamiento de bitácora.");
        return;
    }

    btnTxtBitacora.addEventListener('click', () => {
        fileInputTxt.click();
    });

    fileInputTxt.addEventListener('change', async (event) => {
        const files = event.target.files;

        if (files.length === 0) {
            console.error("No se seleccionaron archivos.");
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se seleccionó ningún archivo.'
            });
            return;
        }

        try {
            // Procesar cada archivo secuencialmente
            for (const file of files) {
                console.log(`Procesando archivo: ${file.name}`);
                await procesarArchivoBitacora(file);
            }

            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: 'Procesamiento de archivos completado.'
            });
        } catch (error) {
            console.error("Error al procesar los archivos:", error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: `Error al procesar los archivos: ${error.message}`
            });
        }
    });
}

/**
 * Procesa un solo archivo de bitácora
 * @param {File} file - El objeto archivo a procesar
 */
async function procesarArchivoBitacora(file) {
    try {
        // Extraer datos del archivo
        const datosFichero = await extraerDatosBitacora(file);

        if (!datosFichero || datosFichero.length === 0) {
            throw new Error(`No se pudieron extraer datos del archivo ${file.name}`);
        }

        // Obtener datos desde el backend
        const datosDB = await enviarDatosAlBackend(datosFichero);

        if (!datosDB || datosDB.length === 0) {
            console.warn(`No se recibieron datos de la base de datos para el archivo ${file.name}`);
        }

        // Actualizar archivo con los datos del backend
        await actualizarContenidoArchivo(file, datosDB);

    } catch (error) {
        console.error(`Error al procesar el archivo ${file.name}:`, error);
        throw error;
    }
}

/**
 * Extrae datos de material y storage bin del archivo de bitácora
 * @param {File} file - El archivo a leer
 * @returns {Promise<Array>} - Array de objetos con storBin y materialNo
 */
function extraerDatosBitacora(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();

        reader.onload = (event) => {
            try {
                const contenido = event.target.result;
                const lineas = contenido.split(/\r?\n/);

                // Filtrar y extraer datos relevantes
                const datos = lineas
                    .map(linea => linea.trim())
                    .filter(linea => /^[0-9]+\s+\w+/.test(linea))
                    .map(linea => {
                        const partes = linea.split(/\s+/);
                        // Verificar si tenemos suficientes partes para una línea válida
                        return partes.length >= 6
                            ? {
                                storBin: partes[1].trim(),
                                materialNo: partes[5].trim()
                            }
                            : null;
                    })
                    .filter(Boolean); // Eliminar entradas nulas

                resolve(datos);
            } catch (error) {
                reject(`Error al procesar el contenido del archivo: ${error.message}`);
            }
        };

        reader.onerror = (error) => {
            reject(`Error al leer el archivo: ${error}`);
        };

        reader.readAsText(file);
    });
}

/**
 * Envía datos al backend para obtener información de inventario para items de bitácora
 * @param {Array} data - Array de objetos con storBin y materialNo
 * @returns {Promise<Array>} - Array de objetos con datos de conteo
 */
async function enviarDatosAlBackend(data) {
    try {
        const response = await fetch('daoAdmin/daoActualizar-txt.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(`Error en la respuesta del servidor: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('Error enviando datos al backend:', error);
        throw error;
    }
}

/**
 * Actualiza el archivo de bitácora con datos de conteo y genera Excel para items no encontrados
 * @param {File} file - El archivo original
 * @param {Array} dbData - Los datos de la base de datos
 */
async function actualizarContenidoArchivo(file, dbData) {
    const reader = new FileReader();

    return new Promise((resolve, reject) => {
        reader.onload = async function(event) {
            try {
                const originalContent = event.target.result;
                const originalLines = originalContent.split(/\r?\n/);
                const noMatchData = [];

                // Actualizar líneas con datos de conteo
                const updatedLines = originalLines.map((line) => {
                    const parts = line.trim().split(/\s+/);

                    if (parts.length >= 6) {
                        const storBin = parts[1].trim();
                        const materialNo = parts[5].trim();

                        const matchingData = dbData.find(
                            (item) => item.storBin === storBin && item.materialNo === materialNo
                        );

                        if (matchingData) {
                            return line.replace(/_{12}/, matchingData.conteoFinal);
                        } else {
                            // Registrar items no encontrados
                            noMatchData.push({ storBin, materialNo });
                            return line.replace(/_{12}/, '0');
                        }
                    }

                    return line;
                });

                // Crear y descargar el archivo actualizado
                const finalContent = updatedLines.join("\n");
                const blob = new Blob([finalContent], { type: "text/plain" });

                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = `actualizado_${file.name}`;
                link.click();

                // Procesar items no encontrados y generar reporte Excel
                if (noMatchData.length > 0) {
                    const extraItems = await enviarDatosAlBackendAux(noMatchData);
                    descargarDataFromBackendPro(extraItems);
                }

                resolve();
            } catch (error) {
                reject(`Error al actualizar el archivo: ${error.message}`);
            }
        };

        reader.onerror = (error) => {
            reject(`Error al leer el archivo: ${error.message}`);
        };

        reader.readAsText(file);
    });
}

/**
 * Obtiene datos de los items no encontrados en el archivo de texto
 * @param {Array} noMatchData - Array de objetos con storBin y materialNo
 * @returns {Promise<Array>} - Array de objetos con datos detallados
 */
async function enviarDatosAlBackendAux(noMatchData) {
    try {
        const response = await fetch('daoAdmin/daoActualizar-txtAux.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(noMatchData)
        });

        if (!response.ok) {
            throw new Error(`Error en la respuesta del servidor: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('Error enviando datos al backend para items no encontrados:', error);
        throw error;
    }
}

/**
 * Genera un reporte Excel para items no encontrados en el archivo original
 * @param {Array} dataFromBackend - Array de objetos con datos de items
 */
function descargarDataFromBackendPro(dataFromBackend) {
    // Crear libro
    var wb = XLSX.utils.book_new();
    wb.Props = {
        Title: "SheetJS",
        Subject: "Números de parte faltantes",
        Author: "Grammer Inventario",
        CreatedDate: new Date()
    };

    wb.SheetNames.push("Test Sheet");

    // Definir fila de encabezado
    var ws_data = [['InventoryItem', 'Record', 'Bin', 'Bin/n', 'Contador', 'Numero Parte', 'Plant', 'Cantidad', 'Type']];

    // Seguimiento de conteos de storage bin
    var storBinCounts = {};

    // Procesar cada dato
    for (var i = 0; i < dataFromBackend.length; i++) {
        var item = dataFromBackend[i];

        // Omitir items sin números de parte
        if (!item.material) {
            continue;
        }

        // Gestionar conteo/numeración de storage bin
        if (!storBinCounts[item.storageBin]) {
            storBinCounts[item.storageBin] = 0;
        }
        storBinCounts[item.storageBin]++;

        // Nombre de bin modificado para bins que no comienzan con P
        var binWithCount = !item.storageBin.startsWith('P')
            ? `${item.storageBin}/${storBinCounts[item.storageBin]}`
            : item.storageBin;

        // Agregar fila a la hoja
        ws_data.push([
            item.inventoryItem || '',
            item.invRecount || '',
            item.storageBin || '',
            binWithCount,
            storBinCounts[item.storageBin] || '',
            item.material || '',
            item.plan || '',
            item.conteoFinal || '0',
            item.storageType || ''
        ]);
    }

    // Crear hoja y agregarla al libro
    var ws = XLSX.utils.aoa_to_sheet(ws_data);
    wb.Sheets["Test Sheet"] = ws;

    // Generar datos binarios
    var wbout = XLSX.write(wb, {bookType: 'xlsx', type: 'binary'});

    // Convertir a ArrayBuffer para blob
    function s2ab(s) {
        var buf = new ArrayBuffer(s.length);
        var view = new Uint8Array(buf);
        for (var i = 0; i < s.length; i++) {
            view[i] = s.charCodeAt(i) & 0xFF;
        }
        return buf;
    }

    // Crear y activar descarga
    saveAs(new Blob([s2ab(wbout)], {type: "application/octet-stream"}), 'Numeros_de_parte_faltantes.xlsx');
}

/**********************************************************************************************************************/
/*****************************************************TABLA STORAGE_UNIT***********************************************/
/**********************************************************************************************************************/

var nombreArchivoStorage = "";

/**
 * Inicializa los eventos y manejadores para el procesamiento de Storage Unit
 */
function iniciarProcesoStorage() {
    const btnTxtStorage = document.getElementById('btnTxtStorage');
    const fileInputTxtS = document.getElementById('fileInputTxtS');

    if (!btnTxtStorage || !fileInputTxtS) {
        console.error("No se encontraron los elementos requeridos para el procesamiento de storage unit.");
        return;
    }

    btnTxtStorage.addEventListener('click', () => {
        fileInputTxtS.click();
    });

    fileInputTxtS.addEventListener('change', async (event) => {
        const files = Array.from(event.target.files);

        if (files.length === 0) {
            console.error("No se seleccionaron archivos.");
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se seleccionó ningún archivo.'
            });
            return;
        }

        try {
            // Recopilar datos de items no coincidentes de todos los archivos
            const allNoMatchData = [];

            // Procesar cada archivo secuencialmente
            for (const file of files) {
                console.log(`Procesando archivo: ${file.name}`);
                const noMatchData = await procesarArchivoStorage(file);
                nombreArchivoStorage = file.name;

                if (Array.isArray(noMatchData) && noMatchData.length > 0) {
                    allNoMatchData.push(...noMatchData);
                }
            }

            // Procesar todos los items no coincidentes de todos los archivos
            if (allNoMatchData.length > 0) {
                await handleNoMatchData(allNoMatchData, nombreArchivoStorage);
            }

            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: 'Procesamiento de archivos completado.'
            });

        } catch (error) {
            console.error("Error al procesar los archivos:", error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: `Error al procesar los archivos: ${error.message}`
            });
        }
    });
}

/**
 * Procesa un solo archivo de storage
 * @param {File} file - El objeto archivo a procesar
 * @returns {Promise<Array>} - Array de items no coincidentes
 */
async function procesarArchivoStorage(file) {
    try {
        // Extraer datos del archivo
        const fileData = await manejarArchivoStorage(file);

        if (!fileData || fileData.length === 0) {
            throw new Error(`No se pudieron extraer datos del archivo ${file.name}`);
        }

        // Obtener datos desde el backend
        const dbData = await enviarDatosAlBackendStorage(fileData);

        if (!dbData || dbData.length === 0) {
            console.warn(`No se recibieron datos de la base de datos para el archivo ${file.name}`);
        }

        // Actualizar archivo con los datos del backend y obtener items no coincidentes
        const noMatchData = await actualizarArchivoStorage(file, dbData);
        return noMatchData;

    } catch (error) {
        console.error(`Error al procesar el archivo ${file.name}:`, error);
        throw error;
    }
}

/**
 * Extrae datos de storage unit y bin del archivo de storage
 * @param {File} file - El archivo a leer
 * @returns {Promise<Array>} - Array de objetos con storBin y storUnit
 */
function manejarArchivoStorage(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();

        reader.onload = (event) => {
            try {
                const contenido = event.target.result;
                const lineas = contenido.split(/\r?\n/);

                // Filtrar y extraer datos relevantes
                const datos = lineas
                    .map(linea => linea.trim())
                    .filter(linea => /^[0-9]+\s+\w+/.test(linea))
                    .map(linea => {
                        const partes = linea.split(/\s+/);
                        // Verificar si tenemos suficientes partes para una línea válida
                        return partes.length >= 7
                            ? {
                                storBin: partes[1].trim(),
                                storUnit: partes[6].trim()
                            }
                            : null;
                    })
                    .filter(Boolean); // Eliminar entradas nulas

                resolve(datos);
            } catch (error) {
                reject(`Error al procesar el contenido del archivo: ${error.message}`);
            }
        };

        reader.onerror = (error) => {
            reject(`Error al leer el archivo: ${error}`);
        };

        reader.readAsText(file);
    });
}

/**
 * Envía datos al backend para obtener información de storage
 * @param {Array} data - Array de objetos con storBin y storUnit
 * @returns {Promise<Array>} - Array de objetos con datos de cantidad
 */
async function enviarDatosAlBackendStorage(data) {
    try {
        const response = await fetch('daoAdmin/daoActualizarStorage-txt.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(`Error en la respuesta del servidor: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('Error enviando datos al backend para storage:', error);
        throw error;
    }
}

/**
 * Actualiza el archivo de storage con datos de cantidad y devuelve items no coincidentes
 * @param {File} file - El archivo original
 * @param {Array} dbData - Los datos de la base de datos
 * @returns {Promise<Array>} - Array de storage units no coincidentes
 */
function actualizarArchivoStorage(file, dbData) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        const noMatchData = [];

        reader.onload = function(event) {
            try {
                const originalContent = event.target.result;
                const originalLines = originalContent.split(/\r?\n/);

                // Actualizar líneas con datos de cantidad
                const updatedLines = originalLines.map((line) => {
                    const parts = line.trim().split(/\s+/);

                    if (parts.length >= 7) {
                        const storBin = parts[1].trim();
                        const storUnit = parts[6].trim();

                        const matchingData = dbData.find(
                            (item) => item.storBin === storBin && item.storUnit === storUnit
                        );

                        if (matchingData) {
                            return line.replace(/_{12}/, matchingData.cantidad);
                        } else {
                            // Registrar items no encontrados
                            noMatchData.push({ storUnit });
                            return line.replace(/_{12}/, '0');
                        }
                    }

                    return line;
                });

                // Crear y descargar el archivo actualizado
                const finalContent = updatedLines.join("\n");
                const blob = new Blob([finalContent], { type: "text/plain" });

                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = `actualizado_${file.name}`;
                link.click();

                // Devolver el nombre de archivo original y los items no coincidentes
                resolve(noMatchData);
            } catch (error) {
                reject(`Error al actualizar el archivo: ${error.message}`);
            }
        };

        reader.onerror = (error) => {
            reject(`Error al leer el archivo: ${error.message}`);
        };

        reader.readAsText(file);
    });
}

/**
 * Maneja el procesamiento de storage units no coincidentes
 * @param {Array} noMatchData - Array de storage units no coincidentes
 * @param {string} nombreArchivoStorage - Nombre del archivo original
 */
async function handleNoMatchData(noMatchData, nombreArchivoStorage) {
    try {
        // Obtener datos de items no coincidentes
        const extraItems = await enviarDatosAlBackendStorageAux(noMatchData);

        // Generar reporte Excel si hay datos
        if (extraItems && extraItems.length > 0) {
            descargarDataFromBackend(extraItems, nombreArchivoStorage);
        }
    } catch (error) {
        console.error('Error procesando items no encontrados:', error);
        throw error;
    }
}

/**
 * Obtiene datos para storage units no encontradas en el archivo de texto
 * @param {Array} noMatchData - Array de objetos con storUnit
 * @returns {Promise<Array>} - Array de objetos con datos detallados
 */
async function enviarDatosAlBackendStorageAux(noMatchData) {
    try {
        const response = await fetch('daoAdmin/daoActualizarStorage-txtAux.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ storageUnits: noMatchData })
        });

        if (!response.ok) {
            throw new Error(`Error en la respuesta del servidor: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('Error enviando datos al backend para items no encontrados:', error);
        throw error;
    }
}

/**
 * Genera un reporte Excel para storage units no encontradas en el archivo original
 * @param {Array} dataFromBackend - Array de objetos con datos de items
 * @param {string} nombreArchivoStorage - Nombre del archivo original
 */
function descargarDataFromBackend(dataFromBackend, nombreArchivoStorage) {
    // Crear libro
    var wb = XLSX.utils.book_new();
    wb.Props = {
        Title: "SheetJS",
        Subject: nombreArchivoStorage,
        Author: "Grammer Inventario",
        CreatedDate: new Date()
    };

    wb.SheetNames.push("Test Sheet");

    // Definir fila de encabezado
    var ws_data = [['InventoryItem', 'Page', 'Bin', 'Bin/n', 'Contador', 'Numero Parte', 'Plant', 'Cantidad', 'Sun', 'Type']];

    // Seguimiento de conteos de storage bin
    var storBinCounts = {};

    // Procesar cada dato
    for (var i = 0; i < dataFromBackend.length; i++) {
        var item = dataFromBackend[i];

        // Omitir items sin números de parte
        if (!item.numero_Parte) {
            continue;
        }

        // Gestionar conteo/numeración de storage bin
        if (!storBinCounts[item.storage_Bin]) {
            storBinCounts[item.storage_Bin] = 0;
        }
        storBinCounts[item.storage_Bin]++;

        // Nombre de bin modificado para bins que no comienzan con P
        var binWithCount = !item.storage_Bin.startsWith('P')
            ? `${item.storage_Bin}/${storBinCounts[item.storage_Bin]}`
            : item.storage_Bin;

        // Agregar fila a la hoja
        ws_data.push([
            item.inventoryItem || '',
            item.inventoryPage || '',
            item.storage_Bin || '',
            binWithCount,
            storBinCounts[item.storage_Bin] || '',
            item.numero_Parte || '',
            item.plan || '',
            item.cantidad || '0',
            item.storageUnit || '',
            item.storage_Type || ''
        ]);
    }

    // Crear hoja y agregarla al libro
    var ws = XLSX.utils.aoa_to_sheet(ws_data);
    wb.Sheets["Test Sheet"] = ws;

    // Generar datos binarios
    var wbout = XLSX.write(wb, {bookType: 'xlsx', type: 'binary'});

    // Convertir a ArrayBuffer para blob
    function s2ab(s) {
        var buf = new ArrayBuffer(s.length);
        var view = new Uint8Array(buf);
        for (var i = 0; i < s.length; i++) {
            view[i] = s.charCodeAt(i) & 0xFF;
        }
        return buf;
    }

    // Crear y activar descarga
    saveAs(new Blob([s2ab(wbout)], {type: "application/octet-stream"}), nombreArchivoStorage + '.xlsx');
}

/**
 * Función de utilidad para mostrar tooltips con imágenes de ejemplo
 * @param {string} id - ID del elemento al que adjuntar el tooltip
 * @param {string} imageUrl - URL de la imagen a mostrar
 * @param {number} width - Ancho de la imagen
 * @param {number} height - Alto de la imagen
 */
function mostrarImagenTooltip(id, imageUrl, width, height) {
    tippy(`#${id}`, {
        content: `<img src="${imageUrl}" width="${width}" height="${height}">`,
        allowHTML: true,
        interactive: true,
        placement: 'right',
        theme: 'light',
        trigger: 'click',
        onShow(instance) {
            setTimeout(() => {
                instance.hide();
            }, 5000);
        }
    });
}