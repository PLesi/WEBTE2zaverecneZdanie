<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Merge PDFs</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
            max-width: 500px;
            margin: auto;
        }

        h2 {
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="file"],
        input[type="text"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        button {
            margin-top: 20px;
            padding: 10px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
        }

        #message {
            margin-top: 15px;
            color: red;
            text-align: center;
        }
    </style>
</head>

<body>

    <h2>Merge Two PDF Files</h2>

    <label for="file1">Select first PDF:</label>
    <input type="file" id="file1" accept="application/pdf" />

    <label for="file2">Select second PDF:</label>
    <input type="file" id="file2" accept="application/pdf" />



    <button id="mergeBtn">Merge PDFs</button>

    <div id="message"></div>

    <script>
        const mergeBtn = document.getElementById('mergeBtn');
        const message = document.getElementById('message');

        mergeBtn.addEventListener('click', async () => {
            message.textContent = '';
            const file1 = document.getElementById('file1').files[0];
            const file2 = document.getElementById('file2').files[0];


            if (!file1 || !file2) {
                message.textContent = 'Please select both PDF files.';
                return;
            }


            if (file1.type !== 'application/pdf' || file2.type !== 'application/pdf') {
                message.textContent = 'Both files must be PDFs.';
                return;
            }

            const formData = new FormData();
            formData.append('file1', file1);
            formData.append('file2', file2);
            formData.append('apiKey', 'asd');
            formData.append('platform', 'frontend');


            try {
                mergeBtn.disabled = true;
                mergeBtn.textContent = 'Merging...';

                const response = await fetch('http://node75.webte.fei.stuba.sk/api/pdf/merge', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    message.textContent = 'Error: ' + errorText;
                    mergeBtn.disabled = false;
                    mergeBtn.textContent = 'Merge PDFs';
                    return;
                }

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);

                // Create a temporary download link and click it
                const a = document.createElement('a');
                a.href = url;
                a.download = 'merged.pdf';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);

                mergeBtn.textContent = 'Merge PDFs';
                mergeBtn.disabled = false;
                message.style.color = 'green';
                message.textContent = 'Merged PDF downloaded successfully!';
            } catch (err) {
                message.textContent = 'Error: ' + err.message;
                mergeBtn.disabled = false;
                mergeBtn.textContent = 'Merge PDFs';
            }
        });
    </script>

</body>

</html>