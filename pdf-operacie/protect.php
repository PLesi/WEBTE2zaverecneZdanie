<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
        }

        body {
            padding: 2rem;
            flex: 1;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        h1 {
            margin-bottom: 1rem;
        }

        form {
            margin-bottom: 2rem;
        }

        label {
            font-weight: bold;
        }

        input[type="file"],
        input[type="password"] {
            margin-top: 0.25rem;
            margin-bottom: 1rem;
            padding: 0.3rem;
            width: 100%;
            max-width: 300px;
            box-sizing: border-box;
        }

        button {
            padding: 0.5rem 1rem;
            cursor: pointer;
        }


        footer {
            text-align: center;
            padding: 1rem;
            background-color: #f2f2f2;
            margin-top: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Password Protect PDF</h1>
        <form id="pdfProtectForm" method="post" enctype="multipart/form-data">
            <label for="pdfInput">Upload PDF:</label><br />
            <input type="file" id="pdfInput" accept="application/pdf" required /><br />

            <label for="password">Enter Password:</label><br />
            <input type="password" id="password" required /><br />

            <button id="downloadBtn" type="submit">Download protected PDF</button>
        </form>


    </div>

    <footer>Webove technologie - Pdf editor application</footer>

    <script>
        const form = document.getElementById('pdfProtectForm');
        const pdfInput = document.getElementById('pdfInput');
        const passwordInput = document.getElementById('password');
        const downloadBtn = document.getElementById('downloadBtn');

        let protectedBlob = null;

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
            const password = passwordInput.value;

            if (!file || !password) return alert('Please upload a PDF and enter a password.');

            const formData = new FormData();
            formData.append('file', file);
            formData.append('password', password);

            try {
                const response = await fetch('http://node75.webte.fei.stuba.sk/api/pdf/protect', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) throw new Error('Password protection failed');

                protectedBlob = await response.blob();
            } catch (err) {
                alert(err.message);
            }
        });

        downloadBtn.addEventListener('click', () => {
            if (!protectedBlob) return;
            const a = document.createElement('a');
            a.href = URL.createObjectURL(protectedBlob);
            a.download = 'protected.pdf';
            a.click();
        });
    </script>
</body>

</html>