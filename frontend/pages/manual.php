<!DOCTYPE html>
<html>
<head>
    <title>Download as PDF</title>
</head>
<body>
    <!-- To co bude v PDF, taktiež viditelne na webe -->
    <div id="toPDF" style="border: 1px solid #ccc; padding: 20px;">
        <h1>PDF Editor manual</h1>
        <p>Operácie:</p>
        <ul>
            <li>Komprimovať PDF</li>
            <li>JPG do PDF</li>
            <li>Spojiť PDF</li>
            <li>Rotovať stránky</li>
            <li>Číslovať stránky</li>
            <li>Pridať heslo</li>
            <li>Editovať PDF</li>
            <li>Odstrániť stránku</li>
            <li>Rozdeliť PDF</li>
            <li>Preskupiť stránky</li>
        </ul>
        <h2>1. Komprimovať PDF</h2>
        <p>Na kompresiu PDF súboru vyberte súbor a nastavte úroveň kompresie (1-9).</p>
        <h2>2. JPG do PDF</h2>
        <p>Na prevod JPG obrázkov do PDF vyberte obrázky a nastavte poradie.</p>
        <h2>3. Spojiť PDF</h2>
        <p>Na spojenie viacerých PDF súborov vyberte súbory a nastavte poradie.</p>
        <h2>4. Rotovať stránky</h2>
        <p>Na otočenie stránok PDF súboru vyberte súbor a nastavte uhol rotácie.</p>
        <h2>5. Číslovať stránky</h2>
        <p>Na číslovanie stránok PDF súboru vyberte súbor a nastavte formát číslovania.</p>
        <h2>6. Pridať heslo</h2>
        <p>Na pridanie hesla do PDF súboru vyberte súbor a zadajte heslo.</p>
        <h2>7. Editovať PDF</h2>
        <p>Na editáciu PDF súboru vyberte súbor a nastavte požadované úpravy.</p>
        <h2>8. Odstrániť stránku</h2>
        <p>Na odstránenie stránky z PDF súboru vyberte súbor a nastavte číslo stránky na odstránenie.</p>
        <h2>9. Rozdeliť PDF</h2>
        <p>Na rozdelenie PDF súboru vyberte súbor a nastavte rozsah stránok na rozdelenie.</p>
        <h2>10. Preskupiť stránky</h2>
        <p>Na preskupenie stránok PDF súboru vyberte súbor a nastavte nové poradie stránok.</p>
    </div>

    <button onclick="downloadPDF()">Download as PDF</button>

    <script>
        function downloadPDF() {
            const pdfContent = document.getElementById('toPDF').innerHTML;
            const formData = new FormData();
            formData.append('pdf_content', pdfContent);

            fetch('../../manualPDF.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'manual.pdf';
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