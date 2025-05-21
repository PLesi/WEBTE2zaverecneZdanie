document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('pdfForm');
    const pdfInput = document.getElementById('pdfInput');
    const compressionLevel = document.getElementById('compressionLevel');
    const previewContainer = document.getElementById('previewContainer');
    const pdfPagesContainer = document.getElementById('pdfPages');
    const downloadBtn = document.getElementById('downloadBtn');
    const originalSizeText = document.getElementById('povodna-velkost');
    const compressedSizeText = document.getElementById('compressed-size');

    let compressedBlob = null;

    pdfInput.addEventListener('change', () => {
        const file = pdfInput.files[0];
        if (file) {
            const sizeKB = (file.size / 1024).toFixed(2);
            originalSizeText.innerText = `Original Size: ${sizeKB} KB`;
        } else {
            originalSizeText.innerText = '';
        }
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const file = pdfInput.files[0];
        if (!file) return alert('Please upload a PDF.');

        const level = compressionLevel.value;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('level', level);

        try {
            const response = await fetch('http://node75.webte.fei.stuba.sk/api/pdf/compressNew', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) throw new Error('Compression failed');

            compressedBlob = await response.blob();
            const blobUrl = URL.createObjectURL(compressedBlob);

            const sizeKB = (compressedBlob.size / 1024).toFixed(2);
            compressedSizeText.innerText = `Compressed Size: ${sizeKB} KB`;
            previewContainer.style.display = 'flex';

            // Clear previous pages
            pdfPagesContainer.innerHTML = '';

            // Load PDF with PDF.js and render pages
            const pdf = await pdfjsLib.getDocument(blobUrl).promise;
            for (let i = 1; i <= pdf.numPages; i++) {
                const page = await pdf.getPage(i);
                const viewport = page.getViewport({
                    scale: 0.6
                }); // smaller scale for thumbnails

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                await page.render({
                    canvasContext: context,
                    viewport: viewport
                }).promise;
                pdfPagesContainer.appendChild(canvas);
            }

            URL.revokeObjectURL(blobUrl);
        } catch (err) {
            alert(err.message);
        }
    });

    downloadBtn.addEventListener('click', () => {
        if (!compressedBlob) return;
        const a = document.createElement('a');
        a.href = URL.createObjectURL(compressedBlob);
        a.download = 'compressed.pdf';
        a.click();
    });
});