function downloadPDF() {
    const table = document.getElementById('applicant-table');

    // Clone table to avoid modifying original
    const clone = table.cloneNode(true);

    // Remove the last row if needed
    const tbody = clone.querySelector('tbody');
    if (tbody) {
        const rows = tbody.querySelectorAll('tr');
        if (rows.length > 0) rows[rows.length - 1].remove();
    }

    // Append clone to a temporary container
    const container = document.createElement('div');
    container.style.position = 'absolute';
    container.style.left = '-9999px'; // Offscreen
    container.appendChild(clone);
    document.body.appendChild(container);

    // Render table using html2canvas
    html2canvas(clone, {
        scale: 2,
        backgroundColor: '#ffffff',
        allowTaint: false,
        useCORS: true
    }).then((canvas) => {
        const imgData = canvas.toDataURL('image/png');

        // Initialize jsPDF (A4 portrait)
        const pdf = new jspdf.jsPDF({
            orientation: 'portrait',
            unit: 'px',
            format: 'a4'
        });

        // PDF page dimensions
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = pdf.internal.pageSize.getHeight();

        // Image dimensions
        const imgWidth = canvas.width;
        const imgHeight = canvas.height;

        // Calculate scaling to fit width
        const ratio = pdfWidth / imgWidth;
        const scaledHeight = imgHeight * ratio;

        let yPosition = 20; // Top margin

        if (scaledHeight < pdfHeight) {
            // Center vertically if short table
            yPosition = (pdfHeight - scaledHeight) / 2;
        }

        // Add image to PDF
        pdf.addImage(imgData, 'PNG', 0, yPosition, pdfWidth, scaledHeight);

        // Save PDF
        pdf.save('applicant-details.pdf');

        // Remove temporary container
        document.body.removeChild(container);
    }).catch((error) => {
        console.error('html2canvas error:', error);
        alert('Failed to generate PDF. Check console.');
        document.body.removeChild(container);
    });
}
