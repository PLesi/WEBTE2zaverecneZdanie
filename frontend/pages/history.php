<?php 
    session_start();
    if (!isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] != true) {
        header("Location: login_form.php");
    } 
?>
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
<h2 data-i18n="history.title">História užívateľa</h2>

<p data-i18n="history.description">Táto stránka zobrazuje históriu užívateľa.</p>

<div class="table-container">
    <table id="historyTable" class="table">
        <thead>
        <tr>
            <th data-i18n="history.table.user">Užívateľ</th>
            <th data-i18n="history.table.operation">Operácia</th>
            <th data-i18n="history.table.time">Čas</th>
            <th data-i18n="history.table.city">Mesto</th>
            <th data-i18n="history.table.country">Štát</th>
            <th data-i18n="history.table.platform">Platforma</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div class="text-center">
    <button onclick="exportToCSV()" class="btn-primary" data-i18n="history.export">Exportovať do CSV</button>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- i18next -->
<script src="https://unpkg.com/i18next@23.15.1/dist/umd/i18next.min.js"></script>
<!-- custom JS -->
<!--<script src="../assets/js/i18n.js"></script>-->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize i18next
        const savedLanguage = localStorage.getItem('language') || 'sk';
        i18next.init({
            lng: savedLanguage,
            fallbackLng: 'en',
            resources: {
                sk: { translation: {
                        'navbar.brand': 'PDF Editor',
                        'navbar.home': 'Domov',
                        'navbar.operations': 'Operácie',
                        'navbar.history': 'História',
                        'navbar.manual': 'Príručka',
                        'navbar.profile': 'Profil',
                        'navbar.logout': 'Odhlásiť',
                        'home.title': 'Vitajte v PDF Editore',
                        'home.description': 'Jednoducho spracujte svoje PDF súbory – spájajte, editujte, mažte stránky a viac.',
                        'operations': {
                            'compress': 'Komprimovať PDF',
                            'jpg_to_pdf': 'JPG do PDF',
                            'merge': 'Spojiť PDF',
                            'rotate': 'Rotovať stránky',
                            'number': 'Číslovať stránky',
                            'protect': 'Pridať heslo',
                            'edit': 'Editovať PDF',
                            'delete_page': 'Odstrániť stránku',
                            'split': 'Rozdeliť PDF',
                            'rearrange': 'Preskupiť stránky'
                        },
                        'history.title': 'História užívateľa',
                        'history.description': 'Táto stránka zobrazuje históriu užívateľa.',
                        'history.table.user': 'Užívateľ',
                        'history.table.operation': 'Operácia',
                        'history.table.time': 'Čas',
                        'history.table.city': 'Mesto',
                        'history.table.country': 'Štát',
                        'history.table.platform': 'Platforma',
                        'history.export': 'Exportovať do CSV',
                        'history.error': 'Chyba pri načítavaní dát.'
                    }},
                en: { translation: {
                        'navbar.brand': 'PDF Editor',
                        'navbar.home': 'Home',
                        'navbar.operations': 'Operations',
                        'navbar.history': 'History',
                        'navbar.manual': 'Manual',
                        'navbar.profile': 'Profile',
                        'navbar.logout': 'Logout',
                        'home.title': 'Welcome to PDF Editor',
                        'home.description': 'Easily process your PDF files – merge, edit, delete pages, and more.',
                        'operations': {
                            'compress': 'Compress PDF',
                            'jpg_to_pdf': 'JPG to PDF',
                            'merge': 'Merge PDF',
                            'rotate': 'Rotate Pages',
                            'number': 'Number Pages',
                            'protect': 'Add Password',
                            'edit': 'Edit PDF',
                            'delete_page': 'Delete Page',
                            'split': 'Split PDF',
                            'rearrange': 'Rearrange Pages'
                        },
                        'history.title': 'User History',
                        'history.description': 'This page displays the user history.',
                        'history.table.user': 'User',
                        'history.table.operation': 'Operation',
                        'history.table.time': 'Time',
                        'history.table.city': 'City',
                        'history.table.country': 'Country',
                        'history.table.platform': 'Platform',
                        'history.export': 'Export to CSV',
                        'history.error': 'Error fetching history data.'
                    }}
            }
        }, function(err, t) {
            if (err) {
                console.error('i18next initialization failed:', err);
                return;
            }
            console.log('i18next initialized successfully');
            updateContent();
        });

        // Update content function
        function updateContent() {
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                const translation = i18next.t(key);
                if (translation) el.textContent = translation;
                else console.warn(`No translation found for key: ${key}`);
            });
        }

        // Change language function
        window.changeLanguage = function (lng) {
            i18next.changeLanguage(lng, () => {
                updateContent();
                localStorage.setItem('language', lng);
                console.log(`Language changed to: ${lng}`);
            });
        };

        // Fetch data
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
                updateContent(); // Update translations after data is loaded
            })
            .catch(error => {
                console.error('Error fetching history data:', error);
                const tableBody = document.querySelector('#historyTable tbody');
                tableBody.innerHTML = '<tr><td colspan="6" data-i18n="history.error">Chyba pri načítavaní dát.</td></tr>';
                updateContent(); // Update translations for error message
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