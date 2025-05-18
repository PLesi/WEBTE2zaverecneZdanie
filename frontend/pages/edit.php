<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PDF Editing</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>

<body>
    <!-- Navigačný panel -->
    <?php include 'navbar.php'; ?>

    <h1>PDF Editing</h1>
    <input type="file" id="pdf-upload" accept="application/pdf" />
    <input id="pen-width" type="range" min="3" value="5" step="2" max="25">
    <input type="color" value="#000000" id="pen-color" />
    <input type="text" value="1" id="page-number" />
    <button onclick="prevPage()"> &lt; </button>
    <button onclick="nextPage()"> &gt; </button>
    <button onclick="handleInput()">Text</button>
    <input id="text-size" type="number" min="3" value="16" step="2" max="72">
    <input id="text-color" type="color" value="#000000" />
    <button onclick="toggleEraser()">Eraser</button>
    <input id="eraser-width" type="range" min="3" value="5" step="2" max="25">
    <button onclick="clearPage()">Clear All</button>
    <button onclick="saveAndSend()">Save and Send</button>

    <div id="pdf-container">
        <canvas id="pdf-canvas"></canvas>
        <canvas id="draw-canvas"></canvas>
    </div>

    <div id="pdf-preview">
        <h2>Page Previews</h2>
        <div id="thumbnail-container" class="thumbnail-grid"></div>
    </div>

    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- i18next -->
    <script src="https://unpkg.com/i18next@23.15.1/dist/umd/i18next.min.js"></script>
    <!-- custom JS -->
    <script src="../assets/js/i18n.js"></script>
    <script>
        let pdfDoc = null,
            typedarray = null;
        let drawings = {},
            textAnnotations = {};
        let textMode = false,
            eraserMode = false,
            drawing = false;
        let ctx = document.getElementById('draw-canvas').getContext('2d');
        let textSize = parseInt(document.getElementById('text-size').value);
        let textColor = document.getElementById('text-color').value;

        const drawCanvas = document.getElementById('draw-canvas');
        const pdfCanvas = document.getElementById('pdf-canvas');

        document.getElementById('text-color').addEventListener('change', () => {
            textColor = document.getElementById('text-color').value;
        });

        document.getElementById('text-size').addEventListener('change', () => {
            textSize = parseInt(document.getElementById('text-size').value) || 16;
        });

        document.getElementById('eraser-width').addEventListener('change', () => {
            eraserSize = parseInt(document.getElementById('eraser-width').value) || 5;
        });

        function handleInput() {
            textMode = true;
            eraserMode = false;
            drawCanvas.style.cursor = "text";
        }

        function toggleEraser() {
            eraserMode = !eraserMode;
            textMode = false;
            drawCanvas.style.cursor = eraserMode ? "crosshair" : "default";
        }

        function clearPage() {
            const pageNum = parseInt(document.getElementById('page-number').value);
            ctx.clearRect(0, 0, drawCanvas.width, drawCanvas.height);
            drawings[pageNum] = null;
            textAnnotations[pageNum] = [];
            loadPage(pageNum);
            updateThumbnail(pageNum);
        }

        function prevPage() {
            let pageNum = parseInt(document.getElementById('page-number').value);
            if (pageNum <= 1) return;
            saveCurrentDrawing();
            pageNum--;
            document.getElementById('page-number').value = pageNum;
            loadPage(pageNum);
        }

        function nextPage() {
            let pageNum = parseInt(document.getElementById('page-number').value);
            if (!pdfDoc || pageNum >= pdfDoc.numPages) return;
            saveCurrentDrawing();
            pageNum++;
            document.getElementById('page-number').value = pageNum;
            loadPage(pageNum);
        }

        function drawTextOnCanvas(text, x, y) {
            ctx.font = `${textSize}px Arial`;
            ctx.fillStyle = textColor;
            ctx.fillText(text, x, y);
        }

        drawCanvas.addEventListener('mousedown', () => drawing = true);
        drawCanvas.addEventListener('mouseup', () => {
            drawing = false;
            ctx.beginPath();
        });

        drawCanvas.addEventListener('mousemove', (e) => {
            if (!drawing) return;

            const pageNum = parseInt(document.getElementById('page-number').value);
            if (eraserMode) {
                const eraserSize = parseInt(document.getElementById('eraser-width').value);
                ctx.clearRect(e.offsetX - eraserSize / 2, e.offsetY - eraserSize / 2, eraserSize, eraserSize);
                const threshold = eraserSize / 2;
                saveCurrentDrawing();
                updateThumbnail(pageNum);
                if (textAnnotations[pageNum]) {
                    textAnnotations[pageNum] = textAnnotations[pageNum].filter(({
                            x,
                            y
                        }) =>
                        Math.abs(x - e.offsetX) > threshold || Math.abs(y - e.offsetY) > threshold
                    );
                }
            } else {
                ctx.lineWidth = parseInt(document.getElementById('pen-width').value) || 5;
                ctx.lineCap = "round";
                ctx.strokeStyle = document.getElementById('pen-color').value;
                ctx.lineTo(e.offsetX, e.offsetY);
                ctx.stroke();
                ctx.beginPath();
                ctx.moveTo(e.offsetX, e.offsetY);
                textMode = false;
                eraserMode = false;
                saveCurrentDrawing();
                updateThumbnail(pageNum);
            }
        });

        drawCanvas.addEventListener('click', (e) => {
            if (!textMode) return;

            const input = document.createElement("input");
            input.style.backgroundColor = "transparent";
            input.style.color = textColor;
            input.style.border = "none";
            input.type = "text";
            input.style.position = "absolute";
            input.style.left = `${e.pageX}px`;
            input.style.top = `${e.pageY}px`;
            input.style.font = `${textSize}px Arial`;
            input.style.zIndex = 10;
            document.body.appendChild(input);
            input.focus();

            input.addEventListener("blur", () => {
                const text = input.value;
                const pageNum = parseInt(document.getElementById('page-number').value);
                if (text) {
                    if (!textAnnotations[pageNum]) textAnnotations[pageNum] = [];
                    textAnnotations[pageNum].push({
                        text,
                        x: e.offsetX,
                        y: e.offsetY
                    });
                    drawTextOnCanvas(text, e.offsetX, e.offsetY);
                    saveCurrentDrawing();
                    updateThumbnail(pageNum);
                }
                document.body.removeChild(input);
            });
        });

        function saveCurrentDrawing() {
            const pageNum = parseInt(document.getElementById('page-number').value);
            drawings[pageNum] = drawCanvas.toDataURL("image/png");
        }

        function renderThumbnails() {
            const container = document.getElementById('thumbnail-container');
            container.innerHTML = '';
            for (let i = 1; i <= pdfDoc.numPages; i++) {
                pdfDoc.getPage(i).then(page => {
                    const viewport = page.getViewport({
                        scale: 0.2
                    });
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.setAttribute('data-page', i);
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;

                    page.render({
                        canvasContext: context,
                        viewport: viewport
                    }).promise.then(() => {
                        canvas.addEventListener('click', () => {
                            saveCurrentDrawing();
                            document.getElementById('page-number').value = i;
                            loadPage(i);
                        });
                    });

                    container.appendChild(canvas);
                });
            }
        }

        function updateThumbnail(pageNum) {
            pdfDoc.getPage(pageNum).then(page => {
                const container = document.getElementById('thumbnail-container');
                const canvas = container.querySelector(`canvas[data-page="${pageNum}"]`);
                if (!canvas) return;

                const context = canvas.getContext('2d');
                const viewport = page.getViewport({
                    scale: 0.2
                });
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                page.render({
                    canvasContext: context,
                    viewport: viewport
                }).promise.then(() => {
                    if (drawings[pageNum]) {
                        const img = new Image();
                        img.onload = () => {
                            context.drawImage(img, 0, 0, canvas.width, canvas.height);
                        };
                        img.src = drawings[pageNum];
                    }

                    if (textAnnotations[pageNum]) {
                        context.save();
                        context.scale(0.2, 0.2);
                        textAnnotations[pageNum].forEach(({
                            text,
                            x,
                            y
                        }) => {
                            context.font = `${textSize}px Arial`;
                            context.fillStyle = textColor;
                            context.fillText(text, x, y);
                        });
                        context.restore();
                    }
                });
            });
        }

        function loadPage(pageNumber) {
            pdfDoc.getPage(pageNumber).then(page => {
                const viewport = page.getViewport({
                    scale: 1.5
                });
                pdfCanvas.width = drawCanvas.width = viewport.width;
                pdfCanvas.height = drawCanvas.height = viewport.height;
                document.getElementById('pdf-preview').style.marginTop = pdfCanvas.height + 'px';

                const renderContext = {
                    canvasContext: pdfCanvas.getContext('2d'),
                    viewport: viewport
                };

                page.render(renderContext).promise.then(() => {
                    ctx.clearRect(0, 0, drawCanvas.width, drawCanvas.height);
                    const pageNum = parseInt(document.getElementById('page-number').value);
                    if (drawings[pageNum]) {
                        const img = new Image();
                        img.onload = () => ctx.drawImage(img, 0, 0);
                        img.src = drawings[pageNum];
                    }
                    if (textAnnotations[pageNum]) {
                        textAnnotations[pageNum].forEach(({
                            text,
                            x,
                            y
                        }) => {
                            drawTextOnCanvas(text, x, y);
                        });
                    }
                });
            });
        }

        document.getElementById('pdf-upload').addEventListener('change', function() {
            const file = this.files[0];
            const reader = new FileReader();
            reader.onload = function() {
                typedarray = new Uint8Array(this.result);
                pdfjsLib.getDocument(typedarray).promise.then(pdf => {
                    pdfDoc = pdf;
                    loadPage(1);
                    renderThumbnails();
                    document.getElementById('page-number').addEventListener('change', () => {
                        const pageNum = parseInt(document.getElementById('page-number').value);
                        saveCurrentDrawing();
                        loadPage(pageNum);
                    });
                });
            };
            reader.readAsArrayBuffer(file);
        });

        function saveAndSend() {
            saveCurrentDrawing();
            const pageNum = parseInt(document.getElementById('page-number').value);
            const annotationBlob = drawCanvas.toDataURL("image/png");
            const fileInput = document.getElementById('pdf-upload');
            const formData = new FormData();
            formData.append("pdf", fileInput.files[0]);
            formData.append("annotation", dataURLtoBlob(annotationBlob));

            fetch("http://node75.webte.fei.stuba.sk/api/pdf/edit", {
                    method: "POST",
                    body: formData
                }).then(res => res.blob())
                .then(blob => {
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = "edited.pdf";
                    link.click();
                });
        }

        function dataURLtoBlob(dataurl) {
            const arr = dataurl.split(',');
            const mime = arr[0].match(/:(.*?);/)[1];
            const bstr = atob(arr[1]);
            let n = bstr.length;
            const u8arr = new Uint8Array(n);
            while (n--) u8arr[n] = bstr.charCodeAt(n);
            return new Blob([u8arr], {
                type: mime
            });
        }
    </script>
</body>

</html>