/**
 * Funciones JavaScript para la impresión de reportes
 */

/**
 * Imprime la tabla actual
 * @param {string} tableId - ID de la tabla a imprimir
 * @param {string} title - Título del reporte
 */
function printTable(tableId, title) {
  const printWindow = window.open("", "_blank")

  const styles = `
          <style>
              body {
                  font-family: Arial, sans-serif;
                  margin: 20px;
              }
              h1 {
                  text-align: center;
                  color: #667eea;
                  margin-bottom: 10px;
              }
              .print-info {
                  text-align: center;
                  margin-bottom: 20px;
                  font-size: 12px;
                  color: #666;
              }
              table {
                  width: 100%;
                  border-collapse: collapse;
                  margin-bottom: 20px;
              }
              th {
                  background-color: #667eea;
                  color: white;
                  font-weight: bold;
                  text-align: left;
                  padding: 8px;
                  border: 1px solid #ddd;
              }
              td {
                  padding: 8px;
                  border: 1px solid #ddd;
              }
              tr:nth-child(even) {
                  background-color: #f2f2f2;
              }
              .no-print {
                  display: none;
              }
              @media print {
                  body {
                      margin: 0;
                      padding: 15px;
                  }
                  h1 {
                      font-size: 18px;
                      margin-bottom: 5px;
                  }
                  .print-info {
                      font-size: 10px;
                      margin-bottom: 10px;
                  }
                  table {
                      font-size: 12px;
                  }
                  th, td {
                      padding: 5px;
                  }
              }
          </style>
      `

  const table = document.getElementById(tableId)
  if (!table) {
    alert("Tabla no encontrada")
    return
  }

  const tableClone = table.cloneNode(true)

  const noPrintElements = tableClone.querySelectorAll(".no-print")
  noPrintElements.forEach((element) => {
    element.remove()
  })

  const content = `
          <!DOCTYPE html>
          <html lang="es">
          <head>
              <meta charset="UTF-8">
              <title>${title}</title>
              ${styles}
          </head>
          <body>
              <h1>${title}</h1>
              <div class="print-info">
                  Generado el ${new Date().toLocaleString()}
              </div>
              ${tableClone.outerHTML}
          </body>
          </html>
      `

  printWindow.document.open()
  printWindow.document.write(content)
  printWindow.document.close()

  printWindow.onload = () => {
    printWindow.print()
  }
}

/**
 * Exporta la tabla a PDF
 * @param {string} url - URL del script de exportación
 * @param {string} tableId - ID de la tabla a exportar
 * @param {Object} filters - Filtros aplicados
 */
function exportTableToPDF(url, tableId, filters = {}) {
  let exportUrl = `${url}?formato=pdf&tabla=${tableId}`

  for (const [key, value] of Object.entries(filters)) {
    if (value) {
      exportUrl += `&${key}=${encodeURIComponent(value)}`
    }
  }

  window.location.href = exportUrl
}

/**
 * Exporta la tabla a CSV
 * @param {string} url - URL del script de exportación
 * @param {string} tableId - ID de la tabla a exportar
 * @param {Object} filters - Filtros aplicados
 */
function exportTableToCSV(url, tableId, filters = {}) {
  let exportUrl = `${url}?formato=csv&tabla=${tableId}`

  for (const [key, value] of Object.entries(filters)) {
    if (value) {
      exportUrl += `&${key}=${encodeURIComponent(value)}`
    }
  }

  window.location.href = exportUrl
}
