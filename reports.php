<?php
require_once 'configs/auth.php';
checkAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700&display=swap');

        .scrollable-table-wrapper {
    max-height: 400px; /* You can adjust this height as needed */
    overflow-y: auto;
    border: 1px solid #ccc;
    border-radius: 6px;
}

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }
        body {
    display: flex;
    background-color: rgb(249, 244, 239);
}

.current-time {
            font-size: 16px;
            margin-right: 20px;
            color:rgb(16, 69, 205);
            background-color: white;
            padding: 12px 24px;
            border-radius: 12px;
            border: 2px solid rgb(15, 1, 97);
            font-weight: bold;
            width: 150px;                     /* Fixed width */
    text-align: center;               /* Centers the time text */
    white-space: nowrap; 
        }
        
        /* Table Styles */
        .table-container {
            margin-top: 20px;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color:rgb(8, 7, 106);
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background-color:rgb(8, 7, 106);
            color: white;
        }
        
        /* Filter Container Styles */
        .filter-container {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: rgb(8, 7, 106);
            border-radius: 8px;
            color:white
        }
        .filter-select, .export-btn, .filter-btn {
            padding: 8px 12px;
            border: 2px solid white;
            border-radius: 6px;
            background-color: white;
            color: rgb(8, 7, 106);
            font-weight: bold;
            cursor: pointer;
        }
        .export-btn, .filter-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }
        .export-btn:hover, .filter-btn:hover {
            background-color:rgb(8, 7, 106);
            color: white;
        }
        .pagination {
    text-align: center;
    margin-top: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.page-link {
    display: inline-block;
    padding: 6px 12px;
    margin: 0 3px;
    color:rgb(0, 5, 104);;
    text-decoration: none;
    border-radius: 4px;
    margin-bottom: 20px;
}

.page-link.active {
    background-color:rgb(0, 5, 104);
    color:white;
    font-weight: bold;
    border: 2px solid rgb(0, 5, 104);
}

.pagination > a.page-link:first-child,
.pagination > a.page-link:last-child {
    background-color: white;
    color: rgb(0, 5, 104);
    font-weight: bold;
}

.page-link:hover {
    background-color: white;
    color: rgb(0, 5, 104);
    border: 2px solid rgb(0, 5, 104);
    font-weight: bold;
}

    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    
    <div class="main-content">
        <div class="dashboard-header">
            <span>Reports</span>
            <div class="header-right">
                <div id="current-time" class="current-time"></div>
                <button onclick="redirectToAuthorization(event)" class="lupon-btn">
                    <?php echo htmlspecialchars($_SESSION['username']); ?> <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </div>
        
        <div class="table-container">
            <div class="filter-container" id="filterContainer">
                <label>From:</label>
                <select id="startYear" class="filter-select">
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                </select>
                <select id="startMonth" class="filter-select">
                    <option value="01">January</option>
                    <option value="02">February</option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>

                <label>To:</label>
                <select id="endYear" class="filter-select">
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                </select>   
                <select id="endMonth" class="filter-select">
                    <option value="01">January</option>
                    <option value="02">February</option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>

                <label>Nature:</label>
                <select id="natureFilter" class="filter-select">
                    <option value="">All Cases</option>
                    <option value="Criminal">Criminal</option>
                    <option value="Civil">Civil</option>
                </select>
                <label>
                    <input type="checkbox" id="allYearsCheckbox"> All Years
                </label>

                <button onclick="applyFilter()" class="filter-btn">
                    <i class="fas fa-filter"></i> Filter
                </button>
                
                <?php if (isset($_SESSION['accountType']) && $_SESSION['accountType'] === 'lupon'): ?>
                <button onclick="exportToExcel()" class="export-btn">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </button>
                <?php endif; ?>
            </div>
            <div class="scrollable-table-wrapper">
            <center><canvas id="casesChart" height="100"></canvas></center>
            <table>
                <thead>
                    <tr>
                        <th>Case ID</th>
                        <th>Complainant</th>
                        <th>Respondent</th>
                        <th>Title</th>
                        <th>Nature</th>
                        <th>Date Filed</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Rows will be dynamically inserted here -->
                </tbody>
            </table>
            </div>
            <div id="pagination"></div>


        </div>

        <script>
    let casesChart;
    let currentPage = 1;
    const itemsPerPage = 10;
    let allCases = [];

    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        document.getElementById('current-time').textContent = timeString;
    }

    setInterval(updateTime, 1000);
    updateTime();

    function redirectToAuthorization(event) {
        event.preventDefault(); 
        window.location.href = "configs/logout.php"; 
    }

    function renderChart(data) {
        const ctx = document.getElementById('casesChart').getContext('2d');

        if (casesChart) {
            casesChart.destroy();
        }

        casesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Criminal',
                        data: data.criminalCounts,
                        borderColor: '#e30505',
                        backgroundColor: '#e30505',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Civil',
                        data: data.civilCounts,
                        borderColor: '#06004c',
                        backgroundColor: '#06004c',
                        
                        fill: false,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Cases per Month by Nature',
                        color: '#333',
                        font: { size: 18 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    function applyFilter() {
        const startYear = document.getElementById('startYear').value;
        const startMonth = document.getElementById('startMonth').value;
        const endYear = document.getElementById('endYear').value;
        const endMonth = document.getElementById('endMonth').value;
        const nature = document.getElementById('natureFilter').value;
        const allYears = document.getElementById('allYearsCheckbox').checked;

        let url = `configs/fetch_filtered_cases.php?nature=${nature}&allYears=${allYears}`;
        if (!allYears) {
            url += `&startYear=${startYear}&startMonth=${startMonth}&endYear=${endYear}&endMonth=${endMonth}`;
        }

        fetch(url)
            .then(response => response.json())
            .then(cases => {
                allCases = cases;
                currentPage = 1;
                renderPaginatedTable();
                renderChartData();
            });
    }

    function renderPaginatedTable() {
    const tbody = document.querySelector('table tbody');
    tbody.innerHTML = '';

    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const paginatedCases = allCases.slice(start, end);

    if (paginatedCases.length === 0) {
        const row = document.createElement('tr');
        row.innerHTML = `<td colspan="6" style="text-align:center;">No records found.</td>`;
        tbody.appendChild(row);
        document.getElementById('pagination').innerHTML = '';
        return;
    }

    paginatedCases.forEach(case_ => {
        const row = document.createElement('tr');
        const formattedDate = new Date(case_.file_date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        row.innerHTML = `
            <td>${case_.case_no}</td>
            <td>${case_.complainants || ''}</td>
            <td>${case_.respondents || ''}</td>
            <td>${case_.title}</td>
            <td>${case_.nature}</td>
            <td>${formattedDate}</td>
        `;
        tbody.appendChild(row);
    });

    renderPaginationControls();
}


    function renderPaginationControls() {
    const totalPages = Math.ceil(allCases.length / itemsPerPage);
    const pagination = document.getElementById('pagination');
    pagination.className = 'pagination';
    pagination.innerHTML = '';

    if (totalPages <= 1) return;

    const createPageLink = (label, page, isActive = false, isDisabled = false) => {
        const a = document.createElement('a');
        a.textContent = label;
        a.href = '#';
        a.className = 'page-link' + (isActive ? ' active' : '');
        if (!isDisabled) {
            a.onclick = (e) => {
                e.preventDefault();
                currentPage = page;
                renderPaginatedTable();
            };
        } else {
            a.style.pointerEvents = 'none';
            a.style.opacity = '0.5';
        }
        return a;
    };

    pagination.appendChild(createPageLink('«', currentPage - 1, false, currentPage === 1));

    for (let i = 1; i <= totalPages; i++) {
        pagination.appendChild(createPageLink(i, i, i === currentPage));
    }

    pagination.appendChild(createPageLink(' »', currentPage + 1, false, currentPage === totalPages));
}

    function renderChartData() {
        const monthNatureMap = {};

        allCases.forEach(case_ => {
            const date = new Date(case_.file_date);
            const key = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
            const nature = case_.nature;

            if (!monthNatureMap[key]) {
                monthNatureMap[key] = { Criminal: 0, Civil: 0 };
            }

            if (nature === 'Criminal' || nature === 'Civil') {
                monthNatureMap[key][nature]++;
            }
        });

        const keys = Object.keys(monthNatureMap).sort();
        const labels = keys;
        const criminalCounts = keys.map(k => monthNatureMap[k].Criminal);
        const civilCounts = keys.map(k => monthNatureMap[k].Civil);

        renderChart({ labels, criminalCounts, civilCounts });
    }

    document.getElementById('allYearsCheckbox').addEventListener('change', function() {
        const yearSelects = document.querySelectorAll('#startYear, #endYear');
        const monthSelects = document.querySelectorAll('#startMonth, #endMonth');

        yearSelects.forEach(select => select.disabled = this.checked);
        monthSelects.forEach(select => select.disabled = this.checked);
    });


    function exportToExcel() {
    const startYear = document.getElementById('startYear').value;
    const startMonth = document.getElementById('startMonth').value;
    const endYear = document.getElementById('endYear').value;
    const endMonth = document.getElementById('endMonth').value;
    const nature = document.getElementById('natureFilter').value;
    const allYears = document.getElementById('allYearsCheckbox').checked;

    let url = `configs/export_excel.php?nature=${nature}&allYears=${allYears}`;
    
    if (!allYears) {
        url += `&startYear=${startYear}&startMonth=${startMonth}&endYear=${endYear}&endMonth=${endMonth}`;
    }

    window.location.href = url;
}

</script>

    </div>
</body>
</html>