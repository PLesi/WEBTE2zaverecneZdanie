<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User History</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../assets/css/styles.css" rel="stylesheet" />

    <style>
        .table-container {
            width: 95%;
            border-collapse: collapse;
            margin: auto auto 20px;
        }
        .table th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            color: #f8f9fa;
        }
        .table th {
            background-color: #595959;
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
        .table-container {
            overflow-x: auto; /* Horizontal scrollbar for small screens */
        }
        @media (max-width: 768px) {
            table {
                min-width: 600px; /* Ensure table triggers scrollbar */
            }
        }
        h2, p, button {
            text-align: center;
        }
        h2 {
            margin-top: 20px;
        }
        p {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<!-- Navigation bar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#" data-i18n="navbar.brand">PDF Editor</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../../index.php" data-i18n="navbar.home">Domov</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" data-i18n="navbar.history">História</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manual.php" data-i18n="navbar.manual">Príručka</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php" data-i18n="navbar.profile">Profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-i18n="navbar.logout">Odhlásiť</a>
                </li>
                <li class="nav-item">
                    <button class="btn btn-outline-light ms-2" onclick="changeLanguage('sk')">SK</button>
                    <button class="btn btn-outline-light ms-2" onclick="changeLanguage('en')">EN</button>
                </li>
            </ul>
        </div>
    </div>
</nav>
<h2>User History</h2>

<p>This page displays user activity history.</p>

<div class="table-container">
    <table id="historyTable" class="table">
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
        <tbody></tbody>
    </table>
</div>

<div class="text-center">
    <button onclick="exportToCSV()">Export to CSV</button>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('../../get_history_data.php')
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