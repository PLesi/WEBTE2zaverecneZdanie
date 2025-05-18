<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User History</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <h1>User History</h1>

    <p>This page displays user activity history.</p>

    <table id="historyTable">
        <thead>
            <tr>
                <th>User</th>
                <th>Operation</th>
                <th>Time</th>
                <th>City</th>
                <th>Country</th>
                <th>Platform</th>
            </tr>
        </thead>
        <tbody>
            </tbody>
    </table>

    <button onclick="exportToCSV()">Export to CSV</button>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('get_history_data.php')
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.querySelector('#historyTable tbody');
                    data.forEach(item => {
                        const row = tableBody.insertRow();
                        const userCell = row.insertCell();
                        const operationCell = row.insertCell();
                        const timeCell = row.insertCell();
                        const cityCell = row.insertCell();
                        const countryCell = row.insertCell();
                        const platformCell = row.insertCell();
                        
                        userCell.textContent = item.user;
                        operationCell.textContent = item.operation;
                        timeCell.textContent = item.time;
                        cityCell.textContent = item.city;
                        countryCell.textContent = item.country;
                        platformCell.textContent = item.platform;
                    });
                })
                .catch(error => {
                    console.error('Error fetching history data:', error);
                    const tableBody = document.querySelector('#historyTable tbody');
                    tableBody.innerHTML = '<tr><td colspan="6">Error loading history data.</td></tr>';
                });
        });

        function exportToCSV() {
            const table = document.getElementById('historyTable');
            const rows = table.querySelectorAll('tr');
            let csvContent = "data:text/csv;charset=utf-8,";
            rows.forEach(row => {
                const rowData = Array.from(row.querySelectorAll('th, td'))
                    .map(cell => cell.innerText)
                    .join(',');
                csvContent += rowData + '\r\n';
            });
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement('a');
            link.setAttribute('href', encodedUri);
            link.setAttribute('download', 'user_history.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
</html>