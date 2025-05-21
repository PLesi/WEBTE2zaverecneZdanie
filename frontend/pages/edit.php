<?php
    session_start();
    if (!isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] != true) {
        header("Location: login_form.php");
    } 
    $apiKey = $_SESSION['api_key'] ?? null;
?>
<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title data-i18n="edit.title">Editovať PDF</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="../assets/css/operations.css" rel="stylesheet">
    <style>
        /* Štýlovanie ovládacích prvkov */
        .edit-controls {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .edit-controls input[type="range"],
        .edit-controls input[type="color"],
        .edit-controls input[type="number"] {
            max-width: 100px;
        }

        .edit-controls input[type="number"] {
            width: 60px;
        }

        .thumbnail-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .thumbnail-grid canvas {
            border: 1px solid #ccc;
            cursor: pointer;
        }

        #pdf-container {
            position: relative;
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        #pdf-canvas,
        #draw-canvas {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
        }
    </style>
</head>

<body>
<!-- Navigačný panel -->
<?php include 'navbarPDFoperations.php'; ?>
<div class="hero-section">
    <div class="container mt-5">
        <h1 class="display-4" data-i18n="edit.title">PDF Editing</h1>

        <!-- Chybové hlásenie -->
        <div id="errorMessage">
            <span id="errorText"></span>
            <button type="button" class="close-btn" onclick="hideErrorMessage()">×</button>
        </div>

        <div class="text-center mb-3">
            <!-- Skrytý input -->
            <input type="file" id="pdfInput" accept="application/pdf" />

            <!-- Vlastné tlačidlo s ikonou -->
            <label for="pdfInput" class="custom-file-upload">
                <i class="bi bi-upload"></i>
                <span data-i18n="compress.upload_label">Nahraj PDF</span>
            </label>

            <!-- Zobrazenie názvu súboru -->
            <div id="fileInfo" class="mt-2">
                <span id="fileNameDisplay"></span>
            </div>
        </div>

        <!-- Ovládacie prvky -->
        <div class="edit-controls">
            <div class="d-flex align-items-center gap-2">
                <label for="pen-width" data-i18n="edit.title_pen_width">Pen Width:</label>
                <input id="pen-width" type="range" min="3" value="5" step="2" max="25">
            </div>
            <div class="d-flex align-items-center gap-2">
                <label for="pen-color" data-i18n="edit.title_pen_color">Pen Color:</label>
                <input type="color" value="#000000" id="pen-color">
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-primary" onclick="handleInput()" data-i18n="edit.text_button">Text</button>
                <label for="text-size" data-i18n="edit.title_text_size">Text Size:</label>
                <input id="text-size" type="number" min="3" value="16" step="2" max="72" class="form-control">
                <label for="text-color" data-i18n="edit.title_text_color">Text Color:</label>
                <input id="text-color" type="color" value="#000000">
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-primary" onclick="toggleEraser()" data-i18n="edit.eraser_button">Eraser</button>
                <label for="eraser-width" data-i18n="edit.title_eraser_width">Eraser Width:</label>
                <input id="eraser-width" type="range" min="3" value="5" step="2" max="25">
            </div>
            <button class="btn btn-danger" onclick="clearPage()" data-i18n="edit.clear_button">Clear All</button>
            <button class="btn btn-primary" onclick="saveAndSend()" data-i18n="edit.save_button">Save and Send</button>
        </div>

        <div id="pdf-container">
            <canvas id="pdf-canvas"></canvas>
            <canvas id="draw-canvas"></canvas>
        </div>

        <div id="pdf-preview" class="text-center">
            <h2 class="display-6" data-i18n="edit.preview_title">Page Previews</h2>
            <div id="thumbnail-container" class="thumbnail-grid"></div>
        </div>
        <div class="container mt-5"">
            <div class="d-flex align-items-center gap-2">
                <label for="page-number" data-i18n="edit.label_page">Page:</label>
                <input type="number" value="1" id="page-number" class="form-control">
                <button class="btn btn-outline-secondary" onclick="prevPage()" data-i18n="edit.prev_button">&lt;</button>
                <button class="btn btn-outline-secondary" onclick="nextPage()" data-i18n="edit.next_button">&gt;</button>
            </div>
        </div>
    </div>
</div>
<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- i18next -->
<script src="https://unpkg.com/i18next@23.15.1/dist/umd/i18next.min.js"></script>
<!-- custom JS with defer -->
<script defer src="../assets/js/i18n.js"></script>
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
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');

    // Funkcia na zobrazenie chybového hlásenia
    function showErrorMessage(message) {
        errorText.textContent = message;
        errorMessage.classList.add('show');
        // Automaticky skryť po 5 sekundách
        setTimeout(hideErrorMessage, 5000);
    }

    // Funkcia na skrytie chybového hlásenia
    function hideErrorMessage() {
        errorMessage.classList.remove('show');
        errorText.textContent = '';
    }

    // Funkcia na spracovanie nahraného súboru
    const handleFile = (file) => {
        if (file && file.type === 'application/pdf') {
            fileNameDisplay.textContent = file.name;
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
                }).catch(err => {
                    showErrorMessage(i18next.t('edit.error_load_pdf', { error: err.message }));
                });
            };
            reader.onerror = function() {
                showErrorMessage(i18next.t('edit.error_read_file'));
            };
            reader.readAsArrayBuffer(file);
        } else {
            fileNameDisplay.textContent = '';
            showErrorMessage(i18next.t('edit.error_invalid_file'));
        }
    };

    // Manuálny výber súboru
    document.getElementById('pdfInput').addEventListener('change', function() {
        const file = this.files[0];
        handleFile(file);
    });

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
        if (!pdfDoc || pageNum < 1 || pageNum > pdfDoc.numPages) {
            showErrorMessage(i18next.t('edit.error_invalid_page'));
            return;
        }
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
        if (!pdfDoc || pageNumber < 1 || pageNumber > pdfDoc.numPages) {
            showErrorMessage(i18next.t('edit.error_invalid_page'));
            return;
        }
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
        }).catch(err => {
            showErrorMessage(i18next.t('edit.error_load_page', { error: err.message }));
        });
    }

    function saveAndSend() {
        if (!pdfDoc) {
            showErrorMessage(i18next.t('edit.error_no_pdf'));
            return;
        }
        saveCurrentDrawing();
        const pageNum = parseInt(document.getElementById('page-number').value);
        const annotationBlob = drawCanvas.toDataURL("image/png");
        const fileInput = document.getElementById('pdfInput');
        if (!fileInput.files[0]) {
            showErrorMessage(i18next.t('edit.error_no_file'));
            return;
        }
        const formData = new FormData();
        formData.append("pdf", fileInput.files[0]);
        formData.append("annotation", dataURLtoBlob(annotationBlob));
        formData.append('apiKey', <?= json_encode($apiKey) ?> ); 
        fetch("https://node75.webte.fei.stuba.sk/api/pdf/edit", {
            method: "POST",
            body: formData
        }).then(res => {
            if (!res.ok) throw new Error('Failed to save PDF');
            return res.blob();
        }).then(blob => {
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = "edited.pdf";
            link.click();
        }).catch(err => {
            showErrorMessage(i18next.t('edit.error_save_failed', { error: err.message }));
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