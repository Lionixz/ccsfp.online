function openPrintWindow(autoClose = false) {
    const table = document.getElementById("applicant-table");

    if (!table) {
        alert("Table not found.");
        return;
    }

    const printWindow = window.open('', '_blank');

    if (!printWindow) {
        alert("Popup blocked! Please allow popups for this site.");
        return;
    }

    printWindow.document.open();
    printWindow.document.write(`
    <html>
        <head>
            <title>Application Form</title>
            <style>
                @page {
                    size: A4 portrait;
                    margin: 10mm;
                }

                html, body {
                    width: 210mm;
                    min-height: 297mm;
                    margin: 0;
                    padding: 0;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: #fff;
                    color: #000;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    table-layout: fixed;
                }

                td {
                    border: 1px solid #000;
                    padding: 5px;
                    font-size: 10.5px;
                    vertical-align: middle;
                    word-wrap: break-word;
                }

                tr {
                    page-break-inside: avoid;
                }

                img {
                    max-width: 100%;
                    height: auto;
                }

                input {
                    border: none;
                    width: 100%;
                    font-size: 10.5px;
                    color: #000;
                    background: transparent;
                }

                .photo-wrapper {
                    height: 120px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                /* NEW RULE: Constrain image inside the photo wrapper */
                .photo-wrapper img {
                    max-width: 100%;
                    max-height: 100%;
                    object-fit: contain;
                }
            </style>
        </head>
        <body>
            ${table.outerHTML}
        </body>
    </html>
    `);
    printWindow.document.close();

    printWindow.onload = function () {
        setTimeout(() => {
            printWindow.focus();
            printWindow.print();

            if (autoClose) {
                printWindow.onafterprint = function () {
                    printWindow.close();
                };
            }
        }, 300);
    };
}

function printFile() {
    openPrintWindow(true);
}