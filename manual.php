<!DOCTYPE html>
<html>
<head>
    <title>Download as PDF</title>
</head>
<body>

    <div id="toPDF" style="border: 1px solid #ccc; padding: 20px;">
        <h1>This is the content to be converted to PDF</h1>
        <p>Here's some important information:</p>
        <ul>
            <li>Item Alpha</li>
            <li>Item Beta</li>
            <li>Item Gamma</li>
        </ul>
    </div>

    <button onclick="downloadPDF()">Download as PDF</button>

    <script>
        function downloadPDF() {
            const pdfContent = document.getElementById('toPDF').innerHTML;
            const formData = new FormData();
            formData.append('pdf_content', pdfContent);

            fetch('manualPDF.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'dynamic_div_content.pdf';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>

</body>
</html>