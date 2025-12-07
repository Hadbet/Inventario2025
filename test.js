// ✅ FUNCIÓN PARA PROCESAR CSV Y ACTUALIZAR CANTIDADES

async function procesarCSVConCantidades(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();

        reader.onload = async (event) => {
            try {
                const contenido = event.target.result;
                const lineas = contenido.split(/\r?\n/);

                console.log("📄 Total líneas en CSV:", lineas.length);

                // Encontrar la línea de encabezados (contiene "Item,StorageBin,...")
                let headerIndex = -1;
                for (let i = 0; i < lineas.length; i++) {
                    if (lineas[i].includes('Item,StorageBin')) {
                        headerIndex = i;
                        break;
                    }
                }

                if (headerIndex === -1) {
                    throw new Error('No se encontró el encabezado del CSV');
                }

                console.log("📋 Encabezado encontrado en línea:", headerIndex);

                // Parsear el CSV usando Papa Parse (si está disponible) o manualmente
                const dataRows = [];
                const storageUnits = [];

                // Procesar cada línea después del encabezado
                for (let i = headerIndex + 1; i < lineas.length; i++) {
                    const linea = lineas[i].trim();
                    if (!linea) continue;

                    // Parsear CSV (respetando comillas)
                    const valores = parseCSVLine(linea);

                    if (valores.length >= 9) {
                        const storageUnit = valores[7].trim(); // Columna 8 (índice 7)

                        if (storageUnit && storageUnit !== '') {
                            dataRows.push({
                                lineIndex: i,
                                storageUnit: storageUnit,
                                originalLine: linea
                            });

                            if (!storageUnits.includes(storageUnit)) {
                                storageUnits.push(storageUnit);
                            }
                        }
                    }
                }

                console.log("🔢 Total Storage Units únicos encontrados:", storageUnits.length);
                console.log("📊 Storage Units:", storageUnits.slice(0, 10), "...");

                // Consultar la base de datos
                console.log("🔍 Consultando base de datos...");

                const response = await fetch('https://grammermx.com/Logistica/Inventario2025/daoAdmin/daoConsultarStorageUnitsCantidades.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ storageUnits: storageUnits })
                });

                if (!response.ok) {
                    throw new Error(`Error del servidor: ${response.status}`);
                }

                const mapaCantidades = await response.json();
                console.log("✅ Respuesta de la base de datos:", mapaCantidades);
                console.log("📦 Total encontrados en BD:", Object.keys(mapaCantidades).length);

                // Actualizar las líneas del CSV
                let encontrados = 0;
                let noEncontrados = 0;

                for (const row of dataRows) {
                    const su = row.storageUnit;

                    if (mapaCantidades[su]) {
                        const cantidad = mapaCantidades[su].cantidad || '0';

                        // Reemplazar "0 PC" por la cantidad real
                        const lineaActualizada = lineas[row.lineIndex].replace(
                            /"0 PC"$/,
                            `"${cantidad} PC"`
                        );

                        lineas[row.lineIndex] = lineaActualizada;
                        encontrados++;

                        console.log(`✅ ${su} -> ${cantidad} PC`);
                    } else {
                        noEncontrados++;
                        console.log(`❌ ${su} -> No encontrado en BD`);
                    }
                }

                console.log("\n📊 RESUMEN:");
                console.log(`✅ Encontrados: ${encontrados}`);
                console.log(`❌ No encontrados: ${noEncontrados}`);

                // Reconstruir el CSV
                const csvActualizado = lineas.join('\n');

                resolve({
                    contenido: csvActualizado,
                    stats: {
                        total: dataRows.length,
                        encontrados: encontrados,
                        noEncontrados: noEncontrados
                    }
                });

            } catch (error) {
                console.error('❌ Error al procesar CSV:', error);
                reject(error);
            }
        };

        reader.onerror = (error) => {
            console.error('❌ Error al leer archivo:', error);
            reject(error);
        };

        reader.readAsText(file);
    });
}

// Función auxiliar para parsear líneas CSV con comillas
function parseCSVLine(line) {
    const valores = [];
    let valorActual = '';
    let dentroDeComillas = false;

    for (let i = 0; i < line.length; i++) {
        const char = line[i];

        if (char === '"') {
            dentroDeComillas = !dentroDeComillas;
        } else if (char === ',' && !dentroDeComillas) {
            valores.push(valorActual);
            valorActual = '';
        } else {
            valorActual += char;
        }
    }

    // Agregar el último valor
    valores.push(valorActual);

    return valores;
}

// ✅ EJEMPLO DE USO:
/*
document.getElementById('btnProcesarCSV').addEventListener('click', async () => {
    const fileInput = document.getElementById('fileInputCSV');
    const file = fileInput.files[0];

    if (!file) {
        alert('Por favor selecciona un archivo CSV');
        return;
    }

    Swal.fire({
        title: 'Procesando CSV',
        text: 'Actualizando cantidades desde la base de datos...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const resultado = await procesarCSVConCantidades(file);

        Swal.close();

        // Descargar el CSV actualizado
        const blob = new Blob([resultado.contenido], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `actualizado_${file.name}`;
        link.click();
        URL.revokeObjectURL(url);

        // Mostrar estadísticas
        Swal.fire({
            icon: 'success',
            title: 'CSV Actualizado',
            html: `
                <p>Total: ${resultado.stats.total}</p>
                <p>✅ Encontrados: ${resultado.stats.encontrados}</p>
                <p>❌ No encontrados: ${resultado.stats.noEncontrados}</p>
            `
        });

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al procesar el CSV: ' + error.message
        });
    }
});
*/